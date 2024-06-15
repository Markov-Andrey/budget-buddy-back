<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subcategory_id' => 'sometimes|required|exists:subcategories,id',
            'amount' => 'sometimes|required|numeric',
            'created_at' => 'sometimes|date',
        ];
    }
}
