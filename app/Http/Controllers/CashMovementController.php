<?php

namespace App\Http\Controllers;

use App\Models\CashMovement;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashMovementController extends Controller
{
    public function store(Request $request, $cashRegisterId)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);
        $register = CashRegister::findOrFail($cashRegisterId);
        if ($register->status !== 'open') {
            return back()->with('error', 'Caixa não está aberto.');
        }
        CashMovement::create([
            'cash_register_id' => $register->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);
        return back()->with('success', 'Movimentação registrada com sucesso!');
    }

    public function index($cashRegisterId)
    {
        $register = CashRegister::findOrFail($cashRegisterId);
        $movements = $register->movements()->orderByDesc('created_at')->get();
        return view('caixa.movements', compact('register', 'movements'));
    }
}
