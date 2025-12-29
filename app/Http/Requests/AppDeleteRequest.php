<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return require_ownership(1, 0, 1);
    }

    public function rules(): array
    {
        return [
            'edit_id' => 'required|string|min:36|max:36',
        ];
    }
}
