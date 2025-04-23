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
            $table->foreignId('participant_id')
                ->constrained('participants') // Laravel asume que es id()
                ->onDelete('cascade');

            $table->date('fecha_asistencia');
            $table->enum('estado', ['Presente', 'Ausente', 'Justificado'])->default('Ausente')->after('fecha_asistencia');
            $table->timestamps();

           
        });
    }

    public function down()
    {
        Schema::dropIfExists('asistencias');
    }
}