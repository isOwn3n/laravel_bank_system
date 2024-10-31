<?php

namespace App\Rules;

use App\Models\Account;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Closure;

class CardNumberBelongsToUser implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $is_valid = Account::where('card_number', $value)
            ->where('user_id', Auth::user()->id)
            ->exists();
        if (!$is_valid)
            $fail('The selected card number is invalid.');
    }
}
