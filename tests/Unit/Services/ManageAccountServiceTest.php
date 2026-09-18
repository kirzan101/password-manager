<?php

namespace Tests\Unit\Services;

use App\DTOs\AccountDTO;
use App\DTOs\ProfileDTO;
use App\DTOs\ProfileUserGroupDTO;
use App\DTOs\UserDTO;
use App\Helpers\Helper;
use App\Interfaces\BaseInterface;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\ProfileInterface;
use App\Interfaces\ProfileUserGroupInterface;
use App\Interfaces\UserInterface;
use App\Models\Profile;
use App\Models\User;
use App\Services\ManageAccountService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Helpers\TestDoubles;

class ManageAccountServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    /** @var \Mockery\MockInterface&BaseFetchInterface */
    protected BaseFetchInterface $fetch;

    /** @var \Mockery\MockInterface&BaseInterface */
    protected BaseInterface $base;

    /** @var \Mockery\MockInterface&UserInterface */
    protected UserInterface $user;

    /** @var \Mockery\MockInterface&ProfileInterface */
    protected ProfileInterface $profile;

    /** @var \Mockery\MockInterface&ProfileUserGroupInterface */
    protected ProfileUserGroupInterface $profileUserGroup;

    /** @var \Mockery\MockInterface&CurrentUserInterface */
    protected CurrentUserInterface $currentUser;

    protected ManageAccountService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fetch = $this->mockBaseFetchInterface();
        $this->base = $this->mockBaseInterface();
        $this->user = Mockery::mock(UserInterface::class);
        $this->profile = Mockery::mock(ProfileInterface::class);
        $this->profileUserGroup = Mockery::mock(ProfileUserGroupInterface::class);
        $this->currentUser = $this->mockCurrentUserInterface();

        $this->service = new ManageAccountService(
            $this->fetch,
            $this->base,
            $this->user,
            $this->profile,
            $this->profileUserGroup,
            $this->currentUser
        );

        $this->beforeApplicationDestroyed(function () {
            Mockery::close();
        });
    }

    #[Test]
    /**
     * This test verifies successful registration of both a user and a profile.
     *
     * Notes:
     * - Uses a full mock of `Auth::user()` because the service calls it inside a `DB::transaction()`,
     *   which can cause Laravel’s `actingAs()` or `User::factory()` to behave inconsistently.
     * - `storeUser` and `storeProfile` are mocked to simulate successful database operations.
     * - The profile response is returned as an anonymous class extending `Model` to satisfy
     *   the `returnModel()` method's expected type (i.e., Eloquent model).
     */
    public function it_registers_a_user_and_profile_successfully()
    {
        $fakeUser = (object)[
            'profile' => (object)['id' => 99],
        ];
        Auth::shouldReceive('user')->andReturn($fakeUser)->byDefault();

        $this->currentUser->shouldReceive('getProfileId')
            ->once()
            ->andReturn(99);

        $request = [
            'username' => 'testuser',
            'email' => 'user@example.com',
            'avatar' => 'avatar.png',
            'first_name' => 'Test',
            'middle_name' => 'M',
            'last_name' => 'User',
            'nickname' => 'T',
            'type' => 'admin',
            'contact_numbers' => ['123456789'],
            'is_admin' => true,
            'is_first_login' => true,
            'password' => 'testuser',
            'user_group_id' => 1,
        ];

        // 🔑 Wrap into DTO
        $accountDTO = new AccountDTO(
            user: UserDTO::fromArray($request),
            profile: ProfileDTO::fromArray($request),
            user_group_id: $request['user_group_id'],
        );

        $this->user->shouldReceive('storeUser')
            ->once()
            ->with(Mockery::type(UserDTO::class))
            ->andReturn([
                'success' => true,
                'last_id' => 1,
                'data' => (object)['id' => 1],
            ]);

        $profileData = new class(['id' => 123, 'first_name' => 'Test']) extends Model {
            protected $guarded = [];
            public $timestamps = false;
        };

        $this->profile->shouldReceive('storeProfile')
            ->once()
            ->with(Mockery::type(ProfileDTO::class))
            ->andReturn([
                'success' => true,
                'data' => $profileData,
            ]);

        $this->profileUserGroup->shouldReceive('storeProfileUserGroup')
            ->once()
            ->with(Mockery::type(ProfileUserGroupDTO::class))
            ->andReturn([
                'success' => true,
                'data' => (object)['id' => 999],
            ]);

        $response = $this->service->register($accountDTO);

        $this->assertEquals(201, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('Profile registration successfully!', $response['message']);
        $this->assertEquals($profileData, $response['data']);
    }

    #[Test]
    public function it_registers_a_user_as_guest()
    {
        $this->currentUser->shouldReceive('getProfileId')->once()->andReturn(null);

        $request = [
            'username' => 'testuser',
            'email' => 'user@example.com',
            'avatar' => 'avatar.png',
            'first_name' => 'Test',
            'middle_name' => 'M',
            'last_name' => 'User',
            'nickname' => 'T',
            'type' => 'admin',
            'contact_numbers' => ['123456789'],
            'is_admin' => true,
            'is_first_login' => true,
            'password' => 'testuser',
            'user_group_id' => 1,
        ];

        // 🔑 Wrap into DTO
        $accountDTO = new AccountDTO(
            user: UserDTO::fromArray($request),
            profile: ProfileDTO::fromArray($request),
            user_group_id: $request['user_group_id'],
        );

        $this->user->shouldReceive('storeUser')
            ->once()
            ->with(Mockery::type(UserDTO::class))
            ->andReturn([
                'success' => true,
                'last_id' => 1,
                'data' => (object)['id' => 1],
            ]);

        $profileData = new class(['id' => 123, 'first_name' => 'Test']) extends Model {
            protected $guarded = [];
            public $timestamps = false;
        };

        $this->profile->shouldReceive('storeProfile')
            ->once()
            ->with(Mockery::type(ProfileDTO::class))
            ->andReturn([
                'success' => true,
                'data' => $profileData,
            ]);

        $this->profileUserGroup->shouldReceive('storeProfileUserGroup')
            ->once()
            ->with(Mockery::type(ProfileUserGroupDTO::class))
            ->andReturn([
                'success' => true,
                'data' => (object)['id' => 999],
            ]);

        $response = $this->service->register($accountDTO);

        $this->assertEquals(201, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('Profile registration successfully!', $response['message']);
        $this->assertEquals($profileData, $response['data']);
    }

    #[Test]
    public function it_throws_error_if_user_creation_fails()
    {
        $this->actingAs(User::factory()->make([
            'profile' => (object)['id' => 99],
        ]));

        $this->currentUser->shouldReceive('getProfileId')
            ->once()
            ->andReturn(99);

        $request = [
            'username' => 'failuser',
            'email' => 'fail@example.com',
            'first_name' => 'Fail',
            'password' => 'failuser',
            'user_group_id' => 1,
        ];

        // Wrap into DTO
        $accountDTO = new AccountDTO(
            user: UserDTO::fromArray($request),
            profile: ProfileDTO::fromArray($request),
            user_group_id: $request['user_group_id'],
        );

        // Simulate user creation failure
        $this->user->shouldReceive('storeUser')
            ->once()
            ->with(Mockery::type(UserDTO::class))
            ->andReturn([
                'status' => Helper::ERROR,
                'message' => 'User creation failed!',
            ]);

        // Ensure profile is never stored
        $this->profile->shouldNotReceive('storeProfile');

        $response = $this->service->register($accountDTO);

        $this->assertEquals(500, $response['code']);
        $this->assertEquals(Helper::ERROR, $response['status']);
        $this->assertStringContainsString('User creation failed', $response['message']);
    }

    #[Test]
    public function it_throws_error_if_profile_creation_fails()
    {
        $this->actingAs(User::factory()->make([
            'profile' => (object)['id' => 99],
        ]));

        $this->currentUser->shouldReceive('getProfileId')
            ->once()
            ->andReturn(99);

        $request = [
            'username' => 'testuser',
            'email' => 'user@example.com',
            'first_name' => 'FailProfile',
            'password' => 'testuser',
            'user_group_id' => 1,
        ];

        // Wrap into DTO
        $accountDTO = new AccountDTO(
            user: UserDTO::fromArray($request),
            profile: ProfileDTO::fromArray($request),
            user_group_id: $request['user_group_id'],
        );

        // Mock successful user creation
        $this->user->shouldReceive('storeUser')
            ->once()
            ->andReturn([
                'code'     => 201,
                'status'   => Helper::SUCCESS,
                'message'  => 'User created successfully!',
                'data'     => (object)['id' => 1],
                'last_id'  => 1,
            ]);

        // Simulate profile creation failure
        $this->profile->shouldReceive('storeProfile')
            ->once()
            ->with(Mockery::type(ProfileDTO::class))
            ->andReturn([
                'status' => Helper::ERROR,
                'message' => 'Profile creation failed!',
            ]);

        $response = $this->service->register($accountDTO);

        $this->assertEquals(500, $response['code']);
        $this->assertEquals(Helper::ERROR, $response['status']);
        $this->assertStringContainsString('Profile creation failed', $response['message']);
    }

    #[Test]
    public function it_updates_user_profile_successfully()
    {
        $profileId = 123;

        // Mock Auth::user() to return a profile ID
        $currentUserUser = (object)['profile' => (object)['id' => 99]];
        Auth::shouldReceive('user')->andReturn($currentUserUser);

        // Request data for update
        $requestData = [
            'avatar' => 'avatar.png',
            'first_name' => 'Updated',
            'middle_name' => 'Middle',
            'last_name' => 'Name',
            'nickname' => 'Nick',
            'type' => 'employee',
            'contact_numbers' => ['123456789'],
            'username' => 'updateduser',
            'email' => 'updated@example.com',
            'user_group_id' => 5,
        ];

        // Wrap request into DTOs
        $accountDTO = new AccountDTO(
            user: UserDTO::fromArray($requestData),
            profile: ProfileDTO::fromArray($requestData),
            user_group_id: $requestData['user_group_id'],
        );

        // Create a fake profile and associated user
        $mockProfile = new class extends Model {
            public $id = 123;
            public $avatar = 'avatar.png';
            public $first_name = 'First';
            public $middle_name = 'M';
            public $last_name = 'Last';
            public $nickname = 'Nick';
            public $type = 'admin';
            public $contact_numbers = ['987654321'];
            public $user;

            public function __construct()
            {
                $this->user = new class {
                    public $id = 1;
                    public $username = 'olduser';
                    public $email = 'old@example.com';
                };
            }
        };

        // Mock the query builder and firstOrFail() using Mockery
        $fakeQueryBuilder = \Mockery::mock(Builder::class);
        $fakeQueryBuilder
            ->shouldReceive('with')
            ->once()
            ->with('user')
            ->andReturnSelf();

        $fakeQueryBuilder
            ->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($mockProfile);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(Profile::class, $profileId)
            ->andReturn($fakeQueryBuilder);

        // Mock profile update to accept a ProfileDTO
        $this->profile->shouldReceive('updateProfile')
            ->once()
            ->with(Mockery::type(ProfileDTO::class), $profileId)
            ->andReturn(['success' => true]);

        // Mock user update to accept a UserDTO
        $this->user->shouldReceive('updateUser')
            ->once()
            ->with(Mockery::type(UserDTO::class), 1)
            ->andReturn(['success' => true]);

        // Mock user group update
        $this->profileUserGroup->shouldReceive('updateProfileUserGroupWithProfileId')
            ->once()
            ->with(Mockery::type(ProfileUserGroupDTO::class), $profileId)
            ->andReturn(['success' => true]);

        // Run the method with AccountDTO
        $response = $this->service->updateUserProfile($accountDTO, $profileId);

        $this->assertEquals(200, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('Profile updated successfully!', $response['message']);
        $this->assertEquals(123, $response['data']->id);
    }
}
