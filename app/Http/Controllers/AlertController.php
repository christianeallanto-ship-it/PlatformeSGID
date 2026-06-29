<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Afficher la liste des alertes.
     */
    public function index(Request $request)
    {
        $query = Alert::with('bin');

        // Filtrage par statut de résolution
        if ($request->filled('status')) {
            $isResolved = $request->status === 'resolved';
            $query->where('is_resolved', $isResolved);
        }

        $alerts = $query->latest()->paginate(10)->withQueryString();

        $mapCity = \App\Helpers\SettingsHelper::get('map_city', 'Cotonou');
        return view('alerts.index', compact('alerts', 'mapCity'));
    }

    /**
     * Marquer une alerte comme résolue (vide le bac associé).
     */
    public function resolve(Alert $alert)
    {
        $alert->update(['is_resolved' => true]);

        // Simuler la collecte en remettant le niveau du bac à 0%
        if ($alert->bin) {
            $alert->bin->update([
                'fill_level' => 0,
                'status' => 'Normal'
            ]);
        }

        return redirect()->back()->with('success', 'Alerte résolue avec succès. Le bac a été marqué comme vidé.');
    }
}
