<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(5);

        return response()->json([
            'status' => true,
            'data' => $users
        ], Response::HTTP_OK);
    }
}
