<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class TarefaController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('tarefa');

        if ($request->filled('data')) {
            $query->whereDate('data_criacao', $request->input('data'));
        }

        if ($request->filled('situacao')) {
            $query->where('situacao', $request->input('situacao'));
        }

        $tarefas = $query->get();

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

        // Simulação de envio de e-mail
        Log::info('Simulação: E-mail enviado ao criar tarefa: ' . $request->descricao);

        return redirect()->route('tarefas.index')
            ->with('success', 'Tarefa criada com sucesso! (Simulação de envio de e-mail)');
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

        // Simulação de envio de e-mail
        Log::info('Simulação: E-mail enviado ao atualizar tarefa ID: ' . $id);

        return redirect()->route('tarefas.index')
            ->with('success', 'Tarefa atualizada com sucesso! (Simulação de envio de e-mail)');
    }

    public function destroy($id)
    {
        DB::table('tarefa')->where('id', $id)->delete();
        return redirect()->route('tarefas.index')->with('success', 'Tarefa excluída com sucesso!');
    }

    public function exportPdf(Request $request)
    {
        $query = DB::table('tarefa');

        // Filtro por data de criação
        if ($request->filled('data')) {
            $query->whereDate('data_criacao', $request->input('data'));
        }

        // Filtro por situação
        if ($request->filled('situacao')) {
            $query->where('situacao', $request->input('situacao'));
        }

        $tarefas = $query->get();

        $pdf = Pdf::loadView('tarefas.pdf', compact('tarefas'));
        return $pdf->download('tarefas.pdf');
    }
}