<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_id' => 'required|integer',
            'name' => 'required|string|max:245',
            'description' => 'required|string|max:245',
            'amount' => 'required|numeric',
            'type' => 'required|string|in:expense,income',
            'due_date' => 'required|string',
            'status' => 'required|string|in:pending,paid,overdue,voided',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
//        dd($validator->errors());
    }
}
