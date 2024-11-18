<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Configurations;
use Exception;

class ConfigController extends Controller
{
    public function showPublicToken() {
        $returnMessage = null;

        try
        {
            $accessToken = Configurations::where([['key', '=', 'MP_PUBLIC_TOKEN']])->first()->value ?? null;

            $returnMessage = response()->json(['message' => $accessToken], 200);
        }
        catch (Exception $ex)
        {
            $returnMessage = response()->json(['message' => $ex->getMessage()], 500);
        }

        return $returnMessage;

    }
}
