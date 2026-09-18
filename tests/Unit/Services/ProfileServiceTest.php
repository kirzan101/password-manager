<?php

namespace Tests\Unit\Services;

use App\DTOs\ProfileDTO;
use App\Helpers\Helper;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\Profile;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Builder;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Helpers\TestDoubles;
use Tests\TestCase;

class ProfileServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    /** @var \Mockery\MockInterface&BaseInterface */
    protected BaseInterface $base;

    /** @var \Mockery\MockInterface&BaseFetchInterface */
    protected BaseFetchInterface $fetch;

    /** @var \Mockery\MockInterface&CurrentUserInterface */
    protected CurrentUserInterface $currentUser;

    /** @var \Mockery\MockInterface|ProfileService */
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->base = $this->mockBaseInterface();
        $this->fetch = $this->mockBaseFetchInterface();
        $this->currentUser = $this->mockCurrentUserInterface();

        // 👇 Use partial mock to allow mocking of trait methods
        $this->service = Mockery::mock(ProfileService::class, [
            $this->base,
            $this->fetch,
            $this->currentUser
        ])->makePartial();

        $this->beforeApplicationDestroyed(function () {
            Mockery::close();
        });
    }

    #[Test]
    public function it_stores_a_profile_successfully()
    {
        $profile = new Profile(['id' => 1, 'first_name' => 'John']);

        $this->currentUser
            ->shouldReceive('getProfileId')
            ->once()
            ->andReturn(99);

        // Prepare request data
        $request = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'contact_numbers' => ['123456'],
            'user_id' => 1,
        ];

        // Create DTO
        $profileDTO = new ProfileDTO(
            first_name: $request['first_name'],
            last_name: $request['last_name'],
            contact_numbers: $request['contact_numbers'],
            user_id: $request['user_id']
        );

        // Mock store call with expected array including audit fields
        $this->base
            ->shouldReceive('store')
            ->once()
            ->with(Profile::class, $profileDTO->withDefaultAudit(99)->toArray())
            ->andReturn($profile);

        $response = $this->service->storeProfile($profileDTO);

        $this->assertSame(201, $response['code']);
        $this->assertSame(Helper::SUCCESS, $response['status']);
        $this->assertSame('Profile created successfully!', $response['message']);
        $this->assertEquals($profile, $response['data']);
    }

    #[Test]
    public function it_updates_a_profile_successfully()
    {
        $profileId = 1;
        $mockProfile = new Profile(['id' => $profileId, 'first_name' => 'Old']);

        $this->currentUser
            ->shouldReceive('getProfileId')
            ->once()
            ->andReturn(99);

        // Mock the query builder and firstOrFail
        $builderMock = Mockery::mock(Builder::class);
        $builderMock
            ->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($mockProfile);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(Profile::class, $profileId)
            ->andReturn($builderMock);

        // Prepare the update request
        $request = [
            'first_name' => 'New',
        ];

        // Create DTO
        $profileDTO = new ProfileDTO(
            first_name: $request['first_name']
            // You can add other fields if needed, default null otherwise
        );

        // Mock update call with expected array including updated_by
        $this->base
            ->shouldReceive('update')
            ->once()
            ->with($mockProfile, $profileDTO->fromModel($mockProfile)->touchUpdatedBy(99)->toArray())
            ->andReturn($mockProfile);

        $response = $this->service->updateProfile($profileDTO, $profileId);

        $this->assertSame(200, $response['code']);
        $this->assertSame(Helper::SUCCESS, $response['status']);
        $this->assertSame('Profile updated successfully!', $response['message']);
        $this->assertEquals($mockProfile, $response['data']);
    }

    #[Test]
    public function it_deletes_a_profile_successfully(): void
    {
        $profileId = 1;
        $currentUserId = 99;

        $mockProfile = Mockery::mock(Profile::class)->makePartial();
        $mockProfile->shouldReceive('getAttribute')->with('id')->andReturn($profileId);

        // Simulate modelUsesSoftDeletes() by making it a SoftDeletes model
        $mockProfile->shouldReceive('getMorphClass')->andReturn(Profile::class); // dummy for type safety

        // showQuery returns a Builder mock that returns the user group
        $mockQuery = Mockery::mock(Builder::class);
        $mockQuery->shouldReceive('firstOrFail')->once()->andReturn($mockProfile);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(Profile::class, $profileId)
            ->andReturn($mockQuery);

        // Mock modelUsesSoftDeletes() and modelHasColumn() directly on the service mock
        $this->service
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('modelUsesSoftDeletes')
            ->andReturn(true);

        $this->service
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('modelHasColumn')
            ->withAnyArgs()
            ->andReturnUsing(function ($model, $column) {
                return $column === 'updated_by';
            });

        $this->currentUser
            ->shouldReceive('getProfileId')
            ->once()
            ->andReturn($currentUserId);

        $this->base
            ->shouldReceive('update')
            ->once()
            ->with($mockProfile, ['updated_by' => $currentUserId])
            ->andReturn($mockProfile);

        $this->base
            ->shouldReceive('delete')
            ->once()
            ->with($mockProfile);

        $response = $this->service->deleteProfile($profileId);

        $this->assertSame(204, $response['code']);
        $this->assertSame('success', $response['status']);
        $this->assertSame('Profile deleted successfully!', $response['message']);
        $this->assertNull($response['data']);
    }
}
