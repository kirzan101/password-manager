<?php

namespace App\DTOs;

use App\Helpers\Helper;

class PermissionDTO extends BaseDTO
{
    /**
     * The module name for the permission.
     * This is a read-only property that is set based on the provided module name.
     */
    public readonly ?string $module;

    /**
     * Create a new PermissionDTO instance.
     */
    public function __construct(
        ?string $module,
        public readonly ?string $type,
        public readonly ?bool $is_active = true,
        ?int $id = null
    ) {
        $this->module = Helper::getModuleName($module);

        parent::__construct($id);
    }
}
