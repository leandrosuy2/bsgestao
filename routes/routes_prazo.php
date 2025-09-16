<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallmentController;

// Rotas para vendas a prazo
Route::middleware(['auth'])->group(function () {
    Route::get('/vendas-prazo', [InstallmentController::class, 'index'])->name('installments.index');
    Route::get('/vendas-prazo/{id}/edit', [InstallmentController::class, 'edit'])->name('installments.edit');
    Route::put('/vendas-prazo/{id}', [InstallmentController::class, 'update'])->name('installments.update');
    Route::post('/vendas-prazo/{id}/mark-as-paid', [InstallmentController::class, 'markAsPaid'])->name('installments.mark-as-paid');
    Route::get('/vendas-prazo/vencidas', [InstallmentController::class, 'overdue'])->name('installments.overdue');
    Route::get('/vendas-prazo/vencendo/{days?}', [InstallmentController::class, 'dueSoon'])->name('installments.due-soon');
    Route::get('/vendas-prazo/estatisticas', [InstallmentController::class, 'statistics'])->name('installments.statistics');
    Route::get('/vendas-prazo/export', [InstallmentController::class, 'export'])->name('installments.export');
});

// Atualizar rota do PDV para incluir validação de pagamento a prazo
Route::post('/pdv/finalize', [App\Http\Controllers\PDVController::class, 'finalize'])->name('pdv.finalize');
