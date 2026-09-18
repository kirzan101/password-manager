<?php

namespace App\Http\Resources\IndexResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogIndexResource extends JsonResource
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
            'module' => $this->module,
            'description' => $this->description,
            'status' => $this->status,
            'type' => $this->type,
            'properties' => $this->properties,
            'old_properties' => $this->old_properties,
        ];
    }
}
