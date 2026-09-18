<?php

namespace App\Http\Resources;

use App\Traits\ReturnDatetimeFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGroupResource extends JsonResource
{
    use ReturnDatetimeFormat;

    /**
     * Define relationships this resource may need.
     */
    public static array $relations = ['createdBy', 'updatedBy'];

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
            'code' => $this->code,
            'description' => $this->description,
            'createdBy' => $this->created_by ? $this->createdBy->getFullName() : null,
            'updatedBy' => $this->updated_by ? $this->updatedBy->getFullName() : null,
            'created_at' => $this->returnShortDateTime($this->created_at),
            'updated_at' => $this->returnShortDateTime($this->updated_at)
        ];
    }
}
