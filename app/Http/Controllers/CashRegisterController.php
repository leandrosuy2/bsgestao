<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashRegisterController extends Controller
{
    public function index()
    {
        $registers = CashRegister::where('user_id', Auth::id())
            ->with('user')
            ->orderByDesc('opened_at')
            ->get();
        return view('caixa.index', compact('registers'));
    }

    public function open(Request $request)
    {
        $request->validate([
            'initial_amount' => 'required|numeric|min:0',
        ]);
        $register = CashRegister::create([
            'user_id' => Auth::id(),
            'opened_at' => now(),
            'initial_amount' => $request->initial_amount,
            'status' => 'open',
        ]);
        return redirect()->route('caixa.show', $register->id)->with('success', 'Caixa aberto com sucesso!');
    }

    public function show($id)
    {
        $register = CashRegister::where('user_id', Auth::id())
            ->with(['movements', 'sales'])
            ->findOrFail($id);
        return view('caixa.show', compact('register'));
    }

    public function close(Request $request, $id)
    {
        $register = CashRegister::where('user_id', Auth::id())->findOrFail($id);
        if ($register->status === 'closed') {
            return back()->with('error', 'Caixa já está fechado.');
        }
        $finalAmount = $register->initial_amount + $register->movements()->where('type', 'in')->sum('amount') - $register->movements()->where('type', 'out')->sum('amount') + $register->movements()->where('type', 'sale')->sum('amount');
        $register->update([
            'closed_at' => now(),
            'final_amount' => $finalAmount,
            'status' => 'closed',
        ]);
        return redirect()->route('caixa.show', $register->id)->with('success', 'Caixa fechado com sucesso!');
    }

    public function report($id)
    {
        $register = CashRegister::where('user_id', Auth::id())
            ->with(['movements', 'sales'])
            ->findOrFail($id);
        // Aqui pode-se gerar um PDF ou exibir um relatório detalhado
        return view('caixa.report', compact('register'));
    }
}
