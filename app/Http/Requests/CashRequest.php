<?php

namespace App\Http\Requests;

use App\Rules\CardNumberBelongsToUser;
use Illuminate\Foundation\Http\FormRequest;

class CashRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'card_number' => ['required', 'integer', 'exists:accounts,card_number', new CardNumberBelongsToUser()],
            'amount' => ['required', 'integer', 'min:1000', 'max:50000000']
        ];
    }
}
