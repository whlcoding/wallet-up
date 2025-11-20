<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateTransactionRequest extends FormRequest
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
        $availableTypes = $this->getAvailableTypes();

        $availableStatuses = $this->getAvailableStatuses();

        return [
            'wallet_id' => 'required|integer',
            'id' => 'required|integer',
            'category_id' => 'integer|nullable',
            'name' => 'string|max:245',
            'description' => 'string|max:245|nullable',
            'type' => 'string|in:' . $availableTypes,
            'status' => 'string|in:' . $availableStatuses,
            'due_date' => 'string',
            'amount' => 'numeric',
            'is_active' => 'boolean',
            'is_recurring' => 'boolean',
            'recurring_transaction_id' => 'integer|nullable',
        ];
    }

    private function getAvailableStatuses(): string
    {
        $availableStatuses = Transaction::availableStatuses();
        return implode(',', $availableStatuses);
    }


    private function getAvailableTypes(): string
    {
        $availableTypes = Transaction::availableTypes();
        return implode(',', $availableTypes);
    }

//    protected function failedValidation(Validator $validator)
//    {
////        dd($validator->errors());
//    }
}
