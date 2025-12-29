<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return require_ownership(1, 0, 1);
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|unique:apps,name|min:6|max:50',
            'price'   => 'required|integer|min:1|max:300000',
            'status'  => 'required|in:Active,Inactive',
        ];
    }
}
