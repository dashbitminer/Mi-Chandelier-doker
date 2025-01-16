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
        Schema::create('brilo_user_projects', function (Blueprint $table) {
            $table->id();
            $table->string('UsuarioCorreo')->nullable();
            $table->string('Pais_Id')->nullable();
            $table->string('Pais_Nombre')->nullable();
            $table->string('Proyecto_Id')->nullable();
            $table->string('ProyectoNombre')->nullable();
            $table->string('SubProyecto_Id')->nullable();
            $table->string('SubProyectoNombre')->nullable();
            $table->string('Nombres')->nullable();
            $table->string('Apellidos')->nullable();
            $table->string('JefeInmediatoCorreo')->nullable();
            $table->string('JefeInmediatoNombre')->nullable();
            $table->string('SalarioBase')->nullable();
            $table->string('SalarioDistribucionValor')->nullable();
            $table->string('SalarioDistribucionPorcentaje')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brilo_user_projects');
    }
};
