<?php

namespace App\DTOs;

class ActivityLogDTO extends BaseDTO
{
    /**
     * Create a new ActivityLogDTO instance.
     */
    public function __construct(
        public readonly ?string $module = null,
        public readonly ?string $description = null,
        public readonly ?string $status = null,
        public readonly ?string $type = null,
        public readonly ?array $properties = [],
        public readonly ?array $old_properties = [],
        public readonly ?int $processed_by = null,
        ?int $id = null,
    ) {
        parent::__construct($id);
    }

    /**
     * Set properties
     *
     * @param array $properties
     * @return self
     */
    public function withProperties(array $properties): self
    {
        $data = array_merge($this->toArray(), ['properties' => $properties]);

        return self::fromArray($data);
    }

    /**
     * Set old properties
     *
     * @param array $old_properties
     * @return self
     */
    public function withOldProperties(array $old_properties): self
    {
        $data = array_merge($this->toArray(), ['old_properties' => $old_properties]);

        return self::fromArray($data);
    }

    /**
     * Set processed_by
     *
     * @param int|null $processed_by
     * @return self
     */
    public function withProcessedBy(?int $processed_by): self
    {
        $data = array_merge($this->toArray(), ['processed_by' => $processed_by]);

        return self::fromArray($data);
    }
}
