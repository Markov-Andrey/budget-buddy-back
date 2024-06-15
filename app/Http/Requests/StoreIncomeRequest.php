<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subcategory_id' => 'required|exists:subcategories,id',
            'amount' => 'required|numeric',
            'created_at' => 'sometimes|date',
        ];
    }
}
