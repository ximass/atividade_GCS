<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Tarefas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Relatório de Tarefas</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Data Criação</th>
                <th>Data Prevista</th>
                <th>Data Encerramento</th>
                <th>Situação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tarefas as $tarefa)
            <tr>
                <td>{{ $tarefa->id }}</td>
                <td>{{ $tarefa->descricao }}</td>
                <td>{{ $tarefa->data_criacao }}</td>
                <td>{{ $tarefa->data_prevista }}</td>
                <td>{{ $tarefa->data_encerramento ?? 'Em aberto' }}</td>
                <td>{{ $tarefa->situacao }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>