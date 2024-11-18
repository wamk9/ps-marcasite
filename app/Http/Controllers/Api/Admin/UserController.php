<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
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


            $users = User::select(['users.id', 'users.name', 'roles.friendly_name as role_name'])
                ->selectRaw('IF(users.email_verified_at IS NOT NULL, \'Ativo\', \'Inativo\') AS activated')
                ->leftJoin('roles', 'roles.id', '=', 'users.role_id');

            if ($dataGetted['filter'] != null) {
                $users = $users->Where('roles.friendly_name', 'LIKE', '%' . $dataGetted['filter'] . '%')
                ->orWhere('users.name', 'LIKE', '%' . $dataGetted['filter'] . '%');
            }

            $users = $users->paginate(4, ['*'], 'page', request()->get('page'));


            return response()->json([
                'message' => $users
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
                'id' => 'required|exists:users,id',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $user = User::where([
                ['id', $dataGetted['id']],
            ])->first();

            $user->delete();

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
                'id' => 'required|exists:users,id',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $user = User::where([
                ['id', $dataGetted['id']],
            ])->first();

            $returnMessage = response()->json(['message' => $user], 200);
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
                'role_id',
                'email',
                'password',
                'password_confirmation',
                'send_confirmation',
                'profile_image'
            ]);

            $validator = Validator::make($dataGetted, [
                'name' => 'required|string',
                'role_id' => 'required|exists:roles,id',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed',
                'send_confirmation' => 'required|boolean'
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            DB::beginTransaction();

            $user = User::create($dataGetted);
            $user->save();

            if (request()->filled('profile_image'))
            {
                $path = storage_path('app/public/profile');

                if(!File::isDirectory($path))
                File::makeDirectory($path, 0755, true, true);

                Image::read(request()->only('profile_image')['profile_image'])->scale(250, 250)->toWebp(90)->save($path.'/' . $user->id . '.webp');
            }

            if (request()->get('send_confirmation')) {
                event(new Registered($user));
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
                'role_id',
                'email',
                'password',
                'password_confirmation',
                'send_confirmation',
                'profile_image'
            ]);

            $validator = Validator::make($dataGetted, [
                'id' => 'required|exists:users,id',
                'name' => 'required|string',
                'role_id' => 'required|exists:roles,id',
                'email' => 'required|email|unique:users,email,'. request()->get('id') .',id',
                'password' => 'sometimes|confirmed',
                'send_confirmation' => 'required|boolean'
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $user = User::where([
                ['id', $dataGetted['id']],
            ])->first();

            if (request()->filled('profile_image'))
            {
                $path = storage_path('app/public/user/'. $user->id);

                if(!File::isDirectory($path))
                File::makeDirectory($path, 0755, true, true);

                Image::read(request()->only('profile_image')['profile_image'])->scale(250, 250)->toWebp(90)->save($path.'/profile.webp');
            }

            if (request()->get('send_confirmation') && !$user->hasVerifiedEmail()) {
                $email_code = rand(100000, 999999);
                $user->update(['email_code' =>  $email_code]);
                Mail::to($user->email)->send(new VerifyEmail($email_code));
            }


            unset($dataGetted['send_confirmation']);
            unset($dataGetted['profile_image']);
            $user->update($dataGetted);

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
}
