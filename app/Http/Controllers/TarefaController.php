<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarefaController extends Controller
{
    public function index()
    {
        $tarefas = DB::table('tarefa')->get();
        return view('tarefas.index', compact('tarefas'));
    }
}