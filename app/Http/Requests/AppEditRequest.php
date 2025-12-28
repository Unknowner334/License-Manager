<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return require_ownership(1, 1, 1);
    }

    public function rules(): array
    {
        return [
            'edit_id' => 'required|string|min:36|max:36',
            'price'   => 'required|integer|min:250|max:300000',
            'status'  => 'required|in:Active,Inactive',
        ];
    }
}
