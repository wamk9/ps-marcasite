<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\PaymentExport;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Barryvdh\DomPDF\Facade\Pdf;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Uid\Ulid;

class CourseController extends Controller
{
    public function index()
    {
        try
        {
            $dataGetted = [
                'page' => request()->route()->parameter('page'),
                'filter' => request()->get('filter')
            ];

            $validator = Validator::make($dataGetted, [
                'page' => 'nullable|number',
                'filter' => 'nullable|string',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $courses = Course::select([
                'courses.id',
                'courses.name'
            ])
            ->selectRaw('IF(courses.active, \'Ativo\', \'Inativo\') AS active')
            ->selectRaw('CONCAT(\'R$ \', SUM(CASE WHEN payments.status = "approved" THEN payments.value_payed ELSE 0 END)) AS value')
            ->selectRaw('CONCAT(DATE_FORMAT(courses.registration_at,\'%d/%m/%Y\') , \' - \', DATE_FORMAT(courses.registration_until,\'%d/%m/%Y\')) AS registration_period')
            ->selectRaw('GREATEST(courses.vacations - SUM(CASE WHEN payments.status = "approved" THEN 1 ELSE 0 END), 0) AS vacations_to_fill')
            ->selectRaw('MAX(CASE WHEN payments.user_id = \''.auth('api')->user()->id.'\' AND payments.status = \'approved\' THEN 1 ELSE 0 END) AS bought')
            ->leftJoin('payments', 'courses.id', '=', 'payments.course_id')
            ->groupBy([
                'courses.id',
                'courses.name',
                'registration_period',
                'active',
                'courses.vacations'
            ]);

            if ($dataGetted['filter'] != null) {
                $courses = $courses->Where('courses.name', 'LIKE', '%' . $dataGetted['filter'] . '%')
                ->orWhere('courses.value', 'LIKE', '%' . $dataGetted['filter'] . '%')
                ->orWhere('courses.registration_at', 'LIKE', '%' . $dataGetted['filter'] . '%')
                ->orWhere('courses.registration_until', 'LIKE', '%' . $dataGetted['filter'] . '%');
            }

            $courses = $courses->paginate(4, ['*'], 'page', request()->get('page'));


            return response()->json([
                'message' => $courses
            ], 200);
        }
        catch (Exception $ex)
        {
            return response()->json([
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function delete()
    {
        $returnMessage = null;

        try
        {
            $dataGetted = [
                'id' => request()->route()->parameter('id'),
            ];

            $validator = Validator::make($dataGetted, [
                'id' => 'required|exists:courses,id',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $course = Course::where([
                ['id', $dataGetted['id']],
            ])->first();

            $path = storage_path('app/public/course/'.$course->id);

            if(File::isDirectory($path))
            {
                File::deleteDirectory($path);
            }

            $course->delete();

            $returnMessage = response()->json(['message' => 'success'], 200);
        }
        catch (ValidationException $ex)
        {
            $returnMessage = response()->json(['message' => json_decode($ex->getMessage(), true)], 422);
        }
        catch (Exception $ex)
        {
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }

        return $returnMessage;
    }

    public function show()
    {
        $returnMessage = null;

        try
        {
            $dataGetted = [
                'id' => request()->route()->parameter('id'),
            ];

            $validator = Validator::make($dataGetted, [
                'id' => 'required|exists:courses,id',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $course = Course::where([
                ['id', $dataGetted['id']],
            ])->first();

            $path = storage_path('app/public/course/'.$course->id);

            if(File::isDirectory($path)) {
                $files = collect(File::files($path))
                    ->filter(function ($file) {
                        return $file->getFilename() !== 'thumb.webp';
                    })
                    ->map(function ($file) {
                        return $file->getFilename();
                    })
                    ->values();

                $courseFiles = [];

                foreach ($files as $filename) {
                    $courseFiles[] = [
                        'name' => $filename,
                        'new_file' => false
                    ];
                }

                $course['files'] = $courseFiles;
            }


            $returnMessage = response()->json(['message' => $course], 200);
        }
        catch (ValidationException $ex)
        {
            $returnMessage = response()->json(['message' => json_decode($ex->getMessage(), true)], 422);
        }
        catch (Exception $ex)
        {
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }

        return $returnMessage;
    }

    public function store()
    {
        $returnMessage = null;

        try
        {
            $dataGetted = request()->only([
                'name',
                'category_id',
                'value',
                'vacations',
                'registration_at',
                'registration_until',
                'description',
                'active',
            ]);

            $validator = Validator::make($dataGetted, [
                'name' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'value' => 'required|decimal:2',
                'vacations' => 'required|integer',
                'registration_at' => 'required|date',
                'registration_until' => 'required|date|after:registration_at',
                'description' => 'required|string',
                'active' => 'required|boolean'
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            DB::beginTransaction();

            $course = Course::create($dataGetted);
            $course->save();

            if (request()->filled('thumb_image'))
            {
                $path = storage_path('app/public/course/'.$course->id);

                if(!File::isDirectory($path))
                {
                    File::makeDirectory($path, 0755, true, true);
                }

                Image::read(request()->only('thumb_image')['thumb_image'])->scale(250, 250)->toWebp(90)->save($path.'/thumb.webp');
            }

            if (request()->filled('files'))
            {
                $files = request()->only('files')['files'];
                $path = storage_path('app/public/course/'.$course->id);

                if(!File::isDirectory($path))
                {
                    File::makeDirectory($path, 0755, true, true);
                }

                foreach ($files as $file)
                {
                    if (preg_match('/^data:(.*);base64,/', $file['data'], $matches)) {
                        $mimeType = $matches[1]; // Get the MIME type
                        $base64String = substr($file['data'], strpos($file['data'], ',') + 1); // Remove the prefix

                        // Decode the base64 string
                        $decodedFile = base64_decode($base64String);
                        $fullPath = $path . '/' . Ulid::generate() . '_' . str_replace(' ', '-', $file['name']);
                        File::put($fullPath, $decodedFile);
                    }
                    else
                    {
                        $decodedFile = base64_decode($file['data']);
                        $fullPath = $path . '/' . Ulid::generate() . '_' . str_replace(' ', '-', $file['name']);
                        File::put($fullPath, $decodedFile);
                    }                }
            }

            DB::commit();

            $returnMessage = response()->json(['message' => 'success'], 200);
        }
        catch (ValidationException $ex)
        {
            DB::rollBack();
            $returnMessage = response()->json(['message' => json_decode($ex->getMessage(), true)], 422);
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }

        return $returnMessage;
    }

    public function update()
    {
        $returnMessage = null;

        try
        {
            $dataGetted = request()->only([
                'id',
                'name',
                'category_id',
                'value',
                'vacations',
                'registration_at',
                'registration_until',
                'description',
                'active',
            ]);

            $validator = Validator::make($dataGetted, [
                'id' => 'required|exists:courses,id',
                'name' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'value' => 'required|decimal:2',
                'vacations' => 'required|integer',
                'registration_at' => 'required|date',
                'registration_until' => 'required|date|after:registration_at',
                'description' => 'required|string',
                'active' => 'required|boolean'
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            DB::beginTransaction();

            $course = Course::where([['id', $dataGetted['id']]])->first();
            $course->update($dataGetted);


            if (request()->filled('thumb_image'))
            {
                $path = storage_path('app/public/course/'. $course->id);

                if(!File::isDirectory($path))
                File::makeDirectory($path, 0755, true, true);

                Image::read(request()->only('thumb_image')['thumb_image'])->scale(250, 250)->toWebp(90)->save($path.'/thumb.webp');
            }

            if (request()->filled('files'))
            {
                $files = request()->only('files')['files'];
                $path = storage_path('app/public/course/'.$course->id);

                if(!File::isDirectory($path))
                {
                    File::makeDirectory($path, 0755, true, true);
                }

                foreach ($files as $file)
                {
                    if (isset($file['remove']) && $file['remove'])
                    {
                        $fullPath = $path . '/' . $file['name'];

                        if (File::exists($fullPath))
                        {
                            File::delete($fullPath);
                        }
                    }
                    else if ($file['new_file'])
                    {
                        if (preg_match('/^data:(.*);base64,/', $file['data'], $matches)) {
                            $mimeType = $matches[1]; // Get the MIME type
                            $base64String = substr($file['data'], strpos($file['data'], ',') + 1); // Remove the prefix

                            // Decode the base64 string
                            $decodedFile = base64_decode($base64String);
                            $fullPath = $path . '/' . Ulid::generate() . '_' . str_replace(' ', '-', $file['name']);
                            File::put($fullPath, $decodedFile);
                        }
                        else
                        {
                            $decodedFile = base64_decode($file['data']);
                            $fullPath = $path . '/' . Ulid::generate() . '_' . str_replace(' ', '-', $file['name']);
                            File::put($fullPath, $decodedFile);
                        }
                    }
                }
            }

            DB::commit();

            $returnMessage = response()->json(['message' => 'success'], 200);
        }
        catch (ValidationException $ex)
        {
            DB::rollBack();
            $returnMessage = response()->json(['message' => json_decode($ex->getMessage(), true)], 422);
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }

        return $returnMessage;
    }

    public function showRegistered()
    {
        $returnMessage = null;

        try
        {
            $dataGetted = [
                'id' => request()->route()->parameter('id'),
            ];

            $validator = Validator::make($dataGetted, [
                'id' => 'required|exists:courses,id',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $course = Course::select(['payments.id AS register_num', 'users.name'])
                ->leftJoin('payments', 'courses.id', '=', 'payments.course_id')
                ->leftJoin('users', 'users.id', '=', 'payments.user_id')
                ->where([['courses.id', $dataGetted['id']],['payments.status', 'approved']])->get();

            $returnMessage = response()->json(['message' => $course], 200);
        }
        catch (ValidationException $ex)
        {
            DB::rollBack();
            $returnMessage = response()->json(['message' => json_decode($ex->getMessage(), true)], 422);
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }

        return $returnMessage;
    }

    public function exportToExcel()
    {
        $returnMessage = null;

        try
        {
            $dataGetted = [
                'id' => request()->route()->parameter('id'),
            ];

            $validator = Validator::make($dataGetted, [
                'id' => 'required|exists:courses,id',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $export = new PaymentExport($dataGetted['id']);
            $hashCode = Ulid::generate();
            $url = 'export/'.$dataGetted['id'].'/'.$hashCode.'.xlsx';
            $path = storage_path('app/public/export/course/'.$dataGetted['id']);

            if(!File::isDirectory($path))
            {
                File::makeDirectory($path, 0755, true, true);
            }

            Excel::store($export, 'export/course/'.$dataGetted['id'].'/'.$hashCode.'.xlsx', 'public', \Maatwebsite\Excel\Excel::XLSX, [
                'visibility' => 'public',
            ]);

            $returnMessage = response()->json(['message' => $url], 200);
        }
        catch (ValidationException $ex)
        {
            DB::rollBack();
            $returnMessage = response()->json(['message' => json_decode($ex->getMessage(), true)], 422);
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }

        return $returnMessage;
    }

    public function exportToPdf()
    {
        $returnMessage = null;

        try
        {
            $dataGetted = [
                'id' => request()->route()->parameter('id'),
            ];

            $validator = Validator::make($dataGetted, [
                'id' => 'required|exists:courses,id',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $course = Course::where([['courses.id', $dataGetted['id']]])->first();

            $students = Course::select(['payments.id AS register_num', 'users.name AS student_name'])
            ->leftJoin('payments', 'courses.id', '=', 'payments.course_id')
            ->leftJoin('users', 'users.id', '=', 'payments.user_id')
            ->where([['courses.id', $dataGetted['id']],['payments.status', 'approved']])->get();


            $hashCode = Ulid::generate();
            $url = 'export/'.$dataGetted['id'].'/'.$hashCode.'.pdf';
            $path = storage_path('app/public/export/course/'.$dataGetted['id']);

            if(!File::isDirectory($path))
            {
                File::makeDirectory($path, 0755, true, true);
            }

            $data = [
                'title' => 'Inscritos no curso \''.$course->name.'\'',
                'date' => date('m/d/Y'),
                'items' => $students->toArray()
            ];


            $pdf = Pdf::loadView('pdf.export', $data);
            File::put($path.'/'.$hashCode.'.pdf', $pdf->output());

            $returnMessage = response()->json(['message' => $url], 200);
        }
        catch (ValidationException $ex)
        {
            DB::rollBack();
            $returnMessage = response()->json(['message' => json_decode($ex->getMessage(), true)], 422);
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }

        return $returnMessage;
    }
}
