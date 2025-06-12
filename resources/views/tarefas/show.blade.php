<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Tarefa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Detalhes da Tarefa</h1>
        
        @if($tarefa)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $tarefa->descricao }}</h5>
                <p class="card-text">
                    <strong>ID:</strong> {{ $tarefa->id }}<br>
                    <strong>Data de Criação:</strong> {{ $tarefa->data_criacao }}<br>
                    <strong>Data Prevista:</strong> {{ $tarefa->data_prevista }}<br>
                    <strong>Data de Encerramento:</strong> {{ $tarefa->data_encerramento ?? 'Em aberto' }}<br>
                    <strong>Situação:</strong> {{ $tarefa->situacao }}
                </p>
            </div>
        </div>
        @else
        <div class="alert alert-warning">
            Tarefa não encontrada.
        </div>
        @endif
        
        <div class="mt-3">
            <a href="{{ route('tarefas.index') }}" class="btn btn-secondary">Voltar</a>
            @if($tarefa)
                <a href="{{ route('tarefas.edit', $tarefa->id) }}" class="btn btn-primary">Editar</a>
            @endif
        </div>
    </div>
</body>
</html>
