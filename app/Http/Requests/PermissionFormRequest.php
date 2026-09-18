<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Models\Permission;
use App\Models\User;
use App\Rules\UniqueIgnoringSoftDeletes;
use Illuminate\Validation\Rule;
use App\Traits\TrimsInputTrait;
use Illuminate\Foundation\Http\FormRequest;

class PermissionFormRequest extends FormRequest
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
        $routeParam = $this->route('permissions');

        // If the route parameter is numeric, treat it as an existing model (update)
        if (is_numeric($routeParam)) {
            $model = Permission::find($routeParam);
            return $user->can(Helper::ACTION_TYPE_UPDATE, $model);
        }

        if ($routeParam instanceof Permission) {
            // If the route parameter is an instance of Permission, treat it as an existing model (update)
            return $user->can(Helper::ACTION_TYPE_UPDATE, $routeParam);
        }

        // Otherwise, this is a create request
        return $user->can(Helper::ACTION_TYPE_CREATE, Permission::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'module' => ['required', 'string', 'max:255'],
            'type' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'type')
                    ->where(fn($query) => $query->where('module', $this->input('module')))
                    ->ignore($this->id),
            ],
        ];
    }
}
