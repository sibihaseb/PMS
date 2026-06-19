<?php

namespace App\Http\Requests\Api;

use App\Enums\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'plan' => ['required', Rule::in([Plan::Pro->value, Plan::Team->value])],
        ];
    }

    public function priceId(): string
    {
        $plan = Plan::from($this->string('plan')->toString());

        return match ($plan) {
            Plan::Team => config('cashier.team_price_id'),
            Plan::Pro => config('cashier.pro_price_id'),
            default => config('cashier.pro_price_id'),
        };
    }
}
