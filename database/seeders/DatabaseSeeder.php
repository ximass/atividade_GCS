<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $tarefas = [];

        for ($i = 1; $i <= 10; $i++) {
            $tarefas[] = [
                'descricao'         => "Tarefa número {$i}",
                'data_criacao'      => Carbon::now(),
                'data_prevista'     => Carbon::now()->addDays(rand(1, 10))->toDateString(),
                'data_encerramento' => null,
                'situacao'          => ['pendente', 'em_andamento', 'concluida'][array_rand(['pendente', 'em_andamento', 'concluida'])],
            ];
        }

        DB::table('tarefa')->insert($tarefas);
    }
}
