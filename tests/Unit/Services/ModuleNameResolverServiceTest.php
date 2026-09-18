<?php

namespace Tests\Unit\Services;

use App\Services\ModuleNameResolverService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModuleNameResolverServiceTest extends TestCase
{
    protected ModuleNameResolverService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ModuleNameResolverService();
    }

    #[Test]
    public function it_returns_snake_case_and_plural_module_name()
    {
        $this->assertEquals('user_groups', $this->service->resolve('UserGroup'));
        $this->assertEquals('roles', $this->service->resolve('Role'));
        $this->assertEquals('audit_logs', $this->service->resolve('AuditLog'));
    }

    #[Test]
    public function it_returns_null_for_null_input()
    {
        $this->assertNull($this->service->resolve(null));
        $this->assertNull($this->service->resolve(''));
    }

    #[Test]
    public function it_returns_same_string_if_not_pluralizable()
    {
        $this->assertEquals('data', $this->service->resolve('Data')); // e.g., already plural or invariant
    }
}
