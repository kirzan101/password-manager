<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'route' => $this->route,
            'category' => $this->category,
            'order' => $this->order,
            'base_name' => $this->base_name,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
