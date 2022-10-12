<?php


namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    private array $rolesPermissions = [
        'admin' => [
            'manage_site_header',
        ],
        'user' => [

        ],

    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        foreach ($this->rolesPermissions as $roleCode => $permissions) {
            $role = Role::where('code', $roleCode)->first();

            if (!$role) {
                continue;
            }

            $role->permissions()->detach();

            foreach ($permissions as $permissionCode) {
                $permission = Permission::where('code', $permissionCode)->first();

                if (!$permission) {
                    continue;
                }

                $role->permissions()->attach($permission);
            }
        }
    }

}
