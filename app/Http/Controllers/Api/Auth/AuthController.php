<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        # code...
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'username' => ['required', 'unique:users'],
            'phone' => ['required', 'unique:users'],
            'dob' => ['required', 'date'],
            'gender' => ['nullable'],
            'password' => ['required', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $credentials = $request->except(['password_confirmation']);
        $dob = explode('/', $credentials['dob']);
        $credentials['dob'] = Carbon::createFromDate($dob[2], $dob[1], $dob[0]);
        $credentials['password'] = bcrypt($credentials['password']);

        $user = User::create($credentials);
        if (!$user) {
            return response()->json(['message' => 'Unable to register, try again later.'], 500);
        }

        return response()->json(['message' => 'Register success, you can login now.'], 200);
    }
}
