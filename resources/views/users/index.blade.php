@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs - BENINCLEAN')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="users" class="text-green-600"></i>
                Utilisateurs du système
            </h1>
            <p class="text-sm text-slate-500">Gérez les comptes, les mots de passe et les rôles des administrateurs et superviseurs.</p>
        </div>
        
        @if(auth()->user()->role === 'Administrateur')
            <div class="shrink-0">
                <button onclick="toggleModal('createUserModal')" class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded-xl flex items-center gap-2 shadow-md transition-all">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Créer un utilisateur
                </button>
            </div>
        @endif
    </div>

    <!-- Feedback messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Table of Users -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Nom complet</th>
                        <th class="px-6 py-4">Adresse Email</th>
                        <th class="px-6 py-4">Rôle</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Date d'inscription</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs text-slate-700 font-bold">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <span>{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold inline-block {{ $user->role === 'Administrateur' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->is_active)
                                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-green-50 text-green-700 border border-green-200 inline-block">
                                        Actif
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 inline-block">
                                        Désactivé
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}
                                                       <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(auth()->user()->role === 'Administrateur' || auth()->id() === $user->id)
                                        <!-- Edit button -->
                                        <button onclick="openEditModal({{ json_encode($user) }})" class="bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold py-1.5 px-3 rounded-lg transition-all flex items-center gap-1">
                                            <i data-lucide="edit-2" class="w-3 h-3"></i>
                                            Modifier
                                        </button>
                                    @endif

                                    @if(auth()->user()->role === 'Administrateur')
                                        @if($user->id !== auth()->id())
                                            <!-- Toggle Active Status -->
                                            <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                @if($user->is_active)
                                                    <button type="submit" class="bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold py-1.5 px-3 rounded-lg transition-all">
                                                        Désactiver
                                                    </button>
                                                @else
                                                    <button type="submit" class="bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 text-xs font-bold py-1.5 px-3 rounded-lg transition-all">
                                                        Activer
                                                    </button>
                                                @endif
                                            </form>

                                            <!-- Delete User -->
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement l\'utilisateur {{ $user->name }} ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold py-1.5 px-3 rounded-lg transition-all flex items-center gap-1">
                                                    <i data-lucide="trash" class="w-3 h-3"></i>
                                                    Supprimer
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400 italic px-2">Vous</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'Administrateur' ? 6 : 5 }}" class="px-6 py-12 text-center text-slate-400">
                                Aucun utilisateur inscrit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

@if(auth()->user()->role === 'Administrateur')
    <!-- Create User Modal -->
    <div id="createUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="toggleModal('createUserModal')"></div>
            <!-- Centered modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                            <h3 class="text-lg font-bold text-slate-800" id="modal-title">Créer un utilisateur</h3>
                            <button type="button" class="text-slate-400 hover:text-slate-600" onclick="toggleModal('createUserModal')">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="name" class="block text-xs font-bold text-slate-500 uppercase mb-1">Nom complet</label>
                                <input type="text" name="name" id="name" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500" placeholder="Ex: Jean Dupont">
                            </div>
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-500 uppercase mb-1">Adresse Email</label>
                                <input type="email" name="email" id="email" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500" placeholder="Ex: jean.dupont@beninclean.com">
                            </div>
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-500 uppercase mb-1">Mot de passe</label>
                                <input type="password" name="password" id="password" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500" placeholder="Au moins 8 caractères">
                            </div>
                            <div>
                                <label for="role" class="block text-xs font-bold text-slate-500 uppercase mb-1">Rôle / Statut</label>
                                <select name="role" id="role" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="Superviseur">Superviseur</option>
                                    <option value="Administrateur">Administrateur</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-3xl">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition-all">
                            Créer le compte
                        </button>
                        <button type="button" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-bold py-2.5 px-4 rounded-xl transition-all" onclick="toggleModal('createUserModal')">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endif

    <!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="toggleModal('editUserModal')"></div>
            <!-- Centered modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100">
                <form id="editForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                            <h3 class="text-lg font-bold text-slate-800" id="modal-title">Modifier l'utilisateur</h3>
                            <button type="button" class="text-slate-400 hover:text-slate-600" onclick="toggleModal('editUserModal')">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="edit_name" class="block text-xs font-bold text-slate-500 uppercase mb-1">Nom complet</label>
                                <input type="text" name="name" id="edit_name" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500">
                            </div>
                            <div>
                                <label for="edit_email" class="block text-xs font-bold text-slate-500 uppercase mb-1">Adresse Email</label>
                                <input type="email" name="email" id="edit_email" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500">
                            </div>
                            <div>
                                <label for="edit_password" class="block text-xs font-bold text-slate-500 uppercase mb-1">Mot de passe (laisser vide pour ne pas modifier)</label>
                                <input type="password" name="password" id="edit_password" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500" placeholder="Saisir un nouveau mot de passe si besoin">
                            </div>
                            <div>
                                <label for="edit_role" class="block text-xs font-bold text-slate-500 uppercase mb-1">Rôle / Statut</label>
                                <select name="role" id="edit_role" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="Superviseur">Superviseur</option>
                                    <option value="Administrateur">Administrateur</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-3xl">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition-all">
                            Enregistrer les modifications
                        </button>
                        <button type="button" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-bold py-2.5 px-4 rounded-xl transition-all" onclick="toggleModal('editUserModal')">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function openEditModal(user) {
            document.getElementById('editForm').action = `/users/${user.id}`;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_password').value = '';

            // Empêcher un non-administrateur de modifier les rôles, ou l'admin de se rétrograder
            const currentRole = "{{ auth()->user()->role }}";
            if (currentRole !== 'Administrateur' || user.id === {{ auth()->id() }}) {
                document.getElementById('edit_role').disabled = true;
            } else {
                document.getElementById('edit_role').disabled = false;
            }

            toggleModal('editUserModal');
        }
    </script>
@endsection
