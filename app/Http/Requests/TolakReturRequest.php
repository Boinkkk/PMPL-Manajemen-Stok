<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TolakReturRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alasan_penolakan' => ['required', 'string'],
        ];
    }
}
