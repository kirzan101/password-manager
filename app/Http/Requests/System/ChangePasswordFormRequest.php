<?php

namespace App\Http\Requests\System;

use App\Traits\TrimsInputTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordFormRequest extends FormRequest
{
    use TrimsInputTrait;

    /**
     * Prepare the data for validation.
     *
     * This method is called before validation occurs.
     * It allows us to modify the input data, such as trimming whitespace.
     */
    protected function prepareForValidation()
    {
        $this->merge($this->trimInputs($this->all()));
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check(); // Ensure the user is authenticated
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $this->validateCurrentPassword($attribute, $value, $fail);
                },
            ],
            'new_password' => 'required|string|min:8|max:50',
            'confirm_password' => [
                'required',
                'string',
                'min:8',
                'max:50',
                'same:new_password',
            ],
            'profile_id' => 'nullable|integer',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    protected function validateCurrentPassword($attribute, $value, $fail)
    {
        if (!Hash::check($value, Auth::user()->password)) {
            $fail('The current password is incorrect.');
        }
    }
}
