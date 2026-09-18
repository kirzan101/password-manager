<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Models\Module;
use App\Models\User;
use App\Rules\UniqueIgnoringSoftDeletes;
use App\Traits\TrimsInputTrait;
use Illuminate\Foundation\Http\FormRequest;

class ModuleFormRequest extends FormRequest
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
        $routeParam = $this->route('modules');

        // If the route parameter is numeric, treat it as an existing model (update)
        if (is_numeric($routeParam)) {
            $model = Module::find($routeParam);
            return $user->can(Helper::ACTION_TYPE_UPDATE, $model);
        }

        if ($routeParam instanceof Module) {
            // If the route parameter is an instance of Module, treat it as an existing model (update)
            return $user->can(Helper::ACTION_TYPE_UPDATE, $routeParam);
        }

        // Otherwise, this is a create request
        return $user->can(Helper::ACTION_TYPE_CREATE, Module::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:modules,name,id' . $this->id, // Unique rule that ignores the current record's ID for updates
            ],
            'icon' => ['nullable', 'string', 'max:255'],
            'route' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'order' => ['required', 'integer'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
