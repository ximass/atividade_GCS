<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TarefaController;
use Mockery;
use Carbon\Carbon;

class TarefaControllerTest extends TestCase
{
    public function testIndexReturnsView()
    {
        $query = Mockery::mock();
        $query->shouldReceive('get')->andReturn(collect([]));
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($query);
        DB::shouldReceive('connection')->andReturnSelf();
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'GET');
        $response = $controller->index($request);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('tarefas.index', $response->name());
    }

    public function testCreateReturnsView()
    {
        $controller = new TarefaController();
        $response = $controller->create();
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('tarefas.create', $response->name());
    }

    public function testStoreInsertsData()
    {
        $table = Mockery::mock();
        $table->shouldReceive('insert')->once();
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        Log::shouldReceive('info')->once();
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'POST', [
            'descricao' => 'Teste',
            'data_prevista' => '2025-05-10',
            'data_encerramento' => null,
            'situacao' => 'pendente'
        ]);
        $response = $controller->store($request);
        $this->assertTrue($response->isRedirect());
    }

    public function testShowReturnsView()
    {
        $table = Mockery::mock();
        $table->shouldReceive('find')->with(1)->andReturn((object)['id'=>1]);
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        
        $controller = new TarefaController();
        $response = $controller->show(1);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('tarefas.show', $response->name());
    }

    public function testEditReturnsView()
    {
        $table = Mockery::mock();
        $table->shouldReceive('find')->with(1)->andReturn((object)['id'=>1]);
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        
        $controller = new TarefaController();
        $response = $controller->edit(1);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('tarefas.edit', $response->name());
    }

    public function testUpdateUpdatesData()
    {
        $table = Mockery::mock();
        $where = Mockery::mock();
        $where->shouldReceive('update')->once();
        $table->shouldReceive('where')->with('id', 1)->andReturn($where);
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        Log::shouldReceive('info')->once();
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas/1', 'PUT', [
            'descricao' => 'Atualizado',
            'data_prevista' => '2025-05-11',
            'data_encerramento' => null,
            'situacao' => 'concluida'
        ]);
        $response = $controller->update($request, 1);
        $this->assertTrue($response->isRedirect());
    }

    public function testDestroyDeletesData()
    {
        $table = Mockery::mock();
        $where = Mockery::mock();
        $where->shouldReceive('delete')->once();
        $table->shouldReceive('where')->with('id', 1)->andReturn($where);
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        
        $controller = new TarefaController();
        $response = $controller->destroy(1);
        $this->assertTrue($response->isRedirect());
    }

    public function testExportPdfDownloadsFile()
    {
        $query = Mockery::mock();
        $query->shouldReceive('get')->andReturn(collect([]));
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($query);
        DB::shouldReceive('connection')->andReturnSelf();
        
        \Barryvdh\DomPDF\Facade\Pdf::shouldReceive('loadView')->andReturnSelf();
        \Barryvdh\DomPDF\Facade\Pdf::shouldReceive('download')->andReturn(response('pdf-content'));
        $controller = new TarefaController();
        $request = Request::create('/tarefas/export/pdf', 'GET');
        $response = $controller->exportPdf($request);
        $this->assertEquals('pdf-content', $response->getContent());
    }

    public function testIndexWithFilters()
    {
        $query = Mockery::mock();
        $query->shouldReceive('whereDate')->with('data_criacao', Carbon::now()->toDateString())->andReturnSelf();
        $query->shouldReceive('where')->with('situacao', 'pendente')->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect([]));
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($query);
        DB::shouldReceive('connection')->andReturnSelf();
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'GET', [
            'data' => Carbon::now()->toDateString(),
            'situacao' => 'pendente'
        ]);
        $response = $controller->index($request);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('tarefas.index', $response->name());
    }

    public function testStoreWithAllFields()
    {
        $table = Mockery::mock();
        $table->shouldReceive('insert')->once();
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        Log::shouldReceive('info')->once();
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'POST', [
            'descricao' => 'Nova tarefa',
            'data_prevista' => '2025-05-12',
            'data_encerramento' => '2025-05-13',
            'situacao' => 'em_andamento'
        ]);
        $response = $controller->store($request);
        $this->assertTrue($response->isRedirect());
    }

    public function testIndexWithoutFilters()
    {
        $query = Mockery::mock();
        $query->shouldReceive('get')->andReturn(collect([]));
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($query);
        DB::shouldReceive('connection')->andReturnSelf();
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'GET');
        $response = $controller->index($request);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testIndexWithOnlyDataFilter()
    {
        $query = Mockery::mock();
        $query->shouldReceive('whereDate')->with('data_criacao', Carbon::now()->toDateString())->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect([]));
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($query);
        DB::shouldReceive('connection')->andReturnSelf();
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'GET', [
            'data' => Carbon::now()->toDateString()
        ]);
        $response = $controller->index($request);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testIndexWithOnlySituacaoFilter()
    {
        $query = Mockery::mock();
        $query->shouldReceive('where')->with('situacao', 'pendente')->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect([]));
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($query);
        DB::shouldReceive('connection')->andReturnSelf();
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'GET', [
            'situacao' => 'pendente'
        ]);
        $response = $controller->index($request);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testStoreWithDescricaoVazia()
    {
        $table = Mockery::mock();
        $table->shouldReceive('insert')->once();
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        Log::shouldReceive('info')->once();
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'POST', [
            'descricao' => '',
            'data_prevista' => '2025-05-12',
            'data_encerramento' => null,
            'situacao' => 'pendente'
        ]);
        $response = $controller->store($request);
        $this->assertTrue($response->isRedirect());
    }

    public function testStoreWithNullDataEncerramento()
    {
        $table = Mockery::mock();
        $table->shouldReceive('insert')->once();
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        Log::shouldReceive('info')->once();
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'POST', [
            'descricao' => 'Teste',
            'data_prevista' => '2025-05-12',
            'data_encerramento' => null,
            'situacao' => 'pendente'
        ]);
        $response = $controller->store($request);
        $this->assertTrue($response->isRedirect());
    }

    public function testUpdateWithNullDataEncerramento()
    {
        $table = Mockery::mock();
        $where = Mockery::mock();
        $where->shouldReceive('update')->once();
        $table->shouldReceive('where')->with('id', 1)->andReturn($where);
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        Log::shouldReceive('info')->once();
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas/1', 'PUT', [
            'descricao' => 'Atualizado',
            'data_prevista' => '2025-05-11',
            'data_encerramento' => null,
            'situacao' => 'concluida'
        ]);
        $response = $controller->update($request, 1);
        $this->assertTrue($response->isRedirect());
    }

    public function testShowWithInvalidId()
    {
        $table = Mockery::mock();
        $table->shouldReceive('find')->with(999)->andReturn(null);
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        
        $controller = new TarefaController();
        $response = $controller->show(999);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testEditWithInvalidId()
    {
        $table = Mockery::mock();
        $table->shouldReceive('find')->with(999)->andReturn(null);
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        
        $controller = new TarefaController();
        $response = $controller->edit(999);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testDestroyWithInvalidId()
    {
        $table = Mockery::mock();
        $where = Mockery::mock();
        $where->shouldReceive('delete')->once();
        $table->shouldReceive('where')->with('id', 999)->andReturn($where);
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($table);
        DB::shouldReceive('connection')->andReturnSelf();
        
        $controller = new TarefaController();
        $response = $controller->destroy(999);
        $this->assertTrue($response->isRedirect());
    }

    public function testExportPdfWithFilters()
    {
        $query = Mockery::mock();
        $query->shouldReceive('whereDate')->with('data_criacao', Carbon::now()->toDateString())->andReturnSelf();
        $query->shouldReceive('where')->with('situacao', 'pendente')->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect([]));
        
        DB::shouldReceive('table')->with('tarefa')->andReturn($query);
        DB::shouldReceive('connection')->andReturnSelf();
        
        \Barryvdh\DomPDF\Facade\Pdf::shouldReceive('loadView')->andReturnSelf();
        \Barryvdh\DomPDF\Facade\Pdf::shouldReceive('download')->andReturn(response('pdf-content'));
        $controller = new TarefaController();
        $request = Request::create('/tarefas/export/pdf', 'GET', [
            'data' => Carbon::now()->toDateString(),
            'situacao' => 'pendente'
        ]);
        $response = $controller->exportPdf($request);
        $this->assertEquals('pdf-content', $response->getContent());
    }
}