<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Models\UserPaymentIntegration;
use App\Services\SicrediService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSicrediPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sale;
    protected $userIntegration;

    public function __construct(Sale $sale, UserPaymentIntegration $userIntegration)
    {
        $this->sale = $sale;
        $this->userIntegration = $userIntegration;
    }

    public function handle()
    {
        $service = new SicrediService($this->userIntegration->api_key);
        $boletoData = [
            // Preencher com dados da venda
            'valor' => $this->sale->total,
            'cliente' => $this->sale->customer_name,
            'vencimento' => $this->sale->due_date,
            // ... outros campos necessários
        ];
        $result = $service->generateBoleto($boletoData);
        if ($result && isset($result['boleto_url'])) {
            $this->sale->boleto_url = $result['boleto_url'];
            $this->sale->boleto_id = $result['id'] ?? null;
            $this->sale->boleto_status = $result['status'] ?? null;
            $this->sale->save();
        } else {
            Log::error('Falha ao gerar boleto Sicredi', ['sale_id' => $this->sale->id, 'result' => $result]);
        }
    }
}
