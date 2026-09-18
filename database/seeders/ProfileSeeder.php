<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Profile;
use App\Models\ProfileRole;
use App\Models\ProfileUserGroup;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'avatar' => null,
                'username' => 'admin',
                'email' => 'admin@mail.com',
                'password' => bcrypt('admin'),
                'is_admin' => true,
                'status' => 'active',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'nickname' => 'Admin',
                'type' => Helper::ACCOUNT_TYPE_ADMIN,
                'position' => 'Administrator',
                'contact_numbers' => [
                    '0999999999'
                ],
                'created_by' => null,
                'updated_by' => null,
                'role' => 'Admin',
                'user_group_code' => 'ADMIN',
                'is_first_login' => false,
            ],
            [
                'avatar' => null,
                'username' => 'user1',
                'email' => 'user@mail.com',
                'password' => bcrypt('user1'),
                'is_admin' => false,
                'status' => 'active',
                'first_name' => 'User',
                'last_name' => 'Account',
                'nickname' => 'user',
                'type' => Helper::ACCOUNT_TYPE_USER,
                'position' => 'System Analyst',
                'contact_numbers' => [
                    '0999999991'
                ],
                'created_by' => null,
                'updated_by' => null,
                'role' => 'System Analyst',
                'user_group_code' => 'USER',
                'is_first_login' => false,
            ],
            [
                'avatar' => 'avatars/lalatina.png',
                'username' => 'lalatina',
                'email' => 'lalatina@mail.com',
                'password' => bcrypt('lalatina'),
                'is_admin' => false,
                'status' => 'active',
                'first_name' => 'Lalatina',
                'last_name' => 'Dustiness Ford',
                'nickname' => 'lalatina',
                'type' => Helper::ACCOUNT_TYPE_USER,
                'position' => 'System Administrator',
                'contact_numbers' => [
                    '0999999991'
                ],
                'created_by' => null,
                'updated_by' => null,
                'role' => 'System Administrator',
                'user_group_code' => 'USER',
                'is_first_login' => false,
            ]
        ];

        foreach ($users as $user) {
            $createdUser = User::create([
                'username' => $user['username'],
                'email' => $user['email'],
                'password' => $user['password'],
                'is_admin' => $user['is_admin'],
                'status' => $user['status'],
                'is_first_login' => $user['is_first_login'] ?? true,
            ]);

            $createdProfile = Profile::create([
                'user_id' => $createdUser->id,
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'nickname' => $user['nickname'],
                'type' => $user['type'],
                'avatar' => $user['avatar'],
                'position' => $user['position'],
                'contact_numbers' => $user['contact_numbers'], //array of contact numbers
                'created_by' => $user['created_by'],
                'updated_by' => $user['updated_by'],
            ]);

            $userGroupId = UserGroup::where('code', $user['user_group_code'])->pluck('id')->first();

            // add user group to profile
            if ($userGroupId) {
                ProfileUserGroup::create([
                    'profile_id' => $createdProfile->id,
                    'user_group_id' => $userGroupId,
                ]);
            }

            $roleId = Role::where('name', $user['role'])->pluck('id')->first();

            // add role to profile
            if ($roleId) {
                ProfileRole::create([
                    'profile_id' => $createdProfile->id,
                    'role_id' => $roleId,
                ]);
            }
        }
    }
}
