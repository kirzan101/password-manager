<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\UserGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userGroups = [
            [
                'name' => 'Admin',
                'code' => 'ADMIN',
                'description' => 'Administrator group',
                // 'created_by' => 1,
                // 'updated_by' => 1,
            ],
            [
                'name' => 'System Users',
                'code' => 'USER',
                'description' => 'Users group',
                // 'created_by' => 1,
                // 'updated_by' => 1,
            ],
        ];

        UserGroup::insert($userGroups);
    }
}
