<?php

namespace Tests\Unit\Services;

use App\DTOs\PermissionDTO;
use App\Interfaces\BaseInterface;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\ModuleNameResolverInterface;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Helpers\TestDoubles;

#[CoversClass(PermissionService::class)]
class PermissionServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    /** @var \Mockery\MockInterface&BaseInterface */
    protected BaseInterface $base;

    /** @var \Mockery\MockInterface&BaseFetchInterface */
    protected BaseFetchInterface $fetch;

    /** @var \Mockery\MockInterface&ModuleNameResolverInterface */
    protected ModuleNameResolverInterface $moduleResolver;

    /** @var \Mockery\MockInterface&CurrentUserInterface */
    protected CurrentUserInterface $currentUser;

    protected PermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->base = $this->mockBaseInterface();
        $this->fetch = $this->mockBaseFetchInterface();
        $this->moduleResolver = Mockery::mock(ModuleNameResolverInterface::class);
        $this->currentUser = $this->mockCurrentUserInterface();

        $this->service = new PermissionService(
            $this->base,
            $this->fetch,
            $this->moduleResolver,
            $this->currentUser
        );

        $this->beforeApplicationDestroyed(fn() => Mockery::close());
    }

    #[Test]
    public function it_can_store_a_permission()
    {
        // Test input
        $request = ['module' => 'equipment', 'type' => 'read', 'is_active' => true];

        // Use PermissionDTO
        $permissionDTO = new PermissionDTO(
            module: $request['module'],
            type: $request['type'],
            is_active: $request['is_active']
        );

        $resolvedModule = 'equipment_management';

        // Fake Permission model to return from base->store
        $mockPermission = new Permission([
            'module' => $resolvedModule,
            'type' => 'read',
            'is_active' => true,
        ]);
        $mockPermission->id = 1;
        $mockPermission->exists = true;

        // Mock the base store call
        $this->base
            ->shouldReceive('store')
            ->once()
            ->with(Permission::class, $permissionDTO->toArray())
            ->andReturn($mockPermission);

        // Call the service method
        $result = $this->service->storePermission($permissionDTO);

        // Assertions
        $this->assertEquals(1, $result['data']->id);
        $this->assertEquals($resolvedModule, $result['data']->module);
        $this->assertEquals('read', $result['data']->type);
        $this->assertTrue($result['data']->is_active);

        $this->assertEquals(201, $result['code']);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Permission created successfully!', $result['message']);
        $this->assertInstanceOf(Permission::class, $result['data']);
    }

    #[Test]
    public function it_can_update_a_permission()
    {
        $permissionId = 1;
        $request = ['module' => 'equipment', 'type' => 'create', 'is_active' => false];

        // DTO now resolves module internally
        $permissionDTO = new PermissionDTO(
            module: $request['module'],
            type: $request['type'],
            is_active: $request['is_active']
        );

        $original = new Permission([
            'id' => $permissionId,
            'module' => 'old_module',
            'type' => 'read',
            'is_active' => true,
        ]);

        $updated = new Permission([
            'id' => $permissionId,
            'module' => $permissionDTO->module, // use DTO module
            'type' => 'create',
            'is_active' => false,
        ]);

        $mockQuery = Mockery::mock(Builder::class);
        $mockQuery->shouldReceive('firstOrFail')->andReturn($original);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(Permission::class, $permissionId)
            ->andReturn($mockQuery);

        $this->base
            ->shouldReceive('update')
            ->once()
            ->with($original, Mockery::on(function ($data) {
                return isset($data['module'], $data['type'], $data['is_active']);
            }))
            ->andReturn($updated);

        $result = $this->service->updatePermission($permissionDTO, $permissionId);

        $this->assertEquals($permissionDTO->module, $result['data']->module);
        $this->assertEquals('create', $result['data']->type);
        $this->assertFalse($result['data']->is_active);

        $this->assertEquals(200, $result['code']);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Permission updated successfully!', $result['message']);
        $this->assertInstanceOf(Permission::class, $result['data']);
    }

    #[Test]
    public function it_can_delete_a_permission()
    {
        $permissionId = 1;
        $permission = new Permission(['id' => $permissionId]);

        $mockQuery = Mockery::mock(Builder::class);
        $mockQuery->shouldReceive('firstOrFail')->andReturn($permission);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(Permission::class, $permissionId)
            ->andReturn($mockQuery);

        $this->base
            ->shouldReceive('delete')
            ->once()
            ->with($permission);

        $result = $this->service->deletePermission($permissionId);

        $this->assertEquals(204, $result['code']);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Permission deleted successfully!', $result['message']);
        $this->assertNull($result['data']);
    }
}
