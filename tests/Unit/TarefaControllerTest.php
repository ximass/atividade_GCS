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
        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('get')->andReturn(collect([]));
        DB::shouldReceive('table')->with('tarefa')->andReturn($queryMock);
        
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
        DB::shouldReceive('table->insert')->once();
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
        DB::shouldReceive('table->find')->andReturn((object)['id'=>1]);
        $controller = new TarefaController();
        $response = $controller->show(1);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('tarefas.show', $response->name());
    }

    public function testEditReturnsView()
    {
        DB::shouldReceive('table->find')->andReturn((object)['id'=>1]);
        $controller = new TarefaController();
        $response = $controller->edit(1);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('tarefas.edit', $response->name());
    }

    public function testUpdateUpdatesData()
    {
        DB::shouldReceive('table->where->update')->once();
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
        DB::shouldReceive('table->where->delete')->once();
        $controller = new TarefaController();
        $response = $controller->destroy(1);
        $this->assertTrue($response->isRedirect());
    }

    public function testExportPdfDownloadsFile()
    {
        DB::shouldReceive('table->get')->andReturn(collect([]));
        \Barryvdh\DomPDF\Facade\Pdf::shouldReceive('loadView')->andReturnSelf();
        \Barryvdh\DomPDF\Facade\Pdf::shouldReceive('download')->andReturn(response('pdf-content'));
        $controller = new TarefaController();
        $request = Request::create('/tarefas/export/pdf', 'GET');
        $response = $controller->exportPdf($request);
        $this->assertEquals('pdf-content', $response->getContent());
    }

    public function testIndexWithFilters()
    {
        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('whereDate')->with('data_criacao', Carbon::now()->toDateString())->andReturnSelf();
        $queryMock->shouldReceive('where')->with('situacao', 'pendente')->andReturnSelf();
        $queryMock->shouldReceive('get')->andReturn(collect([]));
        DB::shouldReceive('table')->with('tarefa')->andReturn($queryMock);
        
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
        DB::shouldReceive('table->insert')->once();
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
        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('get')->andReturn(collect([]));
        DB::shouldReceive('table')->with('tarefa')->andReturn($queryMock);
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'GET');
        $response = $controller->index($request);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testIndexWithOnlyDataFilter()
    {
        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('whereDate')->with('data_criacao', Carbon::now()->toDateString())->andReturnSelf();
        $queryMock->shouldReceive('get')->andReturn(collect([]));
        DB::shouldReceive('table')->with('tarefa')->andReturn($queryMock);
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'GET', [
            'data' => Carbon::now()->toDateString()
        ]);
        $response = $controller->index($request);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testIndexWithOnlySituacaoFilter()
    {
        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('where')->with('situacao', 'pendente')->andReturnSelf();
        $queryMock->shouldReceive('get')->andReturn(collect([]));
        DB::shouldReceive('table')->with('tarefa')->andReturn($queryMock);
        
        $controller = new TarefaController();
        $request = Request::create('/tarefas', 'GET', [
            'situacao' => 'pendente'
        ]);
        $response = $controller->index($request);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testStoreWithDescricaoVazia()
    {
        DB::shouldReceive('table->insert')->once();
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
        DB::shouldReceive('table->insert')->once();
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
        DB::shouldReceive('table->where->update')->once();
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
        DB::shouldReceive('table->find')->andReturn(null);
        $controller = new TarefaController();
        $response = $controller->show(999);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testEditWithInvalidId()
    {
        DB::shouldReceive('table->find')->andReturn(null);
        $controller = new TarefaController();
        $response = $controller->edit(999);
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testDestroyWithInvalidId()
    {
        DB::shouldReceive('table->where->delete')->once();
        $controller = new TarefaController();
        $response = $controller->destroy(999);
        $this->assertTrue($response->isRedirect());
    }

    public function testExportPdfWithFilters()
    {
        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('whereDate')->with('data_criacao', Carbon::now()->toDateString())->andReturnSelf();
        $queryMock->shouldReceive('where')->with('situacao', 'pendente')->andReturnSelf();
        $queryMock->shouldReceive('get')->andReturn(collect([]));
        DB::shouldReceive('table')->with('tarefa')->andReturn($queryMock);
        
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