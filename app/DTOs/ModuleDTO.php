<?php

namespace App\DTOs;

class ModuleDTO extends BaseDTO
{
    /**
     * Create a new ModuleDTO instance.
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $icon = null,
        public readonly ?string $route = null,
        public readonly ?string $category = null,
        public readonly int $order = 0,
        public readonly ?string $base_name = null, // format name same as the module name in permissions table. e.g "User Groups" => "user_groups"
        public readonly bool $is_active = true,
        ?int $id = null
    ) {
        parent::__construct($id);
    }

    /**
     * Create a new ModuleDTO instance with the specified order.
     *
     * @param int $order
     * @return self
     */
    public function withOrder(int $order = 0): self
    {
        $data = array_merge($this->toArray(), ['order' => $order]);

        return self::fromArray($data);
    }

    /**
     * Create a new ModuleDTO instance with the specified base name.
     *
     * @param string $base_name
     * @return self
     */
    public function withBaseName(string $base_name): self
    {
        $data = array_merge($this->toArray(), ['base_name' => $base_name]);

        return self::fromArray($data);
    }
}
