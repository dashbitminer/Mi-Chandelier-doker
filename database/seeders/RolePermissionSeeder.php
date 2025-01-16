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
        if (Role::count() === 0) {
            $administrador = Role::create(['name' => 'administrador']);
            $contabilidad = Role::create(['name' => 'contabilidad']);
            $colaborador = Role::create(['name' => 'colaborador']);
            $creadorContenido = Role::create(['name' => 'creador-contenido']);

            $permissions = [
                ['name' => 'operaciones.solicitudes-viaje', 'scope' => 'operaciones'],
                ['name' => 'operaciones.solicitudes-viaje.review', 'scope' => 'operaciones'],
                ['name' => 'operaciones.hojas-tiempo', 'scope' => 'operaciones'],
                ['name' => 'operaciones.hojas-tiempo.review', 'scope' => 'operaciones'],

                ['name' => 'formaciones.cursos', 'scope' => 'formaciones'],
                ['name' => 'formaciones.centro-ayuda', 'scope' => 'formaciones'],
                ['name' => 'admin.formaciones', 'scope' => 'admin.formaciones'],
                ['name' => 'admin.formaciones.categorias', 'scope' => 'admin.formaciones'],
                ['name' => 'admin.formaciones.cursos', 'scope' => 'admin.formaciones'],
                ['name' => 'admin.formaciones.cursos-eliminados', 'scope' => 'admin.formaciones'],

                ['name' => 'contabilidad.hojas-tiempo', 'scope' => 'contabilidad'],
                ['name' => 'contabilidad.solicitudes-viaje', 'scope' => 'contabilidad'],
                ['name' => 'contabilidad.proyectos', 'scope' => 'contabilidad'],
                ['name' => 'contabilidad.tipo-pago', 'scope' => 'contabilidad'],

                ['name' => 'admin.usuarios', 'scope' => 'admin'],
                ['name' => 'admin.roles', 'scope' => 'admin'],
            ];

            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission['name'], 'scope' => $permission['scope']]);

                $administrador->givePermissionTo($permission['name']);
            }

            $contabilidad->syncPermissions(
                'operaciones.solicitudes-viaje',
                'operaciones.hojas-tiempo',
                'formaciones.cursos',
                'formaciones.centro-ayuda',
                'contabilidad.hojas-tiempo',
                'contabilidad.solicitudes-viaje',
                'contabilidad.proyectos',
                'contabilidad.tipo-pago'
            );

            $colaborador->syncPermissions(
                'operaciones.solicitudes-viaje',
                'operaciones.solicitudes-viaje.review',
                'operaciones.hojas-tiempo',
                'operaciones.hojas-tiempo.review',
                'formaciones.cursos',
                'formaciones.centro-ayuda'
            );

            $creadorContenido->syncPermissions(
                'admin.formaciones',
                'admin.formaciones.categorias',
                'admin.formaciones.cursos',
                'admin.formaciones.cursos-eliminados',
            );
        }
    }
}
