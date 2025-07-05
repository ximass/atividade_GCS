<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Listagem de Tarefas 1</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Listagem de Tarefas 4</h1>
        <a href="{{ route('tarefas.create') }}" class="btn btn-primary mb-3">Nova Tarefa</a>
        <a href="{{ route('tarefas.export.pdf', request()->all()) }}" class="btn btn-danger mb-3 ms-2">Exportar para PDF</a>

        <!-- Filtro de tarefas -->
        <form method="GET" action="{{ route('tarefas.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="data" class="form-label">Data de Criação</label>
                <input type="date" id="data" name="data" class="form-control" value="{{ request('data') }}">
            </div>
            <div class="col-md-4">
                <label for="situacao" class="form-label">Situação</label>
                <select id="situacao" name="situacao" class="form-select">
                    <option value="">Todas</option>
                    <option value="pendente" {{ request('situacao') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="concluída" {{ request('situacao') == 'Concluída' ? 'selected' : '' }}>Concluída</option>
                    <option value="em_andamento" {{ request('situacao') == 'Em Andamento' ? 'selected' : '' }}>Em Andamento</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary">Filtrar</button>
            </div>
        </form>
        <!-- Fim do filtro -->

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descrição</th>
                    <th>Data Criação</th>
                    <th>Data Prevista</th>
                    <th>Data Encerramento</th>
                    <th>Situação</th>
                    <th>Ações</th>
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
                    <td>
                        <a href="{{ route('tarefas.edit', $tarefa->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('tarefas.destroy', $tarefa->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Tem certeza que deseja excluir?');">
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>