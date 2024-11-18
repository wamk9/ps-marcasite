<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Configurations;
use App\Models\Payment;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class PaymentController extends Controller
{
    public function buyCourse()
    {
        try
        {
            $dataGetted = ['course_id' => request()->route()->parameter('course_id')];

            $validator = Validator::make($dataGetted, [
                'course_id' => 'required|exists:courses,id',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $accessToken = Configurations::where([['key', '=', 'MP_PRIVATE_TOKEN']])->first()->value ?? null;

            MercadoPagoConfig::setAccessToken($accessToken);
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL); // To test in localhost, default is SERVER

            $client = new PaymentClient();

            $request_options = new RequestOptions();
            $request_options->setCustomHeaders(["X-Idempotency-Key: " . rand(0,100000)]);

            $payment = $client->create(request()->all(), $request_options);

            $paymentStatus = '';
            $paymentStatus = $payment->status == 'approved' ? 'Pago' : $paymentStatus;
            $paymentStatus = $payment->status == 'in_process' ? 'Processando' : $paymentStatus;
            $paymentStatus = $payment->status == 'pending' ? 'Pendente' : $paymentStatus;
            $paymentStatus = $payment->status == 'rejected' ? 'Cancelado' : $paymentStatus;

            $dbPayment = Payment::create([
                'mp_id' => $payment->id,
                'status' => $payment->status,
                'status_message' => $paymentStatus,
                'user_id' => auth('api')->user()->id,
                'course_id' => $dataGetted['course_id'],
                'value_payed' => $payment->transaction_amount
            ]);

            $dbPayment->save();

            return response()->json([
                'message' => $payment//$preference->init_point
            ], 200);
        }
        catch (ValidationException $ex)
        {
            $returnMessage = response()->json(['message' => json_decode($ex->getMessage(), true)], 422);
        }
        catch (Exception $ex)
        {
            return response()->json([
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function show() {
        $returnMessage = null;

        try
        {
            $dataGetted = [
                'id' => request()->route()->parameter('id'),
            ];

            $validator = Validator::make($dataGetted, [
                'id' => 'required|exists:payments,mp_id',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $payment = Payment::where([
                ['mp_id', $dataGetted['id']],
                ['user_id', auth('api')->user()->id]
            ])->first();

            $returnMessage = response()->json(['message' => $payment], 200);
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

    public function store() {
        $returnMessage = null;

        try
        {
            $paymentStatus = '';
            $paymentStatus = request()->get('status') == 'approved' ? 'Pago' : $paymentStatus;
            $paymentStatus = request()->get('status') == 'in_process' ? 'Processando' : $paymentStatus;
            $paymentStatus = request()->get('status') == 'pending' ? 'Pendente' : $paymentStatus;
            $paymentStatus = request()->get('status') == 'rejected' ? 'Cancelado' : $paymentStatus;


            $dataGetted = array_merge(request()->only([
                'mp_id',
                'status',
                'course_id'
            ]),
            [
                'user_id' => auth('api')->user()->id,
                'status_message' => $paymentStatus
            ]);

            $validator = Validator::make($dataGetted, [
                'mp_id' => 'required|unique:payments,mp_id',
                'status' => 'nullable',
                'course_id' => 'required|exists:courses,id',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator->errors());
            }

            $payment = Payment::create($dataGetted);
            $payment->save();

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
}
