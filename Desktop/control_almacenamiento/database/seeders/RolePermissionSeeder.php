<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // grupos
            'view_grupos',
            'create_grupos',
            'edit_grupos',
            'delete_grupos',
            'import_grupos',

            // archivos
            'view_archivos',
            'upload_archivos',
            'delete_archivos',
            'download_archivos',
            'import_archivos',


            // Users
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // System
            'access_admin_panel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $usuarioRole = Role::firstOrCreate(['name' => 'Usuario']);


        // Asignar permisos a roles
        // Admin: todos los permisos
        $adminRole->syncPermissions(Permission::all());

        // Usuario: permisos limitados
        $usuarioPermissions = [
            'view_grupos',
            'view_archivos',
            'upload_archivos',
            'download_archivos',
        ];
        $usuarioRole->syncPermissions($usuarioPermissions);






    }
}
