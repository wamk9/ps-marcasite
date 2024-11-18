<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Exception;

class RoleController extends Controller
{
    public function index()
    {
        try
        {
            $roles = Role::select(['id AS value', 'friendly_name AS title'])->get();

            return response()->json([
                'message' => $roles
            ], 200);
        }
        catch (Exception $ex)
        {
            return response()->json([
                'message' => $ex->getMessage()
            ], 500);
        }
    }
}
