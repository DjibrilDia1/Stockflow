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
    { name: 'Tableau de bord', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
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
                    href="#"
                    :class="[
                        currentPage === item.name
                            ? 'bg-teal-600 text-white shadow-lg shadow-teal-600/30'
                            : 'text-slate-300 hover:bg-slate-700/50 hover:text-white',
                        'group flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200'
                    ]"
                    @click="currentPage = item.name"
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    {{ item.name }}
                </Link>
            </nav>

            <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-slate-700/50">
                <button @click="logout" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-red-900/30 hover:text-red-400 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            </header>

            <main class="p-8 space-y-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 animate-fade-in">
    <div>
        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Dashboard</h2>
        <p class="text-slate-500 text-sm">Vue d'ensemble des stocks</p>
    </div>

    <div class="flex items-center gap-3">
        <button 
            @click="handleNewEntry"
            class="flex items-center gap-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold shadow-sm transition-all active:scale-95"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>Nouvelle entrée</span>
        </button>

        <button 
            @click="handleNewExit"
            class="flex items-center gap-2 px-4 py-2.5 bg-white border border-teal-600 text-teal-600 hover:bg-teal-50 rounded-lg font-semibold shadow-sm transition-all active:scale-95"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
            </svg>
            <span>Nouvelle sortie</span>
        </button>
    </div>
</div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="card-hover bg-white rounded-xl p-6 shadow-md border border-slate-100 animate-fade-in" style="animation-delay: 0.1s">
                        <h3 class="text-sm font-medium text-slate-500 mb-3">Articles sous seuil</h3>
                        <div class="flex items-end justify-between">
                            <div>
                                <div class="text-4xl font-bold text-red-600 mb-1">{{ stats.underThreshold }}</div>
                                <span class="px-2 py-1 text-xs font-medium text-red-600 bg-red-50 rounded-md">En rupture</span>
                            </div>
                            <svg class="w-10 h-10 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                    </div>

                    <div class="card-hover bg-white rounded-xl p-6 shadow-md border border-slate-100 animate-fade-in" style="animation-delay: 0.2s">
                        <h3 class="text-sm font-medium text-slate-500 mb-3">Total articles</h3>
                        <div class="flex items-end justify-between">
                            <div class="text-2xl font-bold text-slate-800">{{ stats.totalArticles }}</div>
                            <svg class="w-10 h-10 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
                        </div>
                    </div>
                    <div class="card-hover bg-white rounded-xl p-6 shadow-md border border-slate-100 animate-fade-in" style="animation-delay: 0.2s">
                        <h3 class="text-sm font-medium text-slate-500 mb-3">Mouvements</h3>
                        <div class="flex items-end justify-between">
                            <div class="text-2xl font-bold text-slate-800">{{ (stats.movements) }}</div>
                            <svg class="w-10 h-10 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"> </svg>
                        </div>
                    </div>

                    <div class="card-hover bg-white rounded-xl p-6 shadow-md border border-slate-100 animate-fade-in" style="animation-delay: 0.2s">
                        <h3 class="text-sm font-medium text-slate-500 mb-3">Valeurs stocks</h3>
                        <div class="flex items-end justify-between">
                            <div class="text-2xl font-bold text-slate-800">{{ formatCurrency(stats.stockValue) }}</div>
                            <svg class="w-10 h-10 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    </div>  

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
    
    

    <div class="bg-white rounded-xl shadow-md border border-slate-100 flex flex-col h-full overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <h3 class="text-lg font-semibold text-slate-800">Alertes Stock</h3>
    </div>
    
    <div class="divide-y divide-slate-100 flex-1 flex flex-col">
        <div v-for="alert in stockAlerts" 
             :key="alert.id" 
             class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition-colors flex-1">
            <div class="flex-1 min-w-0 pr-4">
                <p class="text-sm font-bold text-slate-800 truncate">{{ alert.product }}</p>
                <p class="text-xs text-slate-500 truncate">{{ alert.location }}</p>
            </div>
            <div class="flex-shrink-0">
                <span :class="['px-3 py-1.5 rounded-lg text-sm font-bold text-white min-w-[75px] inline-block text-center shadow-sm', getAlertBadgeClass(alert.current, alert.threshold)]">
                    {{ alert.current }} / {{ alert.threshold }}
                </span>
            </div>
        </div>
    </div>
</div>

    <div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden self-start flex flex-col">
    <div class="px-6 py-4 border-b border-slate-100">
        <h3 class="text-lg font-semibold text-slate-800">Top 5 articles consommés</h3>
    </div>
    
    <div class="p-6 space-y-6">
        <div v-for="article in topArticles" :key="article.name">
            <div class="flex justify-between text-sm mb-2">
                <span class="font-medium text-slate-700">{{ article.name }}</span>
                <span class="text-teal-600 font-bold">{{ article.value }}%</span>
            </div>
            
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div 
                    class="bg-teal-600 h-full progress-bar transition-all duration-1000" 
                    :style="{ width: article.value + '%' }"
                ></div>
            </div>
        </div>
    </div>

    <div class="p-4 mt-auto border-t border-slate-50">
        <button class="w-full py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition-colors flex items-center justify-center gap-2">
            Voir rapports
        </button>
    </div>
</div>


    

</div>

                    
                <div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-800">Derniers Mouvements</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Article</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Quantité</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Entrepot</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Service</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="m in recentMovements" :key="m.id" class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ m.date }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ m.article }}</td>
                                    <td class="px-6 py-4">
                                        <span :class="['px-3 py-1 rounded-md text-xs font-semibold', getMovementTypeClass(m.type)]">
                                            {{ m.type }}
                                        </span>
                                    </td>
                                    <td :class="['px-6 py-4 text-sm font-bold', m.quantity < 0 ? 'text-red-600' : 'text-teal-600']">
                                        {{ m.quantity > 0 ? '+' : '' }}{{ m.quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ m.entrepot }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ m.service }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

.progress-bar {
    animation: growWidth 1s ease-out;
}

@keyframes growWidth {
    from { width: 0; }
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}
::-webkit-scrollbar-track {
    background: #f1f5f9;
}
::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
</style>