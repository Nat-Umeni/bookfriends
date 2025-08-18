<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFriendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'bail',
                'required',
                'email',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($this->user() && $value === $this->user()->email) {
                        $fail('You cannot add yourself.');
                    }
                },
                Rule::exists('users', 'email'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Please enter an email address.',
            'email.email' => 'Enter a valid email address.',
            'email.exists' => "We couldn't find a user with that email.",
        ];
    }
}
