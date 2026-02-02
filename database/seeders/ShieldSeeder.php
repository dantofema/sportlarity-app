<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_note","view_any_note","create_note","update_note","restore_note","restore_any_note","replicate_note","reorder_note","delete_note","delete_any_note","force_delete_note","force_delete_any_note","view_plan","view_any_plan","create_plan","update_plan","restore_plan","restore_any_plan","replicate_plan","reorder_plan","delete_plan","delete_any_plan","force_delete_plan","force_delete_any_plan","view_shield::role","view_any_shield::role","create_shield::role","update_shield::role","delete_shield::role","delete_any_shield::role","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user"]},{"name":"coach","guard_name":"web","permissions":["view_diary","view_any_diary","view_document","view_any_document","create_document","update_document","restore_document","restore_any_document","replicate_document","reorder_document","delete_document","delete_any_document","force_delete_document","force_delete_any_document","view_note","view_any_note","create_note","update_note","restore_note","restore_any_note","replicate_note","reorder_note","delete_note","delete_any_note","force_delete_note","force_delete_any_note","view_plan","view_any_plan","create_plan","update_plan","restore_plan","restore_any_plan","replicate_plan","reorder_plan","delete_plan","delete_any_plan","force_delete_plan","force_delete_any_plan","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","view_assessment","view_any_assessment","create_assessment","update_assessment","restore_assessment","restore_any_assessment","replicate_assessment","reorder_assessment","delete_assessment","delete_any_assessment","force_delete_assessment","force_delete_any_assessment"]},{"name":"professional","guard_name":"web","permissions":["view_diary","view_any_diary","view_document","view_any_document","create_document","update_document","restore_document","restore_any_document","replicate_document","reorder_document","delete_document","delete_any_document","force_delete_document","force_delete_any_document","view_note","view_any_note","create_note","update_note","restore_note","restore_any_note","replicate_note","reorder_note","delete_note","delete_any_note","force_delete_note","force_delete_any_note","view_plan","view_any_plan","create_plan","update_plan","restore_plan","restore_any_plan","replicate_plan","reorder_plan","delete_plan","delete_any_plan","force_delete_plan","force_delete_any_plan","view_user","view_any_user","view_assessment","view_any_assessment","create_assessment","update_assessment","restore_assessment","restore_any_assessment","replicate_assessment","reorder_assessment","delete_assessment","delete_any_assessment","force_delete_assessment","force_delete_any_assessment"]},{"name":"wellness","guard_name":"web","permissions":["view_diary","view_any_diary","create_diary","update_diary","restore_diary","replicate_diary","reorder_diary","delete_diary","force_delete_diary","view_document","view_any_document","view_note","view_any_note","view_plan","view_any_plan","view_assessment","view_any_assessment"]}]';
        $directPermissions = '{"5":{"name":"restore_any_diary","guard_name":"web"},"9":{"name":"delete_any_diary","guard_name":"web"},"11":{"name":"force_delete_any_diary","guard_name":"web"}}';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
