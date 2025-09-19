<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;
use App\Models\Nfe;
use App\Models\CashMovement;
use App\Models\CashRegister;

class ConectarVendasComNfe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendas:conectar-nfe {--force : Forçar reconexão mesmo se já conectadas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Conecta vendas existentes com NFe e cria movimentações de caixa faltantes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando conexão de vendas com NFe...');
        
        $force = $this->option('force');
        $conectadas = 0;
        $movimentacoesCriadas = 0;
        
        // Buscar vendas que devem ter NFe
        $vendas = Sale::where('has_nfe', true)
            ->where('status', 'completed')
            ->when(!$force, function($query) {
                return $query->whereNull('nfe_id');
            })
            ->with(['cashRegister', 'nfe'])
            ->get();
            
        $this->info("Encontradas {$vendas->count()} vendas para processar...");
        
        foreach ($vendas as $venda) {
            // Buscar NFe compatível
            $nfe = $this->buscarNfeCompativel($venda);
            
            if ($nfe) {
                // Conectar venda com NFe
                $venda->update(['nfe_id' => $nfe->id]);
                $conectadas++;
                
                $this->line("✓ Venda #{$venda->id} conectada com NFe #{$nfe->id}");
                
                // Verificar se já existe movimentação de caixa para esta venda
                $movimentacaoExistente = CashMovement::where('cash_register_id', $venda->cash_register_id)
                    ->where('description', 'like', "%Venda #{$venda->id}%")
                    ->first();
                    
                if (!$movimentacaoExistente && $venda->cashRegister) {
                    // Criar movimentação de caixa faltante
                    $totalPagamentos = $venda->payments()->where('payment_type', '!=', 'prazo')->sum('amount');
                    
                    if ($totalPagamentos > 0) {
                        CashMovement::create([
                            'cash_register_id' => $venda->cash_register_id,
                            'user_id' => $venda->user_id,
                            'type' => 'in',
                            'amount' => $totalPagamentos,
                            'description' => "Venda PDV (NF) #{$venda->id} - Pagamentos",
                        ]);
                        
                        $movimentacoesCriadas++;
                        $this->line("  → Movimentação de caixa criada: R$ " . number_format($totalPagamentos, 2, ',', '.'));
                    }
                }
            } else {
                $this->warn("⚠ Venda #{$venda->id} não encontrou NFe compatível");
            }
        }
        
        $this->info("\nResumo:");
        $this->info("- Vendas conectadas com NFe: {$conectadas}");
        $this->info("- Movimentações de caixa criadas: {$movimentacoesCriadas}");
        
        if ($conectadas > 0 || $movimentacoesCriadas > 0) {
            $this->info("\n✅ Processo concluído com sucesso!");
        } else {
            $this->info("\nℹ Nenhuma ação necessária.");
        }
    }
    
    /**
     * Busca NFe compatível com a venda
     */
    private function buscarNfeCompativel($venda)
    {
        // Buscar NFe da mesma empresa, emitidas recentemente
        $nfes = Nfe::where('company_id', $venda->company_id)
            ->where('status', 'emitida')
            ->where('data_emissao', '>=', $venda->sold_at->subDays(1))
            ->where('data_emissao', '<=', $venda->sold_at->addDays(1))
            ->get();
            
        foreach ($nfes as $nfe) {
            if ($this->vendaCompativelComNfe($venda, $nfe)) {
                return $nfe;
            }
        }
        
        return null;
    }
    
    /**
     * Verifica se uma venda é compatível com uma NFe
     */
    private function vendaCompativelComNfe($venda, $nfe)
    {
        // Verificar se os valores são próximos (diferença de até R$ 10)
        $diferencaValor = abs($venda->final_total - $nfe->valor_total);
        if ($diferencaValor > 10.00) {
            return false;
        }

        // Verificar se o cliente é o mesmo (se informado)
        if ($venda->customer_id) {
            $cliente = \App\Models\Customer::find($venda->customer_id);
            if ($cliente) {
                $cpfCnpjCliente = preg_replace('/\D/', '', $cliente->cpf_cnpj ?? '');
                $cpfCnpjNfe = preg_replace('/\D/', '', $nfe->cpf_destinatario ?? $nfe->cnpj_destinatario ?? '');
                
                if ($cpfCnpjCliente && $cpfCnpjNfe && $cpfCnpjCliente !== $cpfCnpjNfe) {
                    return false;
                }
            }
        }

        return true;
    }
}
