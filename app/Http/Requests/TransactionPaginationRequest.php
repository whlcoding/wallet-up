<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class TransactionPaginationRequest extends FormRequest
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
            'limit'=> 'required|integer|max:100',
            'offset'=> 'required|integer|max:100',
            'type'=> 'required|string|in:' . implode(',', Transaction::availableTypes()),
            'status'=> 'required|string|in:' . implode(',', Transaction::availableStatuses()),
        ];
    }
}
