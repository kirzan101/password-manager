<?php

namespace Tests\Unit\Services;

use App\DTOs\ProfileUserGroupDTO;
use App\Helpers\Helper;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\ProfileUserGroup;
use App\Services\ProfileUserGroupService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Helpers\TestDoubles;

class ProfileUserGroupServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    /** @var \Mockery\MockInterface&BaseInterface */
    protected BaseInterface $base;

    /** @var \Mockery\MockInterface&BaseFetchInterface */
    protected BaseFetchInterface $fetch;

    protected ProfileUserGroupService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the interfaces
        $this->base = $this->mockBaseInterface();
        $this->fetch = $this->mockBaseFetchInterface();

        $this->service = new ProfileUserGroupService(
            $this->base,
            $this->fetch,
        );

        $this->beforeApplicationDestroyed(function () {
            \Mockery::close();
        });
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function it_stores_profile_user_group_successfully()
    {
        $fakeModel = new ProfileUserGroup(['profile_id' => 1, 'user_group_id' => 2]);
        $fakeModel->id = 99;

        $this->base->shouldReceive('store')
            ->once()
            ->with(ProfileUserGroup::class, [
                'profile_id' => 1,
                'user_group_id' => 2,
            ])
            ->andReturn($fakeModel);

        $profileUserGroupDTO = new ProfileUserGroupDTO(
            profile_id: 1,
            user_group_id: 2
        );

        $response = $this->service->storeProfileUserGroup($profileUserGroupDTO);

        $this->assertEquals(201, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('Profile user group created successfully!', $response['message']);
        $this->assertEquals(99, $response['last_id']);
    }

    #[Test]
    public function it_updates_profile_user_group_successfully()
    {
        $profileUserGroupId = 1;

        $existingModel = new ProfileUserGroup([
            'profile_id' => 1,
            'user_group_id' => 2,
        ]);
        $existingModel->id = $profileUserGroupId;

        // Mock the query builder and firstOrFail
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('firstOrFail')->once()->andReturn($existingModel);

        $this->fetch->shouldReceive('showQuery')
            ->once()
            ->with(ProfileUserGroup::class, $profileUserGroupId)
            ->andReturn($builder);

        // Create DTO with updated data
        $profileUserGroupDTO = new ProfileUserGroupDTO(
            profile_id: 1,
            user_group_id: 3 // Updated user_group_id
        );

        // Mock update with array returned by DTO->fromModel()->toArray()
        $this->base->shouldReceive('update')
            ->once()
            ->with(
                $existingModel,
                $profileUserGroupDTO->fromModel($existingModel)->toArray()
            )
            ->andReturn($existingModel);

        $response = $this->service->updateProfileUserGroup($profileUserGroupDTO, $profileUserGroupId);

        $this->assertEquals(200, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('Profile user group updated successfully!', $response['message']);
        $this->assertEquals($existingModel, $response['data']);
    }

    #[Test]
    public function it_deletes_profile_user_group_successfully()
    {
        $profileUserGroupId = 1;
        $model = new ProfileUserGroup(['profile_id' => 1, 'user_group_id' => 2]);
        $model->id = $profileUserGroupId;

        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('firstOrFail')->once()->andReturn($model);

        $this->fetch->shouldReceive('showQuery')
            ->once()
            ->with(ProfileUserGroup::class, $profileUserGroupId)
            ->andReturn($builder);

        $this->base->shouldReceive('delete')
            ->once()
            ->with($model);

        $response = $this->service->deleteProfileUserGroup($profileUserGroupId);

        $this->assertEquals(204, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
    }

    #[Test]
    public function it_updates_profile_user_group_with_profile_id()
    {
        $profileId = 99;

        $model = new ProfileUserGroup([
            'profile_id' => $profileId,
            'user_group_id' => 1
        ]);
        $model->id = 7;

        // Mock query builder and firstOrFail
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('firstOrFail')->once()->andReturn($model);

        $this->fetch->shouldReceive('showQuery')
            ->once()
            ->with(ProfileUserGroup::class, $profileId, 'profile_id')
            ->andReturn($builder);

        // Create DTO with updated data
        $profileUserGroupDTO = new ProfileUserGroupDTO(
            profile_id: $profileId,
            user_group_id: 5 // new value
        );

        // Mock update with DTO array
        $this->base->shouldReceive('update')
            ->once()
            ->with(
                $model,
                $profileUserGroupDTO->toArray()
            )
            ->andReturn($model);

        $response = $this->service->updateProfileUserGroupWithProfileId(
            $profileUserGroupDTO,
            $profileId
        );

        $this->assertEquals(200, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('Profile user group updated successfully!', $response['message']);
        $this->assertEquals($model, $response['data']);
    }

    #[Test]
    public function it_deletes_profile_user_group_with_profile_id()
    {
        $profileId = 99;
        $model = new ProfileUserGroup(['profile_id' => $profileId, 'user_group_id' => 1]);
        $model->id = 10;

        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('firstOrFail')->once()->andReturn($model);

        $this->fetch->shouldReceive('showQuery')
            ->once()
            ->with(ProfileUserGroup::class, $profileId, 'profile_id')
            ->andReturn($builder);

        $this->base->shouldReceive('delete')
            ->once()
            ->with($model);

        $response = $this->service->deleteProfileUserGroupWithProfileId($profileId);

        $this->assertEquals(204, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
    }
}
