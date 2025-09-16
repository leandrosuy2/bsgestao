<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Nfe;

class CorrigirNfesCanceladas extends Command
{
    protected $signature = 'nfe:corrigir-canceladas';
    protected $description = 'Corrige NFes que foram canceladas na API mas não atualizadas no banco';

    public function handle()
    {
        $this->info('Verificando NFes com status inconsistente...');
        
        // Buscar NFes que podem ter sido canceladas mas não atualizadas
        $nfes = Nfe::where('status', 'emitida')
                   ->whereNotNull('ref')
                   ->get();

        $corrigidas = 0;
        
        foreach ($nfes as $nfe) {
            // Aqui você poderia consultar a API para verificar o status real
            // Por agora, vamos apenas mostrar as que podem ter problemas
            $this->line("NFe {$nfe->id} (Ref: {$nfe->ref}) - Status: {$nfe->status}");
        }
        
        $this->info("Verificação concluída. {$corrigidas} NFes corrigidas.");
    }
}
