<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage, useForm } from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Gestionnaire');

const props = defineProps({
    users: Object,
    services: Array,
});

const showAddUserModal = ref(false);
const isEditing = ref(false);

const userForm = useForm({
    id: null,
    name: '',
    email: '',
    role: 'demandeur',
    ser_id: '',
    password: '', // Optionnel pour la modification
});

const openAddModal = () => {
    isEditing.value = false;
    userForm.reset();
    userForm.clearErrors();
    showAddUserModal.value = true;
};

const openEditModal = (user) => {
    isEditing.value = true;
    userForm.clearErrors();
    userForm.id = user.id;
    userForm.name = user.name;
    userForm.email = user.email;
    userForm.role = user.role;
    userForm.ser_id = user.ser_id;
    userForm.password = ''; 
    showAddUserModal.value = true;
};

const deleteUser = (id) => {
    if (confirm('Supprimer cet utilisateur ?')) {
        router.delete(route('gestionnaire.utilisateurs.destroy', id));
    }
};

const saveUser = () => {
    if (isEditing.value) {
        userForm.put(route('gestionnaire.utilisateurs.update', userForm.id), {
            onSuccess: () => {
                showAddUserModal.value = false;
                userForm.reset();
            }
        });
    } else {
        userForm.post(route('gestionnaire.utilisateurs.store'), {
            onSuccess: () => {
                showAddUserModal.value = false;
                userForm.reset();
            }
        });
    }
};

const getRoleClass = (role) => {
    if (role === 'gestionnaire') return 'bg-teal-100 text-teal-700';
    if (role === 'responsable') return 'bg-purple-100 text-purple-700';
    return 'bg-blue-100 text-blue-700';
};

const navigation = [
    { name: 'Tableau de bord', route: 'gestionnaire.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', route: 'gestionnaire.articles.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', route: 'gestionnaire.mouvements.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', route: 'gestionnaire.demandes.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports', route: 'gestionnaire.rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Utilisateurs', route: 'gestionnaire.utilisateurs.index', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { name: 'Services & Fournisseurs', route: 'gestionnaire.services-fournisseurs.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
];

const logout = () => {
    if (confirm('Déconnexion ?')) router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <aside class="fixed left-0 top-0 h-screen w-52 bg-slate-800 shadow-2xl z-50 flex flex-col">
            <div class="px-6 py-6 border-b border-slate-700/50">
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    <span class="text-blue-400">Stock</span><span class="text-teal-400">Flow</span>
                </h1>
                <p class="text-xs text-slate-300 mt-2">Espace Gestionnaire</p>
            </div>
            <nav class="px-3 py-6 space-y-1.5 flex-1">
                <Link v-for="item in navigation" :key="item.name" :href="item.route ? route(item.route) : '#'"
                    :class="[item.route && route().current(item.route) ? 'bg-teal-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700 hover:text-white', 'group flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all']">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    {{ item.name }}
                </Link>
            </nav>
            <div class="p-4 border-t border-slate-700/50">
                <button @click="logout"
                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-400 hover:bg-red-500/10 hover:text-red-400 rounded-lg transition-all group">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Déconnexion
                </button>
            </div>
        </aside>

        <div class="ml-52 flex-1">
            <header
                class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4 text-slate-500">
                    <Link :href="route('gestionnaire.rapports.index')" class="hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <span class="font-medium">Utilisateurs</span>
                </div>
                <div class="flex items-center gap-2 text-slate-700 hover:text-teal-600 cursor-pointer group">
                    <div class="text-sm font-medium text-slate-700">{{ userName }}</div>
                    <div
                        class="w-9 h-9 flex items-center justify-center bg-slate-100 rounded-full group-hover:bg-teal-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </header>

            <main class="p-8 space-y-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Gestion des utilisateurs</h2>
                        <p class="text-slate-500 text-sm">Gérer les comptes et les accès du systèmes</p>
                    </div>
                    <button @click="openAddModal"
                        class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Ajouter utilisateur
                    </button>
                </div>

                <div v-if="showAddUserModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                        @click="showAddUserModal = false"></div>

                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h3 class="text-lg font-bold text-slate-800">{{ isEditing ? 'Modifier l\'utilisateur' : 'Nouvel Utilisateur' }}</h3>
                            <button @click="showAddUserModal = false"
                                class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
                        </div>

                        <form @submit.prevent="saveUser" class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Nom complet</label>
                                <input v-model="userForm.name" type="text" placeholder="Ex: Moussa Ndiaye" required
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Email professionnel</label>
                                <input v-model="userForm.email" type="email" placeholder="m.ndiaye@stockflow.sn"
                                    required
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Rôle</label>
                                    <select v-model="userForm.role"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                                        <option value="gestionnaire">Gestionnaire</option>
                                        <option value="demandeur">Demandeur</option>
                                        <option value="responsable">Responsable</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Service</label>
                                    <select v-model="userForm.ser_id" required
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                                        <option value="" disabled>Choisir un service</option>
                                        <option v-for="service in props.services" :key="service.ser_id" :value="service.ser_id">
                                            {{ service.ser_nom }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="!isEditing">
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Mot de passe</label>
                                <input v-model="userForm.password" type="password" required
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>

                            <div class="pt-4 flex gap-3">
                                <button type="button" @click="showAddUserModal = false"
                                    class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                    Annuler
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors shadow-md">
                                    {{ isEditing ? 'Mettre à jour' : 'Créer le compte' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div
                    class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-wrap gap-4 items-center">
                    <div class="relative flex-1 min-w-[200px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" placeholder="Rechercher un utilisateur..."
                            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50">
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Service / Nom</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Email</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Rôle
                                </th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Statut</th>
                                <th
                                    class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="user in props.users.data" :key="user.id"
                                class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-800">{{ user.service?.ser_nom || 'Sans Service' }}</span>
                                        <span class="text-xs text-slate-500">{{ user.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ user.email }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="['px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide', getRoleClass(user.role)]">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                                        Actif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button @click="openEditModal(user)"
                                            class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button @click="deleteUser(user.id)"
                                            class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex flex-col items-center gap-2">
                        <div class="flex items-center gap-1">
                            <Link v-for="(link, k) in props.users.links" :key="k" :href="link.url || '#'" v-html="link.label"
                                class="px-3 py-1 text-sm rounded transition-all"
                                :class="{'bg-teal-600 text-white font-bold': link.active, 'text-slate-400 hover:text-teal-600': !link.active && link.url, 'text-slate-300 cursor-not-allowed': !link.url}" />
                        </div>
                        <span class="text-xs text-slate-500">{{ props.users.from }}-{{ props.users.to }} sur {{ props.users.total }} utilisateurs</span>
                    </div>

                </div>
            </main>
        </div>
    </div>
</template>
