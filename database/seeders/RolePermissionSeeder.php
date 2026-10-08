<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing roles and permissions
        Permission::truncate();
        Role::truncate();

        // Create permissions
        $permissions = [
            'view academic sessions',
            'create academic sessions',
            'edit academic sessions',
            'delete academic sessions',
            'view academic terms',
            'create academic terms',
            'edit academic terms',
            'delete academic terms',
            'view classes',
            'create classes',
            'edit classes',
            'delete classes',
            'view class arms',
            'create class arms',
            'edit class arms',
            'delete class arms',
            'view subjects',
            'create subjects',
            'edit subjects',
            'delete subjects',
            'view teacher assignments',
            'create teacher assignments',
            'edit teacher assignments',
            'delete teacher assignments',
            'view teachers',
            'create teachers',
            'edit teachers',
            'delete teachers',
            'view teacher subject assignments',
            'create teacher subject assignments',
            'edit teacher subject assignments',
            'delete teacher subject assignments',
            'view students',
            'create students',
            'edit students',
            'delete students',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $schoolAdministrator = Role::firstOrCreate(['name' => 'school_administrator']);
        $teacher = Role::firstOrCreate(['name' => 'teacher']);

        // Assign all permissions to super_admin
        $superAdmin->syncPermissions($permissions);

        // Assign academic structure permissions to school_administrator
        $schoolAdministrator->givePermissionTo([
            'view academic sessions',
            'create academic sessions',
            'edit academic sessions',
            'view academic terms',
            'create academic terms',
            'edit academic terms',
            'view classes',
            'create classes',
            'edit classes',
            'view class arms',
            'create class arms',
            'edit class arms',
            'view subjects',
            'create subjects',
            'edit subjects',
            'view teacher assignments',
            'create teacher assignments',
            'edit teacher assignments',
            'view teachers',
            'create teachers',
            'edit teachers',
            'view students',
            'create students',
            'edit students',
        ]);

        // Teachers have no admin permissions by default

        // Assign super_admin role to existing admin user
        $admin = User::where('email', 'test@example.com')->first();
        if ($admin) {
            $admin->assignRole('super_admin');
        }
    }
}
