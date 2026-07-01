<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Alert;
use App\Models\Collection;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Commande de nettoyage automatique des anciennes données (plus de 3 mois).
 */
Artisan::command('clean:old-data', function () {
    $this->info('Début du nettoyage des anciennes données...');
    
    // 1. Purge des alertes résolues vieilles de plus de 3 mois
    $deletedAlerts = Alert::where('is_resolved', true)
        ->where('updated_at', '<', now()->subMonths(3))
        ->delete();
    $this->info("-> {$deletedAlerts} alertes résolues de plus de 3 mois ont été supprimées.");
    
    // 2. Purge des collectes terminées vieilles de plus de 3 mois
    $deletedCollections = Collection::where('status', 'Terminée')
        ->where('updated_at', '<', now()->subMonths(3))
        ->delete();
    $this->info("-> {$deletedCollections} collectes terminées de plus de 3 mois ont été supprimées.");
    
    $this->info('Nettoyage terminé avec succès !');
})->purpose('Purger les alertes résolues et les collectes terminées de plus de 3 mois.');

// Planifier l'exécution quotidienne
Schedule::command('clean:old-data')->daily();
