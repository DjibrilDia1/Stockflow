<script setup>
import GestionnaireLayout from '@/Layouts/GestionnaireLayout.vue';
defineOptions({ layout: GestionnaireLayout });

import { ref, watch, computed, onMounted } from 'vue';
import { Link, router, usePage, Head } from '@inertiajs/vue3';
import Toast from '@/Components/Toast.vue';
import { Bar, Pie } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const props = defineProps({
    activeTab: String,
    filters: Object,
    lowStockData: Array,
    movementsData: Object,
    kpiData: Object,
    entrepots: Array,
    articles: Array,
});

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Gestionnaire');
const currentTab = ref(props.activeTab || 'low-stock');

// Filters
const filterForm = ref({
    article: props.filters.article || '',
    entrepot: props.filters.entrepot || '',
    type: props.filters.type || '',
    date_start: props.filters.date_start || '',
    date_end: props.filters.date_end || '',
});

let timeout = null;
const applyFilters = () => {
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('gestionnaire.rapports.index'), {
            tab: 'movements',
            ...filterForm.value
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    }, 500);
};

watch(filterForm, () => {
    if (currentTab.value === 'movements') {
        applyFilters();
    }
}, { deep: true });

const changeTab = (tab) => {
    currentTab.value = tab;
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.replaceState({}, '', url);
};

const exportExcel = () => {
    const params = new URLSearchParams({ ...filterForm.value }).toString();
    window.location.href = route('gestionnaire.rapports.export') + '?' + params;
};

const getTypeClass = (type) => {
    switch (type) {
        case 'IN': return 'text-emerald-600 bg-emerald-50';
        case 'OUT': return 'text-red-600 bg-red-50';
        case 'TRANSFER': return 'text-blue-600 bg-blue-50';
        case 'ADJUST': return 'text-amber-600 bg-amber-50';
        default: return 'text-slate-600 bg-slate-50';
    }
};

// Chart Data
const barChartData = computed(() => ({
    labels: ['Total Produits', 'Stock Faible', 'Ruptures'],
    datasets: [
        {
            label: 'Nombre d\'articles',
            backgroundColor: ['#3b82f6', '#f59e0b', '#ef4444'],
            data: [props.kpiData.totalArticles, props.kpiData.lowStock, props.kpiData.outOfStock]
        }
    ]
}));

const barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: true, position: 'bottom' }
    }
};

const pieChartData = computed(() => ({
    labels: ['Disponible', 'Stock Faible', 'Rupture'],
    datasets: [
        {
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
            data: [props.kpiData.available, props.kpiData.lowStock, props.kpiData.outOfStock]
        }
    ]
}));

const pieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: true, position: 'bottom' }
    }
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

const logout = () => { if (confirm('Déconnexion ?')) router.post(route('logout')); };
</script>

