<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Models\Profile;
use App\Models\User;
use App\Rules\UniqueIgnoringSoftDeletes;
use App\Traits\TrimsInputTrait;
use Illuminate\Foundation\Http\FormRequest;

class ProfileFormRequest extends FormRequest
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
        $user = $this->user();
        $routeParam = $this->route('profile');

        // If the route parameter is numeric, treat it as an existing model (update)
        if (is_numeric($routeParam)) {
            $model = Profile::find($routeParam);
            return $user->can(Helper::ACTION_TYPE_UPDATE, $model);
        }

        if ($routeParam instanceof Profile) {
            // If the route parameter is an instance of Profile, treat it as an existing model (update)
            return $user->can(Helper::ACTION_TYPE_UPDATE, $routeParam);
        }

        // Otherwise, this is a create request
        return $user->can(Helper::ACTION_TYPE_CREATE, Profile::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'max:50',
                new UniqueIgnoringSoftDeletes(User::class, 'username', $this->user_id)
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                new UniqueIgnoringSoftDeletes(User::class, 'email', $this->user_id)
            ],
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'nickname' => 'nullable|string|max:50',
            'type' => 'required|in:' . implode(',', Helper::ACCOUNT_TYPES),
            'user_group_id' => 'required|exists:user_groups,id',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id', // Each role ID must exist in the roles table
            'contact_numbers' => 'nullable|array',
            'contact_numbers.*' => 'nullable|string|max:20', // Each contact number should be a string with a max length
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Optional avatar image
        ];
    }
}
