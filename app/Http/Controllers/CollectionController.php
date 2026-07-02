<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * Afficher la liste des tournées de collecte.
     */
    public function index(Request $request)
    {
        $query = Collection::inActiveCity();

        // Filtrage par statut (Planifiée, En cours, Terminée)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Recherche par chauffeur ou itinéraire
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('route_name', 'like', '%' . $request->search . '%')
                  ->orWhere('driver_name', 'like', '%' . $request->search . '%');
            });
        }

        $collections = $query->orderBy('scheduled_at', 'desc')->paginate(10)->withQueryString();

        return view('collections.index', compact('collections'));
    }

    /**
     * Enregistrer une nouvelle tournée de collecte.
     */
    public function store(Request $request)
    {
        $request->validate([
            'route_name' => 'required|string',
            'driver_name' => 'required|string',
            'scheduled_at' => 'required|date',
            'status' => 'required|in:Planifiée,En cours,Terminée',
        ]);

        Collection::create($request->only([
            'route_name',
            'driver_name',
            'scheduled_at',
            'status'
        ]));

        return redirect()->route('collections.index')->with('success', 'Collecte planifiée avec succès.');
    }

    /**
     * Mettre à jour le statut d'une collecte.
     */
    public function updateStatus(Request $request, Collection $collection)
    {
        $request->validate([
            'status' => 'required|in:Planifiée,En cours,Terminée',
        ]);

        $collection->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Statut de la collecte mis à jour.');
    }
}
