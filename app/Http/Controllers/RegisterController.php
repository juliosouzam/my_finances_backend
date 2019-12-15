<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $validate = $this->validator($request->all());
        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        try {
            return response()->json($this->createUser($request->all()));
        } catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }
    }

    private function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    private function validator(array $data)
    {
        return Validator::make($data, $this->rules(), $this->messages());
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ];
    }

    private function messages(): array
    {
        return [
            'name.required' => 'The field %s is required',
            'email.required' => 'The field %s is required',
            'email.email' => 'The field %s must be valid',
            'email.unique' => 'The field %s already in used',
            'password.required' => 'The field %s is required',
            'password.min' => 'The field %s must be 8 or more characters',
            'password.confirmed' => 'The field %s must be equals a password field',
        ];
    }
}
