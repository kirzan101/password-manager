<?php

namespace App\Http\Resources;

use App\Traits\ReturnDatetimeFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    use ReturnDatetimeFormat;

    /**
     * Define relationships this resource may need.
     */
    public static array $relations = ['processedBy'];

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
            'processed_by' => $this->processed_by,
            'processed_by_avatar' => $this->processedBy?->avatar ? route('uploads.show', ['path' => $this->processedBy->avatar]) : null,
            'processed_by_initials' => $this->processedBy?->getInitials(),
            'processed_by_name' => $this->processedByName(),
            'created_at' => $this->returnShortDateTime($this->created_at),
            'updated_at' => $this->returnShortDateTime($this->updated_at),
        ];
    }

    /**
     * Get the name of the user who processed the activity.
     */
    private function processedByName(): ?string
    {
        return $this->processedBy?->getName() ?? '-';
    }
}
