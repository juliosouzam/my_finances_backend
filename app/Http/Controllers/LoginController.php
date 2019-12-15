<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 400);
        }

        $personalToken = $user->createToken(env('APP_KEY'));

        return response()->json([
            'accessToken' => $personalToken->accessToken,
            'expires_at' => $personalToken->token->expires_at
        ]);
    }

    private function validator(array $data)
    {
        return Validator::make($data, $this->rules(), $this->messages());
    }

    private function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8']
        ];
    }

    private function messages(): array
    {
        return [
            'email.required' => 'The field %s is required',
            'email.email' => 'The field %s must be valid',
            'password.required' => 'The field %s is required',
            'password.min' => 'The field %s must be 8 or more characters',
        ];
    }
}
