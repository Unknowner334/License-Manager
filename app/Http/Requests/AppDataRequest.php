<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|string|max:36|min:1|exists:apps,edit_id',
        ];
    }
}
