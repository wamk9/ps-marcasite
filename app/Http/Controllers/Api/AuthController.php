<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login()
    {
        $returnMessage = null;

        try
        {
            $credentials = request(['email', 'password']);

            if (!$token = JWTAuth::attempt($credentials)) {
                throw new UnauthorizedException('Unauthorized');
            }

            $returnMessage = $this->respondWithToken($token);
        }
        catch (UnauthorizedException $ex)
        {
            $returnMessage = response()->json(['message' => $ex->getMessage()], 401);
        }
        catch (Exception $ex)
        {
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }
        finally
        {
            return $returnMessage;
        }
    }

    public function logout()
    {
        $returnMessage = null;

        try
        {
            if (empty(request()->bearerToken()))
            {
                throw new UnauthorizedException('Unauthorized');
            }

            //auth('api')->logout();
            JWTAuth::unsetToken(); // Unset used token, but it's better review this if have time to another applications...
            $returnMessage = response()->json(['message' => 'Successfully logged out'], 200);
        }
        catch (UnauthorizedException $ex)
        {
            $returnMessage = response()->json(['message' => $ex->getMessage()], 401);
        }
        catch (Exception $ex)
        {
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }
        finally
        {
            return $returnMessage;
        }
    }

    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh(JWTAuth::getToken()));
    }

    public function confirmEmailVerification()
    {
        $returnMessage = null;

        try
        {
            $dataGetted = request()->only([
                'email_code'
            ]);

            $validator = Validator::make($dataGetted, [
                'email_code' => 'required|integer|between:100000,999999',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            if (auth('api')->user() == null)
            {
                throw new UnauthorizedException('Unauthorized');
            }

            if (auth('api')->user()->hasVerifiedEmail())
            {
                throw new ValidationException(['email' => 'Email already verified']);
            }

            if (auth('api')->user()->email_code != $dataGetted['email_code'])
            {
                auth('api')->user()->update(['email_code' =>  null]);
                auth('api')->user()->markEmailAsVerified();
            }
            else
            {
                throw new ValidationException(['email' => 'Wrong email code']);
            }

            $returnMessage = response()->json(['message' => 'Successfully email verification'], 200);
        }
        catch (UnauthorizedException $ex)
        {
            $returnMessage = response()->json(['message' => $ex->getMessage()], 401);
        }
        catch (ValidationException $ex)
        {
            $returnMessage = response()->json(['message' => json_decode($ex->getMessage(), true)], 422);
        }
        catch (Exception $ex)
        {
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }
        finally
        {
            return $returnMessage;
        }
    }

    public function sendEmailVerification()
    {
        $returnMessage = null;

        try
        {
            if (auth('api')->user() == null)
            {
                throw new UnauthorizedException('Unauthorized');
            }

            if (auth('api')->user()->hasVerifiedEmail())
            {
                throw new ValidationException(['email' => 'Email already verified']);
            }

            $email_code = rand(100000, 999999);
            auth('api')->user()->update(['email_code' =>  $email_code]);
            Mail::to(auth('api')->user()->email)->send(new VerifyEmail($email_code));

            $returnMessage = response()->json(['message' => 'Verification email sent'], 200);
        }
        catch (UnauthorizedException $ex)
        {
            $returnMessage = response()->json(['message' => $ex->getMessage()], 401);
        }
        catch (ValidationException $ex)
        {
            $returnMessage = response()->json(['message' => json_decode($ex->getMessage(), true)], 422);
        }
        catch (Exception $ex)
        {
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }
        finally
        {
            return $returnMessage;
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'data' =>
            [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'role' => auth('api')->user()->role->name,
                'user_name' => auth('api')->user()->name,
                'user_id' => auth('api')->user()->id,
            ]
        ]);
    }
}
