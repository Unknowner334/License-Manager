<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LicenseGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'app'      => 'required|string|exists:apps,app_id|min:6|max:36',
            'owner'    => 'max:50',
            'duration' => 'required|integer',
            'status'   => 'required|in:Active,Inactive',
            'devices'  => 'required|integer|min:1|max:1000000',
        ];
    }
}
