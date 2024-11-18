<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configurations;
use Exception;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        try
        {
            $dbConfigs = Configurations::select(['key', 'value'])->get();

            $configsToSend = [];

            foreach($dbConfigs as $config)
            {
                $configsToSend[$config->key] = $config->value;
            }

            return response()->json([
                'message' => $configsToSend
            ], 200);
        }
        catch (Exception $ex)
        {
            return response()->json([
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function update()
    {

    }
}
