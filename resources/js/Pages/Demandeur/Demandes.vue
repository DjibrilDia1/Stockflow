<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Demandeur');

const menu = [
    { name: 'Dashboard', route: 'demandeur.dashboard' },
    { name: 'Demandes', route: 'demandeur.demandes.index' },
    { name: 'Consultation articles', route: 'demandeur.articles.index' },
];

const requests = ref([
    { id: 1, ref: 'DR-2026-021', type: 'Retrait', article: 'Papier A4', qty: 20, date: '12/02/2026', status: 'En validation' },
    { id: 2, ref: 'DR-2026-020', type: 'Retrait', article: 'Stylos noirs', qty: 40, date: '10/02/2026', status: 'En preparation' },
    { id: 3, ref: 'DR-2026-019', type: 'Retrait', article: 'Cartouches', qty: 6, date: '08/02/2026', status: 'Prete' },
    { id: 4, ref: 'DR-2026-017', type: 'Retrait', article: 'Classeurs', qty: 8, date: '02/02/2026', status: 'Rejetee' },
]);

const statusClass = (status) => {
    const classes = {
        'En validation': 'bg-amber-100 text-amber-700',
        'En preparation': 'bg-blue-100 text-blue-700',
        Prete: 'bg-emerald-100 text-emerald-700',
        Rejetee: 'bg-red-100 text-red-700',
    };

    return classes[status] ?? 'bg-slate-100 text-slate-700';
};

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
                <p class="text-xs text-slate-300 mt-2">Espace demandeur</p>
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
                    <h2 class="text-xl font-bold text-slate-800">Demandes de retrait</h2>
                    <p class="text-sm text-slate-500">Suivi de vos demandes et historique</p>
                </div>
                <div class="text-sm font-medium text-slate-700">{{ userName }}</div>
            </header>

            <main class="p-8 space-y-6">
                <section class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex flex-col md:flex-row gap-3">
                        <input
                            type="text"
                            placeholder="Rechercher une demande..."
                            class="flex-1 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none"
                        >
                        <button class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">
                            Nouvelle demande
                        </button>
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Article</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Quantite</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="request in requests" :key="request.id" class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-semibold text-blue-600">{{ request.ref }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ request.type }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ request.article }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ request.qty }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ request.date }}</td>
                                <td class="px-6 py-4">
                                    <span :class="['px-3 py-1 rounded-full text-xs font-semibold', statusClass(request.status)]">
                                        {{ request.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </main>
        </div>
    </div>
</template>
