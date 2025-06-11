<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\TarefaController;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Health check route for CI/CD
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'timestamp' => now(),
        'environment' => app()->environment(),
    ]);
});

Route::get('/tarefas', [TarefaController::class, 'index']);

Route::resource('tarefas', TarefaController::class);
Route::get('/tarefas/{id}', [TarefaController::class, 'show']);


Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/tarefas');
    }
    return back()->with('error', 'E-mail ou senha inválidos.');
})->name('login');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

Route::post('/register', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email|unique:users,email',
        'name' => 'required|string|max:255',
        'password' => 'required|min:6',
    ]);
    $user = User::create([
        'email' => $request->email,
        'name' => $request->name,
        'password' => Hash::make($request->password),
    ]);
    Auth::login($user);
    return redirect('/tarefas');
})->name('register');

Route::get('/tarefas/export/pdf', [TarefaController::class, 'exportPdf'])->name('tarefas.export.pdf');