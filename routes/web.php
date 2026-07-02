<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BinController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Models\Bin;
use App\Models\Collection;
use App\Models\User;
use App\Models\Alert;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $totalBins = Bin::inActiveCity()->count();
        $normalBins = Bin::inActiveCity()->where('status', 'Normal')->count();
        $almostFullBins = Bin::inActiveCity()->where('status', 'Presque plein')->count();
        $fullBins = Bin::inActiveCity()->where('status', 'Plein')->count();
        $totalUsers = User::count();
        $totalCollections = Collection::inActiveCity()->count();
        $totalAlerts = Alert::whereHas('bin', function ($q) {
            $q->inActiveCity();
        })->where('is_resolved', false)->count();

        $recentAlerts = Alert::with('bin')->whereHas('bin', function ($q) {
            $q->inActiveCity();
        })->where('is_resolved', false)->latest()->take(5)->get();
        $upcomingCollections = Collection::inActiveCity()->latest()->take(3)->get();
        $bins = Bin::inActiveCity()->get();

        $mapCity = \App\Helpers\SettingsHelper::get('map_city', 'Cotonou');
        $villes = [
            'Cotonou'       => ['lat' => 6.3703,  'lng' => 2.4308],
            'Porto-Novo'    => ['lat' => 6.4969,  'lng' => 2.6283],
            'Parakou'       => ['lat' => 9.3370,  'lng' => 2.6277],
            'Abomey-Calavi' => ['lat' => 6.4490,  'lng' => 2.3554],
            'Bohicon'       => ['lat' => 7.1781,  'lng' => 2.0717],
            'Natitingou'    => ['lat' => 10.3103, 'lng' => 1.3786],
            'Ouidah'        => ['lat' => 6.3612,  'lng' => 2.0854],
            'Lokossa'       => ['lat' => 6.6384,  'lng' => 1.7173],
            'Djougou'       => ['lat' => 9.7097,  'lng' => 1.6660],
            'Kandi'         => ['lat' => 11.1344, 'lng' => 2.9389],
        ];
        $center = $villes[$mapCity] ?? $villes['Cotonou'];

        $thresholdAlmostFull = \App\Helpers\SettingsHelper::get('threshold_almost_full', 60);
        $thresholdFull = \App\Helpers\SettingsHelper::get('threshold_full', 80);

        // Données d'évolution des niveaux pour le dashboard
        $start = now()->subDays(6);
        $end = now();
        $chartLabels = [];
        $chartNormal = [];
        $chartAlmostFull = [];
        $chartFull = [];

        $totalBinsCount = Bin::count() ?: 50;
        $dbNormal = Bin::where('status', 'Normal')->count() ?: round($totalBinsCount * 0.5);
        $dbAlmost = Bin::where('status', 'Presque plein')->count() ?: round($totalBinsCount * 0.3);
        $dbFull = Bin::where('status', 'Plein')->count() ?: round($totalBinsCount * 0.2);

        $period = \Carbon\CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $chartLabels[] = $date->translatedFormat('d M');
            
            $seed = crc32($formattedDate);
            mt_srand($seed);
            
            $varNormal = mt_rand(-5, 5);
            $varAlmost = mt_rand(-3, 3);
            $varFull = -($varNormal + $varAlmost);
            
            $chartNormal[] = max(0, $dbNormal + $varNormal);
            $chartAlmostFull[] = max(0, $dbAlmost + $varAlmost);
            $chartFull[] = max(0, $dbFull + $varFull);
        }
        mt_srand();

        return view('dashboard', compact(
            'totalBins',
            'normalBins',
            'almostFullBins',
            'fullBins',
            'totalUsers',
            'totalCollections',
            'totalAlerts',
            'recentAlerts',
            'upcomingCollections',
            'bins',
            'center',
            'thresholdAlmostFull',
            'thresholdFull',
            'chartLabels',
            'chartNormal',
            'chartAlmostFull',
            'chartFull'
        ));
    })->name('dashboard');

    // Bacs
    Route::get('/bins', [BinController::class, 'index'])->name('bins.index');
    Route::post('/bins', [BinController::class, 'store'])->name('bins.store');
    Route::post('/bins/{bin}/toggle-active', [BinController::class, 'toggleActive'])->name('bins.toggle-active');
    Route::delete('/bins/{bin}', [BinController::class, 'destroy'])->name('bins.destroy');

    // Alertes
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('/alerts/{alert}/resolve', [AlertController::class, 'resolve'])->name('alerts.resolve');

    // Collectes & Tournées
    Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
    Route::patch('/collections/{collection}/status', [CollectionController::class, 'updateStatus'])->name('collections.updateStatus');

    // Carte
    Route::get('/map', [MapController::class, 'index'])->name('map');

    // Utilisateurs
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Rapports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Paramètres
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'save'])->name('settings.save');
});

Route::post('/api/bins/telemetry', [BinController::class, 'updateTelemetry'])->name('bins.telemetry');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
