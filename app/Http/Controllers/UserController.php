<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs.
     */
    public function index()
    {
        $users = User::orderBy('name')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Créer un nouvel utilisateur (admin uniquement).
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'Administrateur') {
            abort(403, 'Action non autorisée. Seuls les administrateurs peuvent gérer les utilisateurs.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Administrateur,Superviseur',
        ], [
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => "L'adresse email est obligatoire.",
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit faire au moins 8 caractères.',
            'role.required' => 'Le rôle est obligatoire.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return redirect()->route('users.index')->with('success', "L'utilisateur {$validated['name']} a été créé avec succès.");
    }

    /**
     * Modifier un utilisateur (admin uniquement).
     */
    public function update(Request $request, User $user)
    {
        if (auth()->user()->role !== 'Administrateur') {
            abort(403, 'Action non autorisée. Seuls les administrateurs peuvent gérer les utilisateurs.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:Administrateur,Superviseur',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8';
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => "L'adresse email est obligatoire.",
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.min' => 'Le mot de passe doit faire au moins 8 caractères.',
            'role.required' => 'Le rôle est obligatoire.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Éviter de s'auto-rétrograder
        if ($user->id !== auth()->id()) {
            $user->role = $validated['role'];
        }

        if ($request->filled('password')) {
            $user->password = \Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', "L'utilisateur {$user->name} a été mis à jour avec succès.");
    }

    /**
     * Activer/Désactiver un utilisateur (admin uniquement).
     */
    public function toggleStatus(User $user)
    {
        if (auth()->user()->role !== 'Administrateur') {
            abort(403, 'Action non autorisée. Seuls les administrateurs peuvent gérer les utilisateurs.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Vous ne pouvez pas désactiver votre propre compte administrateur.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $stateText = $user->is_active ? 'activé' : 'désactivé';
        return redirect()->route('users.index')->with('success', "Le compte de {$user->name} a été {$stateText} avec succès.");
    }

    /**
     * Supprimer un utilisateur définitivement (admin uniquement).
     */
    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'Administrateur') {
            abort(403, 'Action non autorisée. Seuls les administrateurs peuvent gérer les utilisateurs.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte administrateur.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')->with('success', "L'utilisateur {$userName} a été supprimé définitivement.");
    }
}
