<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsistenciasTable extends Migration
{
    public function up()
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id('asistencia_id');
            $table->unsignedBigInteger('participante_id');
            $table->date('fecha_asistencia');
            $table->boolean('presente')->default(false);
            $table->timestamps();

            // Clave foránea
            $table->foreign('participante_id')->references('participante_id')->on('participante')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('asistencias');
    }
}