<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $credentials = $request->only('username', 'password');
        if (filter_var($credentials['username'], FILTER_VALIDATE_EMAIL)) {
            Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']]);
        } else {
            Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']]);
        }

        if (!Auth::check()) {
            return response()->json(['message' => 'Username / Password tidak benar.'], 400);
        }

        $user = User::find(Auth::user()->id);
        $user['access_token'] = $user->createToken('login')->accessToken;

        return response()->json($user, 200);
    }

    public function register(Request $request)
    {
        $dob = explode('/', $request->dob);
        $request['dob'] = Carbon::createFromDate($dob[2], $dob[1], $dob[0]);

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
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        if (!is_null($request['gender'])) {
            switch (substr($request['gender'], 0, 1)) {
                case 'L':
                    $gender = 'M';
                    break;
                case 'P':
                    $gender = 'F';
                    break;

                default:
                    $gender = 'U';
                    break;
            }
            $request['gender'] = $gender;
        }
        $request['password'] = bcrypt($request['password']);

        $user = User::create($request->except('password_confirmation'));
        if (!$user) {
            return response()->json(['message' => 'Unable to register, try again later.'], 500);
        }

        $user['message'] = 'Register success, you can login now.';

        return response()->json($user, 200);
    }
}
