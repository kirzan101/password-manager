<?php

namespace App\DTOs;

abstract class AuditableDTO extends BaseDTO
{
    public function __construct(
        ?int $id = null,
        public ?int $created_by = null,
        public ?int $updated_by = null,
    ) {
        parent::__construct($id);
    }

    /**
     * Create a clone of the DTO with default audit information.
     */
    public function withDefaultAudit(int $currentUserProfileId): static
    {
        $clone = clone $this;
        $clone->created_by = $clone->created_by ?? $currentUserProfileId;
        $clone->updated_by = $clone->updated_by ?? $currentUserProfileId;
        return $clone;
    }

    /**
     * Create a clone of the DTO with updated "updated_by" information.
     */
    public function touchUpdatedBy(int $currentUserProfileId): static
    {
        $clone = clone $this;
        $clone->updated_by = $currentUserProfileId;
        return $clone;
    }
}
