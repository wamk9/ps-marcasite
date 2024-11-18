<?php

namespace App\Jobs;

use App\Models\Configurations;
use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class UpdatePaymentsStatus implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $payments = Payment::where(['status', '!=', 'approved'])->get();
        $accessToken = Configurations::where([['key', '=', 'MP_PRIVATE_TOKEN']])->first()->value ?? null;

        MercadoPagoConfig::setAccessToken($accessToken);
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL); // To test in localhost, default is SERVER

        $request_options = new RequestOptions();
        $request_options->setCustomHeaders(["X-Idempotency-Key: " . rand(0,100000)]);

        $client = new PaymentClient();

        foreach ($payments as $payment)
        {
            $paymentMP = $client->get($payment->mp_id, $request_options);

            if($paymentMP->status != $payment->status) {
                $paymentStatus = '';
                $paymentStatus = $payment->status == 'approved' ? 'Pago' : $paymentStatus;
                $paymentStatus = $payment->status == 'in_process' ? 'Processando' : $paymentStatus;
                $paymentStatus = $payment->status == 'pending' ? 'Pendente' : $paymentStatus;
                $paymentStatus = $payment->status == 'rejected' ? 'Cancelado' : $paymentStatus;

                $payment->status = $paymentMP->status;
                $payment->status_message = $paymentStatus;
            }
        }
    }
}
