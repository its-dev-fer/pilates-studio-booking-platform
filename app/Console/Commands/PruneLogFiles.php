<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PruneLogFiles extends Command
{
    protected $signature = 'logs:prune {--days= : Días de retención (por omitir usa config)}';

    protected $description = 'Elimina archivos .log en storage/logs más antiguos que el período configurado';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('logging.prune_after_days', 15));

        if ($days < 1) {
            $this->error('El número de días debe ser al menos 1.');

            return self::FAILURE;
        }

        $cutoff = now()->subDays($days)->getTimestamp();
        $logsPath = storage_path('logs');
        $deleted = 0;

        foreach (glob($logsPath.'/*.log') ?: [] as $path) {
            if (! is_file($path)) {
                continue;
            }

            if (filemtime($path) < $cutoff) {
                if (@unlink($path)) {
                    $deleted++;
                    $this->line('Eliminado: '.basename($path));
                }
            }
        }

        if ($deleted === 0) {
            $this->info("No había archivos de log más antiguos que {$days} días.");
        } else {
            $this->info("Se eliminaron {$deleted} archivo(s) de log (más de {$days} días).");
        }

        return self::SUCCESS;
    }
}
