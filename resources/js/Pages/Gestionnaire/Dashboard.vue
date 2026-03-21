<script setup>
import GestionnaireLayout from '@/Layouts/GestionnaireLayout.vue';
defineOptions({ layout: GestionnaireLayout });

import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Toast from '@/Components/Toast.vue';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Gestionnaire');

const props = defineProps({
    stats: Object,
    stockAlerts: Array,
    recentMovements: Array,
    topArticles: Array,
});

const navigation = [
    { name: 'Tableau de bord', route: 'gestionnaire.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', route: 'gestionnaire.articles.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', route: 'gestionnaire.mouvements.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', route: 'gestionnaire.demandes.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports', route: 'gestionnaire.rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Utilisateurs', route: 'gestionnaire.utilisateurs.index', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { name: 'Services & Fournisseurs', route: 'gestionnaire.services-fournisseurs.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
];

const shortcuts = [
    {
        label: 'Nouvelle entrée',
        desc: 'Enregistrer un achat',
        color: 'teal',
        icon: 'M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12',
        action: () => router.visit(route('gestionnaire.mouvements.index'), { data: { type: 'IN' } })
    },
    {
        label: 'Nouvelle sortie',
        desc: 'Distribuer du stock',
        color: 'red',
        icon: 'M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4',
        action: () => router.visit(route('gestionnaire.mouvements.index'), { data: { type: 'OUT' } })
    },
    {
        label: 'Nouvel article',
        desc: 'Ajouter un produit',
        color: 'teal',
        icon: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
        route: 'gestionnaire.articles.index'
    },
    {
        label: 'Transfert',
        desc: 'Entre entrepôts',
        color: 'blue',
        icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
        route: 'gestionnaire.mouvements.index'
    },
    {
        label: 'Demandes',
        desc: 'Gérer les retraits',
        color: 'teal',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        route: 'gestionnaire.demandes.index'
    },
    {
        label: 'Rapports',
        desc: 'Voir les stats',
        color: 'violet',
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        route: 'gestionnaire.rapports.index'
    },
];

const maxTopArticleValue = computed(() => {
    if (!props.topArticles || props.topArticles.length === 0) return 1;
    return Math.max(...props.topArticles.map(a => a.value));
});

const getBarWidth = (value) => (value / maxTopArticleValue.value * 100) + '%';

const formatCurrency = (value) => new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';

const getMovementTypeLabel = (type) => ({ 'OUT': 'Sortie', 'IN': 'Entrée', 'ADJUST': 'Ajustement', 'TRANSFER': 'Transfert' }[type] || type);

const getMovementTypeClass = (type) => ({
    'OUT': 'bg-red-50 text-red-600 border border-red-100',
    'IN': 'bg-emerald-50 text-emerald-600 border border-emerald-100',
    'ADJUST': 'bg-orange-50 text-orange-600 border border-orange-100',
    'TRANSFER': 'bg-blue-50 text-blue-600 border border-blue-100',
}[type] || 'bg-slate-100 text-slate-600');

const getShortcutBg = (c) => ({ teal: 'bg-teal-50 border-teal-200 hover:bg-teal-100 hover:border-teal-300', slate: 'bg-slate-50 border-slate-200 hover:bg-slate-100 hover:border-slate-300', red: 'bg-red-50 border-red-200 hover:bg-red-100 hover:border-red-300', blue: 'bg-blue-50 border-blue-200 hover:bg-blue-100 hover:border-blue-300', violet: 'bg-violet-50 border-violet-200 hover:bg-violet-100 hover:border-violet-300' }[c]);
const getShortcutIcon = (c) => ({ teal: 'bg-teal-600', slate: 'bg-slate-700', red: 'bg-red-500', blue: 'bg-blue-500', violet: 'bg-violet-500' }[c]);
const getShortcutText = (c) => ({ teal: 'text-teal-700', slate: 'text-slate-700', red: 'text-red-700', blue: 'text-blue-700', violet: 'text-violet-700' }[c]);

const logout = () => { if (confirm('Déconnexion ?')) router.post(route('logout')); };
</script>

<template>
<main class="flex-1 p-7 space-y-7">

                <!-- Page title + actions -->
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Tableau de bord</h2>
                        <p class="text-slate-400 text-sm mt-0.5">Vue d'ensemble des stocks</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button @click="router.visit(route('gestionnaire.mouvements.index'), { data: { type: 'IN' } })"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Nouvelle entrée
                        </button>
                        <button @click="router.visit(route('gestionnaire.mouvements.index'), { data: { type: 'OUT' } })"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-teal-600 text-teal-600 hover:bg-teal-50 text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                            Nouvelle sortie
                        </button>
                    </div>
                </div>

                <!-- ── KPI Cards ── -->
                <div class="grid grid-cols-4 gap-5">
                    <!-- Articles sous seuil -->
                    <div
                        class="bg-white rounded-2xl p-5 border border-red-100 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5 group">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                </svg>
                            </div>
                            <span
                                class="text-xs font-semibold px-2 py-0.5 rounded-full bg-red-50 text-red-500 border border-red-100">Alerte</span>
                        </div>
                        <div class="text-3xl font-bold text-red-600 mb-1">{{ stats.underThreshold }}</div>
                        <p class="text-xs text-slate-500 font-medium">Articles sous seuil</p>
                    </div>

                    <!-- Total articles -->
                    <div
                        class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5 group">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center group-hover:bg-teal-100 transition-colors">
                                <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-slate-800 mb-1">{{ stats.totalArticles }}</div>
                        <p class="text-xs text-slate-500 font-medium">Total articles</p>
                    </div>

                    <!-- Mouvements -->
                    <div
                        class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5 group">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-slate-800 mb-1">{{ stats.movements }}</div>
                        <p class="text-xs text-slate-500 font-medium">Mouvements ce mois</p>
                    </div>

                    <!-- Valeur stock -->
                    <div
                        class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5 group">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center group-hover:bg-violet-100 transition-colors">
                                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-xl font-bold text-slate-800 mb-1 leading-tight">{{
                            formatCurrency(stats.stockValue) }}</div>
                        <p class="text-xs text-slate-500 font-medium">Valeur du stock</p>
                    </div>
                </div>

                <!-- ── Top 5 + Raccourcis (côte à côte) ── -->
                <div class="grid grid-cols-5 gap-5">

                    <!-- Top 5 articles consommés (3/5) -->
                    <div
                        class="col-span-3 bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h3 class="text-base font-bold text-slate-800">Top 5 articles consommés</h3>
                        </div>

                        <div class="px-6 pt-4 pb-2 flex-1">
                            <template v-if="topArticles && topArticles.length">
                                <div v-for="(article, idx) in topArticles" :key="article.name"
                                    class="flex items-center border-b border-slate-100 last:border-0">
                                    <!-- Label -->
                                    <div
                                        class="w-36 py-4 pr-4 text-sm font-medium text-slate-700 truncate flex-shrink-0">
                                        {{ article.name }}
                                    </div>
                                    <!-- Bar zone -->
                                    <div class="flex-1 py-3">
                                        <div class="h-8 bg-teal-600 rounded-r transition-all duration-1000 ease-out"
                                            :style="{ width: getBarWidth(article.value) }">
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div v-else class="flex flex-col items-center justify-center py-8 text-slate-300">
                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <p class="text-sm">Aucune donnée disponible</p>
                            </div>
                        </div>

                        <div class="px-6 py-3 flex justify-end">
                            <Link :href="route('gestionnaire.rapports.index')"
                                class="px-5 py-2 bg-teal-700 hover:bg-teal-800 text-white text-sm font-semibold rounded-lg transition-colors">
                                Voir rapports
                            </Link>
                        </div>
                    </div>

                    <!-- Raccourcis (2/5) -->
                    <div
                        class="col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2.5">
                            <div class="w-1 h-5 rounded-full bg-teal-500"></div>
                            <h3 class="text-base font-bold text-slate-800">Accès rapides</h3>
                        </div>

                        <div class="p-4 flex-1 grid grid-cols-2 gap-2.5 content-start">
                            <template v-for="sc in shortcuts" :key="sc.label">
                                <button v-if="sc.action" @click="sc.action"
                                    :class="['group flex flex-col items-center gap-2 p-3.5 rounded-xl border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm text-center', getShortcutBg(sc.color)]">
                                    <div
                                        :class="['w-8 h-8 rounded-lg flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm', getShortcutIcon(sc.color)]">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                :d="sc.icon" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p :class="['text-xs font-bold leading-tight', getShortcutText(sc.color)]">{{
                                            sc.label }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ sc.desc }}</p>
                                    </div>
                                </button>

                                <Link v-else :href="route(sc.route)"
                                    :class="['group flex flex-col items-center gap-2 p-3.5 rounded-xl border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm text-center', getShortcutBg(sc.color)]">
                                    <div
                                        :class="['w-8 h-8 rounded-lg flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm', getShortcutIcon(sc.color)]">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                :d="sc.icon" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p :class="['text-xs font-bold leading-tight', getShortcutText(sc.color)]">{{
                                            sc.label }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ sc.desc }}</p>
                                    </div>
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>


            </main>
</template>

<style scoped>
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

main>* {
    animation: fadeUp 0.4s ease-out both;
}

main>*:nth-child(1) {
    animation-delay: 0.05s;
}

main>*:nth-child(2) {
    animation-delay: 0.1s;
}

main>*:nth-child(3) {
    animation-delay: 0.15s;
}

main>*:nth-child(4) {
    animation-delay: 0.2s;
}

::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
</style>
