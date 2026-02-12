<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Responsable');

const menu = [
    { name: 'Dashboard', route: 'responsable.dashboard' },
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
    if (confirm('Deconnexion ?')) {
        router.post(route('logout'));
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <aside class="fixed left-0 top-0 h-screen w-64 bg-slate-800 shadow-2xl z-50 flex flex-col">
            <div class="px-6 py-6 border-b border-slate-700/50">
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    <span class="text-blue-400">Stock</span><span class="text-teal-400">Flow</span>
                </h1>
                <p class="text-xs text-slate-300 mt-2">Espace responsable</p>
            </div>

            <nav class="px-3 py-6 space-y-1.5 flex-1 overflow-y-auto">
                <Link
                    v-for="item in menu"
                    :key="item.name"
                    :href="route(item.route)"
                    :class="[
                        route().current(item.route)
                            ? 'bg-teal-600 text-white shadow-lg'
                            : 'text-slate-300 hover:bg-slate-700 hover:text-white',
                        'flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all'
                    ]"
                >
                    {{ item.name }}
                </Link>
            </nav>

            <div class="p-4 border-t border-slate-700/50">
                <button
                    @click="logout"
                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-400 hover:bg-red-500/10 hover:text-red-400 rounded-lg transition-all"
                >
                    Deconnexion
                </button>
            </div>
        </aside>

        <div class="ml-64 flex-1">
            <header class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Dashboard Responsable</h2>
                    <p class="text-sm text-slate-500">Validation et supervision des demandes</p>
                </div>
                <div class="text-sm font-medium text-slate-700">{{ userName }}</div>
            </header>

            <main class="p-8 space-y-6">
                <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <article v-for="card in stats" :key="card.label" class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
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
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Quantite</th>
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
            </main>
        </div>
    </div>
</template>
