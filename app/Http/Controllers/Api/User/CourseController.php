<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Payment as DBPayment;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

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
            ->selectRaw('CONCAT(\'R$ \', courses.value) AS value')
            ->selectRaw('CONCAT(DATE_FORMAT(courses.registration_at,\'%d/%m/%Y\') , \' - \', DATE_FORMAT(courses.registration_until,\'%d/%m/%Y\')) AS registration_period')
            ->selectRaw('GREATEST(courses.vacations - SUM(CASE WHEN payments.status = "approved" THEN 1 ELSE 0 END), 0) AS vacations_to_fill')
            ->selectRaw('MAX(CASE WHEN payments.user_id = \''.auth('api')->user()->id.'\' AND payments.status = \'approved\' THEN 1 ELSE 0 END) AS bought')
            ->selectRaw('IF(\''.Carbon::now()->subDays(3)->format('Y-m-d').'\' BETWEEN courses.registration_at AND courses.registration_until, 1, 0) AS date_available')
            ->leftJoin('payments', 'courses.id', '=', 'payments.course_id')
            ->where('courses.active', '=', '1')
            ->groupBy([
                'courses.id',
                'courses.name',
                'registration_period',
                'active',
                'value',
                'courses.vacations',
                'date_available'
            ]);


            if ($dataGetted['filter'] != null) {
                $courses = $courses->Where('courses.name', 'LIKE', '%' . $dataGetted['filter'] . '%')
                ->orWhere('courses.value', 'LIKE', '%' . $dataGetted['filter'] . '%')
                ->orWhere('courses.registration_at', 'LIKE', '%' . $dataGetted['filter'] . '%')
                ->orWhere('courses.registration_until', 'LIKE', '%' . $dataGetted['filter'] . '%');
            }

            $courses = $courses->paginate(2, ['*'], 'page', request()->get('page'));


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

    public function indexMyCourse()
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


            $courses = DBPayment::select([
                'courses.id',
                'courses.name',
                'payments.status',
                'payments.status_message'
            ])
            ->selectRaw('DATE_FORMAT(payments.created_at,\'%d/%m/%Y\') AS registered_at')
            ->selectRaw('CONCAT(\'R$\', payments.value_payed) AS value_payed')
            ->leftJoin('courses', 'courses.id', '=', 'payments.course_id')
            ->where('payments.user_id', '=', auth('api')->user()->id);


            if ($dataGetted['filter'] != null) {
                $courses = $courses->Where('courses.name', 'LIKE', '%' . $dataGetted['filter'] . '%')
                ->orWhere('payments.value_payed', 'LIKE', '%' . $dataGetted['filter'] . '%')
                ->orWhere('payments.status_message', 'LIKE', '%' . $dataGetted['filter'] . '%')
                ->orWhere('registration_at', 'LIKE', '%' . $dataGetted['filter'] . '%');
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

            $course = Course::select([
                'courses.id',
                'courses.name',
                'courses.value',
                'courses.description'
            ])
            ->selectRaw('MAX(CASE WHEN payments.user_id = \''.auth('api')->user()->id.'\' AND payments.status = \'approved\' THEN 1 ELSE 0 END) AS bought')
            ->selectRaw('IF(\''.Carbon::now()->subDays(3)->format('Y-m-d').'\' BETWEEN courses.registration_at AND courses.registration_until, 1, 0) AS date_available')
            ->leftJoin('payments', 'courses.id', '=', 'payments.course_id')
            ->where('courses.id', $dataGetted['id'])
            ->groupBy([
                'courses.id',
                'courses.name',
                'courses.value',
                'courses.description',
                'date_available'
            ])
            ->first();



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

                $course['files'] = $files;
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
}
