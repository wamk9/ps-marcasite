<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        try
        {
            $categories = Category::select(['id AS value', 'name AS title'])->get();

            return response()->json([
                'message' => $categories
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
