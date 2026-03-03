<script setup>
import { ref } from 'vue';
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Responsable');

const recentMovements = ref([
    { id: 1, date: '03/03/2026 14:30', article: 'Papier A4', type: 'Entrée', quantite: +50, auteur: 'Jean Dupont', depot: 'Magasin Central' },
    { id: 2, date: '03/03/2026 11:15', article: 'Stylos Noirs', type: 'Sortie', quantite: -10, auteur: 'Marie Faye', depot: 'Direction' },
    { id: 3, date: '02/03/2026 16:45', article: 'Cartouches HP', type: 'Transfert', quantite: -5, auteur: 'Abdou Kane', depot: 'Bâtiment B' },
    { id: 4, date: '02/03/2026 09:00', article: 'Ordinateur Dell', type: 'Entrée', quantite: +2, auteur: 'Jean Dupont', depot: 'Stock Info' },
    { id: 5, date: '01/03/2026 15:20', article: 'Gel Hydro', type: 'Ajustement', quantite: -2, auteur: 'Admin', depot: 'Magasin Central' },
]);

const getTypeClass = (type) => {
    switch (type) {
        case 'Entrée': return 'text-emerald-600 bg-emerald-50';
        case 'Sortie': return 'text-red-600 bg-red-50';
        case 'Transfert': return 'text-blue-600 bg-blue-50';
        default: return 'text-slate-600 bg-slate-50';
    }
};

const getAlertBadgeClass = (current, threshold) => {
    const ratio = current / threshold;
    // Moins de 20% du seuil = Rouge critique
    if (ratio <= 0.2) return 'bg-red-500';
    // Entre 20% et 40% = Orange attention
    return 'bg-amber-600';
};
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

const monthlyData = [
    { month: 'Jan', in: 45, out: 30 },
    { month: 'Fév', in: 52, out: 40 },
    { month: 'Mar', in: 48, out: 45 },
    { month: 'Avr', in: 70, out: 50 },
    { month: 'Mai', in: 60, out: 55 },
    { month: 'Juin', in: 85, out: 60 },
];

// Données pour le camembert (Catégories)
const categories = [
    { name: 'Fournitures', value: 40, color: '#0d9488' }, // Teal-600
    { name: 'Mobilier', value: 25, color: '#3b82f6' },    // Blue-500
    { name: 'Informatique', value: 20, color: '#f59e0b' }, // Amber-500
    { name: 'Autre', value: 15, color: '#94a3b8' },       // Slate-400
];


const activeView = ref('grid'); // Peut être 'grid' ou 'kpi'

// Données pour le Dashboard KPI
const stats = [
    { label: 'Valeur totale du stock', value: '12.450.000 FCFA', trend: '+2.5%', trendClass: 'text-emerald-500' },
    { label: 'Taux de rotation', value: '4.2', trend: '+0.8', trendClass: 'text-emerald-500' },
    { label: 'Articles en rupture', value: '8', trend: '-2', trendClass: 'text-red-500' },
    { label: 'Commandes en attente', value: '15', trend: 'Stable', trendClass: 'text-slate-400' },
];

// Données fictives
const demandes = ref([
    { id: 1, ref: 'DREQ-001', demandeur: 'Entrée', entrepot: 'Fournitures', date: '3/04/2024', statut: 'Brouillon', statutClass: 'bg-amber-100 text-amber-600', detail: '' },
    { id: 2, ref: 'DREQ-002', demandeur: 'Transfert', entrepot: 'Fournitures', date: '13/04/2024', statut: 'Validée', statutClass: 'bg-teal-600 text-white', detail: '+ Stylos noirs x 20' },
    { id: 3, ref: 'DREQ-003', demandeur: 'Ajustement', entrepot: 'Mobilier', date: '15/04/2024', statut: 'Servie', statutClass: 'bg-emerald-500 text-white', detail: '+ Cartouches d’encre x 10' },
    { id: 4, ref: 'DREQ-004', demandeur: 'Sortie', entrepot: 'Mobilier', date: '15/04/2024', statut: 'Rejetée', statutClass: 'bg-red-500 text-white', detail: '+ Besoin non justifiée' },
]);

