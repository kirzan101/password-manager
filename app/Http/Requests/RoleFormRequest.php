<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Models\Role;
use App\Models\User;
use App\Rules\UniqueIgnoringSoftDeletes;
use App\Traits\TrimsInputTrait;
use Illuminate\Foundation\Http\FormRequest;

class RoleFormRequest extends FormRequest
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
        $routeParam = $this->route('roles');

        // If the route parameter is numeric, treat it as an existing model (update)
        if (is_numeric($routeParam)) {
            $model = Role::find($routeParam);
            return $user->can(Helper::ACTION_TYPE_UPDATE, $model);
        }

        if ($routeParam instanceof Role) {
            // If the route parameter is an instance of Role, treat it as an existing model (update)
            return $user->can(Helper::ACTION_TYPE_UPDATE, $routeParam);
        }

        // Otherwise, this is a create request
        return $user->can(Helper::ACTION_TYPE_CREATE, Role::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new UniqueIgnoringSoftDeletes(Role::class, 'name', $this->id)],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'permissionIds' => ['required', 'array'],
            'permissionIds.*' => ['integer', 'exists:permissions,id'],
        ];
    }
}
