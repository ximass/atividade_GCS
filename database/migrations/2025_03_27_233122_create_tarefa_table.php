<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tarefa', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->timestamp('data_criacao')->useCurrent();
            $table->date('data_prevista')->nullable();
            $table->timestamp('data_encerramento')->nullable();
            $table->enum('situacao', ['pendente', 'em_andamento', 'concluida'])->default('pendente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarefa');
    }
};
