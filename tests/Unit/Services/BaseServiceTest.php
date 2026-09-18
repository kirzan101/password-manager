<?php

namespace Tests\Unit\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Stubs\TestModel;

class BaseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BaseService $service;


    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('test_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        $this->service = new BaseService();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('test_models');
        parent::tearDown();
    }

    private function testModelClass(): string
    {
        return TestModel::class;
    }

    #[Test]
    public function it_can_store_a_model(): void
    {
        $modelClass = $this->testModelClass();

        $stored = $this->service->store($modelClass, ['name' => 'Example']);

        $this->assertDatabaseHas('test_models', ['name' => 'Example']);
        $this->assertInstanceOf($modelClass, $stored);
    }

    #[Test]
    public function it_throws_on_invalid_model_store(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service->store(\stdClass::class, ['name' => 'Invalid']);
    }

    #[Test]
    public function it_can_store_multiple_records(): void
    {
        $modelClass = $this->testModelClass();

        $result = $this->service->storeMultiple($modelClass, [
            ['name' => 'One'],
            ['name' => 'Two']
        ]);

        $this->assertTrue($result);
        $this->assertDatabaseHas('test_models', ['name' => 'One']);
        $this->assertDatabaseHas('test_models', ['name' => 'Two']);
    }

    #[Test]
    public function it_can_update_a_model(): void
    {
        $modelClass = $this->testModelClass();
        $stored = $this->service->store($modelClass, ['name' => 'Old']);

        $updated = $this->service->update($stored, ['name' => 'New']);

        $this->assertEquals('New', $updated->name);
        $this->assertDatabaseHas('test_models', ['name' => 'New']);
    }

    #[Test]
    public function it_can_delete_a_model(): void
    {
        $modelClass = $this->testModelClass();
        $stored = $this->service->store($modelClass, ['name' => 'DeleteMe']);

        $this->service->delete($stored);

        $this->assertDatabaseMissing('test_models', ['name' => 'DeleteMe']);
    }

    #[Test]
    public function it_can_delete_multiple_records(): void
    {
        $modelClass = $this->testModelClass();

        $first = $this->service->store($modelClass, ['name' => 'A']);
        $second = $this->service->store($modelClass, ['name' => 'B']);

        $this->service->deleteMultiple($modelClass, [$first->id, $second->id]);

        $this->assertDatabaseMissing('test_models', ['name' => 'A']);
        $this->assertDatabaseMissing('test_models', ['name' => 'B']);
    }

    #[Test]
    public function it_skips_delete_multiple_if_ids_empty(): void
    {
        $modelClass = $this->testModelClass();

        $this->service->deleteMultiple($modelClass, []);

        // No error thrown is a pass
        $this->assertTrue(true);
    }

    #[Test]
    public function it_throws_if_column_does_not_exist_on_delete_multiple(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $modelClass = $this->testModelClass();
        $this->service->deleteMultiple($modelClass, [1], 'non_existent_column');
    }

    #[Test]
    public function it_throws_on_invalid_model_class(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $reflection = new \ReflectionClass(BaseService::class);
        $method = $reflection->getMethod('validateModelClass');
        $method->setAccessible(true);

        $method->invoke(new BaseService(), \stdClass::class);
    }
}
