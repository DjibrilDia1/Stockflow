<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

// Données fictives des utilisateurs (basées sur l'image)
const utilisateurs = ref([
    { 
        id: 1, 
        nom: 'Amadou Diallo', 
        initials: 'AM',
        email: 'a.diallo@stockflow.sn', 
        role: 'Gestionnaire', 
        roleClass: 'bg-teal-100 text-teal-700',
        service: 'Logistique', 
        date: '15/01/2024', 
        statut: 'Actif' 
    },
    { 
        id: 2, 
        nom: 'Fatou Sarr', 
        initials: 'FS',
        email: 'F.Sarr@stockflow.sn', 
        role: 'Demandeur', 
        roleClass: 'bg-blue-100 text-blue-700',
        service: 'RH', 
        date: '15/01/2024', 
        statut: 'Actif' 
    },
    { 
        id: 3, 
        nom: 'Ibrahim Sow', 
        initials: 'IS',
        email: 'I.Sow@stockflow.sn', 
        role: 'Responsable', 
        roleClass: 'bg-purple-100 text-purple-700',
        service: 'Direction', 
        date: '15/01/2024', 
        statut: 'Actif' 
    },
]);

const navigation = [
    { name: 'Tableau de bord', route: 'dashboardtest', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', route: 'articles.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', route: 'mouvements.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', route: 'demandes.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports', route: 'rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0_8 -8' },
    { name: 'Utilisateur',route:'utilisateurs.index', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { name: 'Paramètres', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
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
            </div>
            <nav class="px-3 py-6 space-y-1.5 flex-1">
                <Link v-for="item in navigation" :key="item.name" :href="item.route ? route(item.route) : '#'"
                    :class="[item.route && route().current(item.route) ? 'bg-teal-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700 hover:text-white', 'group flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all']">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" /></svg>
                    {{ item.name }}
                </Link>
            </nav>
            <div class="p-4 border-t border-slate-700/50">
                <button @click="logout" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-400 hover:bg-red-500/10 hover:text-red-400 rounded-lg transition-all group">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Déconnexion
                </button>
            </div>
        </aside>

        <div class="ml-52 flex-1">
            <header class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4 text-slate-500">
                    <Link :href="route('dashboardtest')" class="hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </Link>
                    <span class="font-medium">Utilisateurs</span>
                </div>
                <div class="flex items-center gap-2 text-slate-700">
                    <span class="text-sm font-medium">Gestionnaire de compte</span>
                    <div class="w-9 h-9 bg-slate-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                </div>
            </header>

            <main class="p-8 space-y-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Gestion des utilisateurs</h2>
                        <p class="text-slate-500 text-sm">Gérer les comptes et les accès du systèmes</p>
                    </div>
                    <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Ajouter utilisateur
                    </button>
                </div>

                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-wrap gap-4 items-center">
                    <div class="relative flex-1 min-w-[200px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input type="text" placeholder="Rechercher un utilisateur..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>
                    <select class="bg-slate-50 border border-slate-200 rounded-lg px-10 py-2 text-sm outline-none">
                        <option>Rôle tous</option>
                    </select>
                    <select class="bg-slate-50 border border-slate-200 rounded-lg px-10 py-2 text-sm outline-none">
                        <option>Service Tous</option>
                    </select>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50">
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nom complet</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Rôle</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date de création</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="user in utilisateurs" :key="user.id" class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600 border border-slate-200">
                                            {{ user.initials }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-700">{{ user.nom }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ user.email }}</td>
                                <td class="px-6 py-4">
                                    <span :class="['px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide', user.roleClass]">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700 font-medium">{{ user.service }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ user.date }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                                        {{ user.statut }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                        <button class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex flex-col items-center gap-3">
                        <div class="px-8 py-5 flex flex-col items-center gap-2 bg-white border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <button class="text-slate-400 hover:text-teal-600 font-bold">&lt;</button>
                            <button class="w-8 h-8 bg-teal-600 text-white rounded flex items-center justify-center text-sm font-bold">1</button>
                            <button class="text-slate-400 hover:text-teal-600 font-bold">&gt;</button>
                        </div>
                        <span class="text-xs text-slate-500">1-3 Sur 3</span>
                    </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>