<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Nova Tarefa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Nova Tarefa</h1>
        <form action="{{ route('tarefas.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <input type="text" name="descricao" id="descricao" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="data_prevista" class="form-label">Data Prevista</label>
                <input type="date" name="data_prevista" id="data_prevista" class="form-control">
            </div>
            <div class="mb-3">
                <label for="data_encerramento" class="form-label">Data Encerramento</label>
                <input type="date" name="data_encerramento" id="data_encerramento" class="form-control">
            </div>
            <div class="mb-3">
                <label for="situacao" class="form-label">Situação</label>
                <select name="situacao" id="situacao" class="form-select">
                    <option value="pendente">Pendente</option>
                    <option value="em_andamento">Em andamento</option>
                    <option value="concluida">Concluída</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('tarefas.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>