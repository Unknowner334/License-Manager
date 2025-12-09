<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebuiUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return require_ownership(1, 1, 1);
    }

    public function rules(): array
    {
        return [
            'app_name' => 'required|string|max:50',
            'app_timezone' => 'required|string|in:' . implode(',', timezone_identifiers_list()),
            'currency' => 'required|string|max:10',
            'currency_place' => 'required|in:0,1,2',
            'source' => 'required|in:DISCORD,TELEGRAM,YOUTUBE',
            'source_link' => 'required|string',
        ];
    }
}
