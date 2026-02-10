<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';

// Props reçues du contrôleur Laravel (exemple)
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            underThreshold: 2,
            totalArticles: 350,
            movements: 124,
            stockValue: 100000
        })
    },
    stockAlerts: {
        type: Array,
        default: () => [
            { id: 1, product: 'Papier A4', location: 'Magazin central', current: 2, threshold: 10 },
            { id: 2, product: 'Stylos Noirs', location: 'Central A', current: 5, threshold: 15 },
            { id: 3, product: 'Cartouches', location: 'Bâtiment B', current: 1, threshold: 5 },
            { id: 4, product: 'Nettoyage Surface', location: 'Magazin central', current: 3, threshold: 8 }
        ]
    }
});

// État local
const searchQuery = ref('');
const currentPage = ref('Tableau de bord');

const navigation = [
    { name: 'Tableau de bord', route: 'dashboardtest', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', route: 'articles.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', route: 'mouvements.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', route: 'demandes.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports',route:'rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Utilisateur',route:'utilisateurs.index', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { name: 'Paramètres', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },

];

const topArticles = [
    { name: 'Feuilles A3', value: 85 },
    { name: 'Stylos Bleus', value: 60 },
    { name: 'Cahier Spirales', value: 45 },
    { name: 'Grant Laser', value: 25 }
];

const recentMovements = [
    { id: 1, date: '24/04/2024', article: 'Papier A4', type: 'OUT', quantity: -50, warehouse: 'Campus A',entrepot:'Campus A', service: 'Service RH' },
    { id: 2, date: '23/04/2024', article: 'Stylos Noirs', type: 'IN', quantity: 100, warehouse: 'Magasin central',entrepot:'Magasin central', service: 'Achat fournisseur' },
    { id: 3, date: '22/04/2024', article: 'Toner Laser', type: 'Adjust', quantity: -5, warehouse: 'Bâtiment B',entrepot:'Bâtiment B', service: 'Correction stock' }
];

// Méthodes
const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
};

const getAlertBadgeClass = (current, threshold) => {
    const ratio = current / threshold;
    // Moins de 20% du seuil = Rouge critique
    if (ratio <= 0.2) return 'bg-red-500'; 
    // Entre 20% et 40% = Orange attention
    return 'bg-amber-600'; 
};

const getMovementTypeClass = (type) => {
    const classes = {
        'OUT': 'bg-red-100 text-red-700',
        'IN': 'bg-green-100 text-green-700',
        'Adjust': 'bg-orange-100 text-orange-700'
    };
    return classes[type] || 'bg-slate-100 text-slate-700';
};

const logout = () => {
    if (confirm('Déconnexion ?')) {
        router.post(route('logout'));
    }
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-teal-50/40">
        <aside class="fixed left-0 top-0 h-screen w-52 bg-gradient-to-b from-slate-800 to-slate-900 shadow-2xl border-r border-slate-700/50 z-50">
            <div class="px-6 py-6 border-b border-slate-700/50">
                <h1 class="text-2xl font-bold tracking-tight">
                    <span class="text-blue-400">Stock</span><span class="text-teal-400">Flow</span>
                </h1>
            </div>

            <nav class="px-3 py-6 space-y-1.5">
    <Link
        v-for="item in navigation"
        :key="item.name"
        :href="item.route ? route(item.route) : '#'"
        :class="[
            currentPage === item.name
                ? 'bg-teal-600 text-white shadow-lg shadow-teal-600/30'
                : 'text-slate-300 hover:bg-slate-700/50 hover:text-white',
            'group flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200'
        ]"
    >
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
        </svg>
        {{ item.name }}
    </Link>
</nav>

            <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-slate-700/50">
                <button @click="logout" 
            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-400 hover:bg-red-500/10 hover:text-red-400 rounded-lg transition-all group">
            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Déconnexion
        </button>
            </div>
        </aside>

        <div class="ml-52">
            <header class="bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-10 shadow-sm">
                <div class="px-8 py-4 flex items-center justify-between">
                    <div class="relative flex-1 max-w-md">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Rechercher des produits , mouvements...."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-100/80 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all"
                        />
                    </div>
                    <div class="flex items-center gap-2 text-slate-700 hover:text-teal-600 cursor-pointer group">
                        <span class="text-sm font-medium">Gestionnaire de compte</span>
                        <div class="w-9 h-9 flex items-center justify-center bg-slate-100 rounded-full group-hover:bg-teal-50 transition-colors">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
