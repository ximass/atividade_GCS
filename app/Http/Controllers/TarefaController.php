<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TarefaController extends Controller
{
    public function index()
    {
        $tarefas = DB::table('tarefa')->get();
        return view('tarefas.index', compact('tarefas'));
    }

    public function create()
    {
        return view('tarefas.create');
    }

    public function store(Request $request)
    {
        DB::table('tarefa')->insert([
            'descricao'         => $request->descricao,
            'data_criacao'      => Carbon::now(),
            'data_prevista'     => $request->data_prevista,
            'data_encerramento' => $request->data_encerramento,
            'situacao'          => $request->situacao,
        ]);

        return redirect()->route('tarefas.index')->with('success', 'Tarefa criada com sucesso!');
    }

    public function show($id)
    {
        $tarefa = DB::table('tarefa')->find($id);
        return view('tarefas.show', compact('tarefa'));
    }

    public function edit($id)
    {
        $tarefa = DB::table('tarefa')->find($id);
        return view('tarefas.edit', compact('tarefa'));
    }

    public function update(Request $request, $id)
    {
        DB::table('tarefa')->where('id', $id)->update([
            'descricao'         => $request->descricao,
            'data_prevista'     => $request->data_prevista,
            'data_encerramento' => $request->data_encerramento,
            'situacao'          => $request->situacao,
        ]);

        return redirect()->route('tarefas.index')->with('success', 'Tarefa atualizada com sucesso!');
    }

    public function destroy($id)
    {
        DB::table('tarefa')->where('id', $id)->delete();
        return redirect()->route('tarefas.index')->with('success', 'Tarefa excluída com sucesso!');
    }
}