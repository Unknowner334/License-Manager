<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return require_ownership(1, 1, 1);;
    }

    public function rules(): array
    {
        return [
            'user_id'  => 'required|string|min:4|max:100|exists:users,user_id',
            'name'     => 'required|string|min:4|max:100',
            'status'   => 'required|in:Active,Inactive',
            'role'     => 'required|in:Owner,Manager,Reseller',
        ];
    }
}
