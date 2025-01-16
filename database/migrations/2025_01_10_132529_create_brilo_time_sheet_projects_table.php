<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brilo_time_sheet_projects', function (Blueprint $table) {
            $table->id();
            $table->string('UsuarioCorreo')->nullable();
            $table->string('Pais_Id')->nullable();
            $table->string('Pais_Nombre')->nullable();
            $table->string('Proyecto_Id')->nullable();
            $table->string('ProyectoNombre')->nullable();
            $table->string('SubProyecto_Id')->nullable();
            $table->string('SubProyectoNombre')->nullable();
            $table->string('rolNombre')->nullable();
            $table->string('Activo')->nullable();
            $table->string('EmpleadoNombre')->nullable();
            $table->string('JefeInmediatoCorreo')->nullable();
            $table->string('JefeInmediatoNombre')->nullable();
            $table->string('EmpleadoCargo')->nullable();
            $table->string('UsuarioToken')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brilo_time_sheet_projects');
    }
};
