<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test', function () {
    $this->comment('Executando testes...');
    $exitCode = shell_exec('cd ' . base_path() . ' && ./vendor/bin/phpunit 2>&1; echo $?');
    $lines = explode("\n", trim($exitCode));
    $code = (int) array_pop($lines);
    $output = implode("\n", $lines);
    
    $this->line($output);
    
    if ($code === 0) {
        $this->info('✅ Todos os testes passaram!');
    } else {
        $this->error('❌ Alguns testes falharam!');
    }
    
    return $code;
})->purpose('Execute PHPUnit tests');
