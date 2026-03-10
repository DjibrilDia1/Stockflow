<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Responsable');

const menu = [
    { name: 'Tableau de bord', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', route: 'responsable.dashboard' },
    { name: 'Rapports', route: 'responsable.rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
];

const stats = [
    { label: 'Demandes en attente de validation', value: 7 },
    { label: 'Demandes validees cette semaine', value: 12 },
    { label: 'Demandes rejetees cette semaine', value: 2 },
];

const pending = [
    { id: 1, ref: 'DR-2026-023', service: 'Service RH', qty: 15, date: '12/02/2026' },
    { id: 2, ref: 'DR-2026-022', service: 'Service Finance', qty: 24, date: '12/02/2026' },
    { id: 3, ref: 'DR-2026-021', service: 'Service Pedagogique', qty: 8, date: '11/02/2026' },
];

const logout = () => {
    if (confirm('Déconnexion ?')) router.post(route('logout'));
};

const props = defineProps({
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


const getAlertBadgeClass = (current, threshold) => {
    const ratio = current / threshold;
    // Moins de 20% du seuil = Rouge critique
    if (ratio <= 0.2) return 'bg-red-500';
    // Entre 20% et 40% = Orange attention
    return 'bg-amber-600';
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
                <Link v-for="item in menu" :key="item.name" :href="item.route ? route(item.route) : '#'" :class="[
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
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Tableau de bord Responsable</h2>
                    <p class="text-sm text-slate-500">Validation et supervision des demandes</p>
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
                <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <article v-for="card in stats" :key="card.label"
                        class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                        <p class="text-sm text-slate-500">{{ card.label }}</p>
                        <p class="text-3xl font-bold text-slate-900 mt-2">{{ card.value }}</p>
                    </article>
                </section>

                <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-800">Demandes en attente</h3>
                    </div>

                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Reference
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Service
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Quantite
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="item in pending" :key="item.id" class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-semibold text-blue-600">{{ item.ref }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ item.service }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ item.qty }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ item.date }}</td>
                            </tr>
                        </tbody>
                    </table>
                </section>
                <div class="bg-white rounded-xl shadow-md border border-slate-100 flex flex-col h-full overflow-hidden">
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
            </main>
        </div>
    </div>
</template>
