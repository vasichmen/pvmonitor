<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionsSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(PermissionRoleSeeder::class);
        $this->call(SpaceRolesSeeder::class);
        $this->call(AccessRightsSeeder::class);
        $this->call(MaterialTypesSeeder::class);
        $this->call(MaterialStatusesSeeder::class);
    }
}