const navigation = [
    { name: 'Tableau de bord', route: 'responsable.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Rapports',route:'responsable.rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },

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
                <p class="text-xs text-slate-300 mt-2">Espace Responsable</p>
            </div>

            <nav class="px-3 py-6 space-y-1.5 flex-1">
                <Link v-for="item in navigation" :key="item.name" :href="item.route ? route(item.route) : '#'" :class="[
                    item.route && route().current(item.route)
                        ? 'bg-teal-600 text-white shadow-lg'
                        : 'text-slate-300 hover:bg-slate-700 hover:text-white',
                    'group flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all'
                ]">
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
                    <Link :href="route('gestionnaire.demandes.index')" class="hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <span class="font-medium">Rapports</span>
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

                        <div v-if="activeView === 'kpi'" @click="activeView = 'grid'"
                            class="flex items-center gap-2 text-teal-600 cursor-pointer hover:underline mb-2 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Retour aux rapports
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800">
                            {{ activeView === 'grid' ? 'Gérer vos rapports' : 'Tableau de bord KPI' }}
                        </h2>
                        <p class="text-slate-500 text-sm">
                            {{ activeView === 'grid' ? `Analyser la consommation et l'état de vos stocks` : 'Indicateurs de performance en temps réel' }}
                        </p>
                    </div>

                    <button v-if="activeView === 'grid'"
                        class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Générer rapport
                    </button>
                </div>

                <div v-if="activeView === 'grid'" class="space-y-6">


                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mt-8">
                        <div
                            class="bg-white rounded-xl shadow-md border border-slate-100 flex flex-col h-full overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100">
                                <h3 class="text-lg font-semibold text-slate-800">Alertes Stock</h3>
                            </div>

                            <div class="divide-y divide-slate-100 flex-1 flex flex-col">
                                <div v-for="alert in stockAlerts" :key="alert.id"
                                    class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition-colors flex-1">
                                    <div class="flex-1 min-w-0 pr-4">
                                        <p class="text-sm font-bold text-slate-800 truncate">{{ alert.product }}</p>
                                        <p class="text-xs text-slate-500 truncate">{{ alert.location }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span
                                            :class="['px-3 py-1.5 rounded-lg text-sm font-bold text-white min-w-[75px] inline-block text-center shadow-sm', getAlertBadgeClass(alert.current, alert.threshold)]">
                                            {{ alert.current }} / {{ alert.threshold }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                                <h3 class="font-bold text-slate-800">Journal détaillé des mouvements</h3>
                                <button class="text-teal-600 text-sm font-semibold hover:underline">Exporter en
                                    Excel</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50/50">
                                            <th
                                                class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Date & Heure</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Article</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Type</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Quantité</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Dépôt/Destination</th>
                                            <th
                                                class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Auteur</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr v-for="mvt in recentMovements" :key="mvt.id"
                                            class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-slate-600">{{ mvt.date }}</td>
                                            <td class="px-6 py-4 text-sm font-bold text-slate-800">{{ mvt.article }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span
                                                    :class="['px-2.5 py-1 rounded-full text-xs font-bold', getTypeClass(mvt.type)]">
                                                    {{ mvt.type }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-mono font-bold"
                                                :class="mvt.quantite > 0 ? 'text-emerald-600' : 'text-red-600'">
                                                {{ mvt.quantite > 0 ? '+' : '' }}{{ mvt.quantite }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-600">{{ mvt.depot }}</td>
                                            <td class="px-6 py-4 text-sm text-slate-600">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold">
                                                        {{mvt.auteur.split(' ').map(n => n[0]).join('')}}
                                                    </div>
                                                    {{ mvt.auteur }}
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-center">
                                <button
                                    class="text-sm font-medium text-slate-500 hover:text-teal-600 transition-colors">
                                    Voir tout l'historique
                                </button>
                            </div>
                        </div>
                        <div @click="activeView = 'kpi'"
                            class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between hover:shadow-md transition-shadow cursor-pointer group ring-2 ring-transparent hover:ring-orange-500/20">
                            <div class="flex items-center gap-5">
                                <div
                                    class="w-16 h-16 bg-orange-50 rounded-lg flex items-center justify-center border border-orange-100">
                                    <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M16 8v8m-4-5v5m-4-2v2" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-tight">Tableau de
                                        bord KPI</h3>
                                    <p class="text-sm text-slate-700 font-bold mt-1">Indicateurs de performance du stock
                                    </p>
                                </div>
                            </div>
                            <div class="text-slate-400 group-hover:text-orange-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div v-for="stat in stats" :key="stat.label"
                            class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                            <p class="text-slate-500 text-sm font-medium">{{ stat.label }}</p>
                            <div class="flex items-end justify-between mt-2">
                                <h4 class="text-2xl font-bold text-slate-800">{{ stat.value }}</h4>
                                <span :class="['text-xs font-bold px-2 py-1 rounded-lg bg-slate-50', stat.trendClass]">
                                    {{ stat.trend }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <h3 class="font-bold text-slate-800 mb-4">Flux Mensuel (Entrées vs Sorties)</h3>
                            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                                <div class="flex justify-between items-center mb-6">
                                    <div class="flex gap-4 text-xs">
                                        <span class="flex items-center gap-1"><span
                                                class="w-3 h-3 bg-teal-500 rounded-full"></span> Entrées</span>
                                        <span class="flex items-center gap-1"><span
                                                class="w-3 h-3 bg-slate-300 rounded-full"></span> Sorties</span>
                                    </div>
                                </div>
                                <div class="h-64 w-full relative pt-4">
                                    <svg viewBox="0 0 500 200" class="w-full h-full overflow-visible">
                                        <line x1="0" y1="0" x2="500" y2="0" stroke="#f1f5f9" stroke-width="1" />
                                        <line x1="0" y1="100" x2="500" y2="100" stroke="#f1f5f9" stroke-width="1" />
                                        <line x1="0" y1="200" x2="500" y2="200" stroke="#f1f5f9" stroke-width="1" />

                                        <polyline fill="none" stroke="#0d9488" stroke-width="3" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            points="0,150 100,120 200,130 300,60 400,90 500,20" />

                                        <polyline fill="none" stroke="#cbd5e1" stroke-width="3" stroke-dasharray="8"
                                            points="0,180 100,160 200,155 300,140 400,135 500,120" />
                                    </svg>
                                    <div class="flex justify-between mt-4 text-[10px] text-slate-400 font-medium px-1">
                                        <span v-for="d in monthlyData" :key="d.month">{{ d.month }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <h3 class="font-bold text-slate-800 mb-4">Répartition par Catégorie</h3>
                            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                                <h3 class="font-bold text-slate-800 mb-6">Répartition par Catégorie</h3>
                                <div class="flex items-center justify-between h-64">
                                    <div class="relative w-48 h-48">
                                        <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
                                            <circle cx="18" cy="18" r="15.9" fill="transparent" stroke="#f1f5f9"
                                                stroke-width="3"></circle>
                                            <circle cx="18" cy="18" r="15.9" fill="transparent" stroke="#0d9488"
                                                stroke-width="3" stroke-dasharray="40 100"></circle>
                                            <circle cx="18" cy="18" r="15.9" fill="transparent" stroke="#3b82f6"
                                                stroke-width="3" stroke-dasharray="25 100" stroke-dashoffset="-40">
                                            </circle>
                                        </svg>
                                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                                            <span class="text-2xl font-bold text-slate-800">100%</span>
                                            <span class="text-[10px] text-slate-400 uppercase">Articles</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-3">
                                        <div v-for="cat in categories" :key="cat.name" class="flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full" :style="{ backgroundColor: cat.color }">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-semibold text-slate-700">{{ cat.name }}</span>
                                                <span class="text-[10px] text-slate-400">{{ cat.value }}% du
                                                    stock</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </main>

        </div>

    </div>
</template>