<template>
<main class="p-8">
                <div class="flex border-b border-slate-200 mb-8 overflow-x-auto">
                    <button @click="changeTab('low-stock')" :class="['px-6 py-3 font-medium text-sm transition-all border-b-2', currentTab === 'low-stock' ? 'border-teal-600 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700']">Alerte Stock Seuil Bas</button>
                    <button @click="changeTab('movements')" :class="['px-6 py-3 font-medium text-sm transition-all border-b-2', currentTab === 'movements' ? 'border-teal-600 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700']">Journal des Mouvements</button>
                    <button @click="changeTab('kpi')" :class="['px-6 py-3 font-medium text-sm transition-all border-b-2', currentTab === 'kpi' ? 'border-teal-600 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700']">Tableau de bord KPI</button>
                </div>

                <div v-if="currentTab === 'low-stock'" class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50"><h3 class="text-lg font-semibold text-slate-800">Articles sous seuil d'alerte</h3></div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-slate-50/30 text-xs font-bold text-slate-500 uppercase"><th class="px-6 py-4">Référence</th><th class="px-6 py-4">Article</th><th class="px-6 py-4">Entrepôt</th><th class="px-6 py-4">Stock Actuel</th><th class="px-6 py-4">Seuil Bas</th><th class="px-6 py-4">Sécurité</th><th class="px-6 py-4">Statut</th></tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="item in lowStockData" :key="item.id" class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-slate-600">{{ item.reference }}</td>
                                        <td class="px-6 py-4 text-sm font-bold text-slate-800">{{ item.nom }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ item.entrepot }}</td>
                                        <td class="px-6 py-4 text-sm font-bold" :class="item.stock_actuel < item.stock_securite ? 'text-red-600' : 'text-amber-600'">{{ item.stock_actuel }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-500">{{ item.seuil_bas }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-500">{{ item.stock_securite }}</td>
                                        <td class="px-6 py-4">
                                            <span :class="['px-2 py-1 rounded-full text-xs font-bold', item.statut === 'Critique' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700']">{{ item.statut }}</span>
                                        </td>
                                    </tr>
                                    <tr v-if="lowStockData.length === 0"><td colspan="7" class="px-6 py-12 text-center text-slate-500">Aucun article sous le seuil d'alerte.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div v-if="currentTab === 'movements'" class="space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 grid grid-cols-1 md:grid-cols-5 gap-4">
                        <select v-model="filterForm.article" class="rounded-lg border-slate-200 text-sm">
                            <option value="">Tous les articles</option>
                            <option v-for="art in articles" :key="art.art_id" :value="art.art_id">{{ art.art_nom }}</option>
                        </select>
                        <select v-model="filterForm.entrepot" class="rounded-lg border-slate-200 text-sm">
                            <option value="">Tous les entrepôts</option>
                            <option v-for="ent in entrepots" :key="ent.ent_id" :value="ent.ent_id">{{ ent.ent_nom }}</option>
                        </select>
                        <select v-model="filterForm.type" class="rounded-lg border-slate-200 text-sm">
                            <option value="">Tous les types</option>
                            <option value="IN">Entrée</option><option value="OUT">Sortie</option><option value="TRANSFER">Transfert</option><option value="ADJUST">Ajustement</option>
                        </select>
                        <input type="date" v-model="filterForm.date_start" class="rounded-lg border-slate-200 text-sm">
                        <input type="date" v-model="filterForm.date_end" class="rounded-lg border-slate-200 text-sm">
                    </div>
                    <div class="flex justify-end"><button @click="exportExcel" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-all shadow-sm">Exporter Excel</button></div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead><tr class="bg-slate-50/50 text-xs font-bold text-slate-500 uppercase"><th class="px-6 py-4">Date</th><th class="px-6 py-4">Article</th><th class="px-6 py-4">Entrepôt</th><th class="px-6 py-4">Type</th><th class="px-6 py-4">Quantité</th><th class="px-6 py-4">Source/Dest.</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="mvt in movementsData.data" :key="mvt.mvs_id" class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ new Date(mvt.mvs_date_mouvement).toLocaleString() }}</td>
                                        <td class="px-6 py-4 text-sm font-bold text-slate-800">{{ mvt.item.art_nom }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ mvt.warehouse.ent_nom }}</td>
                                        <td class="px-6 py-4"><span :class="['px-2 py-1 rounded-full text-[10px] font-bold', getTypeClass(mvt.mvs_type)]">{{ mvt.mvs_type }}</span></td>
                                        <td class="px-6 py-4 text-sm font-bold" :class="{
                                            'text-emerald-600': mvt.mvs_type === 'IN',
                                            'text-red-600': mvt.mvs_type === 'OUT',
                                            'text-blue-600': mvt.mvs_type === 'TRANSFER',
                                            'text-amber-600': mvt.mvs_type === 'ADJUST',
                                        }">
                                            {{ mvt.mvs_quantite }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500">{{ mvt.supplier?.fou_nom || mvt.service?.ser_nom || '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                            <div class="text-sm text-slate-500">Affichage de {{ movementsData.from || 0 }} à {{ movementsData.to || 0 }} sur {{ movementsData.total }} mouvements</div>
                            <div class="flex gap-2">
                                <Link v-for="link in movementsData.links" :key="link.label" 
                                    :href="link.url ? link.url + '&tab=movements' : '#'" 
                                    v-html="link.label" 
                                    :class="['px-3 py-1 rounded text-sm transition-all', link.active ? 'bg-teal-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100', !link.url ? 'opacity-50 cursor-not-allowed' : '']" 
                                    preserve-scroll preserve-state />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="currentTab === 'kpi'" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100"><p class="text-slate-500 text-sm font-medium">Articles sous seuil</p><h4 class="text-3xl font-bold text-slate-800 mt-2">{{ kpiData.underThreshold }}</h4></div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100"><p class="text-slate-500 text-sm font-medium">Mouvements du mois</p><h4 class="text-3xl font-bold text-slate-800 mt-2">{{ kpiData.movementsThisMonth }}</h4></div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100"><p class="text-slate-500 text-sm font-medium">Article plus consommé</p><h4 class="text-lg font-bold text-slate-800 mt-2 truncate">{{ kpiData.mostConsumedItem?.name || '-' }}</h4><div class="mt-1 text-sm text-slate-500">Qté: {{ Math.abs(kpiData.mostConsumedItem?.quantity || 0) }}</div></div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100"><p class="text-slate-500 text-sm font-medium">Total articles</p><h4 class="text-3xl font-bold text-slate-800 mt-2">{{ kpiData.totalArticles }}</h4></div>
                    </div>

                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                            <h3 class="text-lg font-semibold text-slate-800 mb-4">Statistiques Articles</h3>
                            <div class="h-64">
                                <Bar :data="barChartData" :options="barChartOptions" />
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                            <h3 class="text-lg font-semibold text-slate-800 mb-4">Répartition du Stock</h3>
                            <div class="h-64">
                                <Pie :data="pieChartData" :options="pieChartOptions" />
                            </div>
                        </div>
                    </div>

                    <!-- Article Lists by Status -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
                        <!-- Out of Stock -->
                        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="px-4 py-3 bg-red-50 border-b border-red-100 flex justify-between items-center">
                                <h4 class="text-sm font-bold text-red-700 uppercase">Articles en Rupture</h4>
                                <span class="bg-red-600 text-white text-[10px] px-2 py-0.5 rounded-full">{{ kpiData.itemsByStatus.out.length }}</span>
                            </div>
                            <div class="max-h-60 overflow-y-auto p-2">
                                <table class="w-full text-xs">
                                    <thead class="text-slate-500 border-b">
                                        <tr><th class="text-left py-2 px-2">Article</th><th class="text-right py-2 px-2">Entrepôt</th></tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        <tr v-for="(item, idx) in kpiData.itemsByStatus.out" :key="idx" class="hover:bg-slate-50">
                                            <td class="py-2 px-2 font-medium text-slate-700">{{ item.nom }}</td>
                                            <td class="py-2 px-2 text-right text-slate-500 italic">{{ item.entrepot }}</td>
                                        </tr>
                                        <tr v-if="kpiData.itemsByStatus.out.length === 0">
                                            <td colspan="2" class="py-4 text-center text-slate-400">Aucun article en rupture</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Low Stock -->
                        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="px-4 py-3 bg-amber-50 border-b border-amber-100 flex justify-between items-center">
                                <h4 class="text-sm font-bold text-amber-700 uppercase">Stock Faible</h4>
                                <span class="bg-amber-500 text-white text-[10px] px-2 py-0.5 rounded-full">{{ kpiData.itemsByStatus.low.length }}</span>
                            </div>
                            <div class="max-h-60 overflow-y-auto p-2">
                                <table class="w-full text-xs">
                                    <thead class="text-slate-500 border-b">
                                        <tr><th class="text-left py-2 px-2">Article</th><th class="text-center py-2 px-2">Qté</th><th class="text-right py-2 px-2">Entrepôt</th></tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        <tr v-for="(item, idx) in kpiData.itemsByStatus.low" :key="idx" class="hover:bg-slate-50">
                                            <td class="py-2 px-2 font-medium text-slate-700">{{ item.nom }}</td>
                                            <td class="py-2 px-2 text-center text-amber-600 font-bold">{{ item.qte }}</td>
                                            <td class="py-2 px-2 text-right text-slate-500 italic text-[10px]">{{ item.entrepot }}</td>
                                        </tr>
                                        <tr v-if="kpiData.itemsByStatus.low.length === 0">
                                            <td colspan="3" class="py-4 text-center text-slate-400">Aucun article sous seuil</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Available -->
                        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="px-4 py-3 bg-teal-50 border-b border-teal-100 flex justify-between items-center">
                                <h4 class="text-sm font-bold text-teal-700 uppercase">Articles Disponibles</h4>
                                <span class="bg-teal-600 text-white text-[10px] px-2 py-0.5 rounded-full">{{ kpiData.itemsByStatus.available.length }}</span>
                            </div>
                            <div class="max-h-60 overflow-y-auto p-2">
                                <table class="w-full text-xs">
                                    <thead class="text-slate-500 border-b">
                                        <tr><th class="text-left py-2 px-2">Article</th><th class="text-center py-2 px-2">Qté</th><th class="text-right py-2 px-2">Entrepôt</th></tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        <tr v-for="(item, idx) in kpiData.itemsByStatus.available" :key="idx" class="hover:bg-slate-50">
                                            <td class="py-2 px-2 font-medium text-slate-700">{{ item.nom }}</td>
                                            <td class="py-2 px-2 text-center text-emerald-600 font-bold">{{ item.qte }}</td>
                                            <td class="py-2 px-2 text-right text-slate-500 italic text-[10px]">{{ item.entrepot }}</td>
                                        </tr>
                                        <tr v-if="kpiData.itemsByStatus.available.length === 0">
                                            <td colspan="3" class="py-4 text-center text-slate-400">Aucun article disponible</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
</template>
