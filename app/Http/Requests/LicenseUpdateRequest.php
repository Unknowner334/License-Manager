<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LicenseUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'edit_id'  => 'required|string|min:36|max:36|exists:licenses,edit_id',
            'license'  => 'max:50',
            'app'      => 'required|string|exists:apps,app_id|min:36|max:36',
            'owner'    => 'max:50',
            'duration' => 'required|integer',
            'status'   => 'required|in:Active,Inactive',
            'devices'  => 'required|integer|min:1|max:1000000',
        ];
    }
}
