<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return require_ownership(1, 1, 1);
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|min:4|max:100',
            'username' => 'required|string|min:4|max:50|unique:users,username',
            'password' => 'required|string|confirmed|min:8|max:50',
            'status'   => 'required|in:Active,Inactive',
            'role'     => 'required|in:Owner,Manager,Reseller',
        ];
    }
}
