<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'q'          => ['nullable','string','max:200'],
            'category'   => ['nullable','string','max:100'],
            'page'       => ['nullable','integer','min:1'],
            'per_page'   => ['nullable','integer','in:6,9,12,18,24'],
            // meta filters: terima apapun (string/satu nilai)
        ];
    }

    public function authorize(): bool { return true; }
}
