<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Demandeur');

const stats = [
    { label: 'Demandes en cours', value: 4, hint: '2 en validation, 2 en preparation' },
    { label: 'Demandes traitees', value: 18, hint: 'sur les 30 derniers jours' },
    { label: 'Demandes rejetees', value: 1, hint: 'necessite une correction' },
];

const notifications = [
    { id: 1, message: 'La demande DR-2026-021 est en cours de validation.', type: 'info' },
    { id: 2, message: 'La demande DR-2026-019 est prete pour retrait.', type: 'success' },
    { id: 3, message: 'La demande DR-2026-017 a ete rejetee.', type: 'warning' },
];

const recentRequests = [
    { id: 1, ref: 'DR-2026-021', service: 'Service RH', date: '12/02/2026', status: 'En validation' },
    { id: 2, ref: 'DR-2026-020', service: 'Service Finance', date: '10/02/2026', status: 'En preparation' },
    { id: 3, ref: 'DR-2026-019', service: 'Service Pedagogique', date: '08/02/2026', status: 'Prete' },
];

const menu = [
    { name: 'Dashboard', route: 'demandeur.dashboard' },
    { name: 'Demandes', route: 'demandeur.demandes.index' },
    { name: 'Consultation articles', route: 'demandeur.articles.index' },
];

const statusClass = (status) => {
    const classes = {
        'En validation': 'bg-amber-100 text-amber-700',
        'En preparation': 'bg-blue-100 text-blue-700',
        Prete: 'bg-emerald-100 text-emerald-700',
    };

    return classes[status] ?? 'bg-slate-100 text-slate-700';
};

const notifClass = (type) => {
    const classes = {
        info: 'border-l-blue-500 bg-blue-50 text-blue-800',
        success: 'border-l-emerald-500 bg-emerald-50 text-emerald-800',
        warning: 'border-l-amber-500 bg-amber-50 text-amber-800',
    };

    return classes[type] ?? 'border-l-slate-500 bg-slate-50 text-slate-800';
};

const logout = () => {
    if (confirm('Deconnexion ?')) {
        router.post(route('logout'));
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <!-- ==================================================  SECTION SIDEBAR =========================================================-->
        <aside class="fixed left-0 top-0 h-screen w-64 bg-slate-800 shadow-2xl z-50 flex flex-col">
            <div class="px-6 py-6 border-b border-slate-700/50">
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    <span class="text-blue-400">Stock</span><span class="text-teal-400">Flow</span>
                </h1>
                <p class="text-xs text-slate-300 mt-2">Espace demandeur</p>
            </div>

            <nav class="px-3 py-6 space-y-1.5 flex-1 overflow-y-auto">
                <Link v-for="item in menu" :key="item.name" :href="route(item.route)" :class="[
                    route().current(item.route)
                        ? 'bg-teal-600 text-white shadow-lg'
                        : 'text-slate-300 hover:bg-slate-700 hover:text-white',
                    'flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all'
                ]">
                    {{ item.name }}
                </Link>
            </nav>

            <div class="p-4 border-t border-slate-700/50">
                <button @click="logout"
                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-400 hover:bg-red-500/10 hover:text-red-400 rounded-lg transition-all">
                    Deconnexion
                </button>
            </div>
        </aside>

        <!-- ==================================================  SECTION CONTENUE PRINCIPAL =========================================================-->
        <div class="ml-64 flex-1">
            <!-- ================================================== SECTION HEADER  =========================================================-->
            <header
                class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Dashboard</h2>
                    <p class="text-sm text-slate-500">Vue d'ensemble de vos demandes</p>
                </div>
                <div class="text-sm font-medium text-slate-700">{{ userName }}</div>
            </header>

            <!-- ================================================== SECTION PRINCIPAL =========================================================-->
            <main class="p-8 space-y-8">
                <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <article v-for="card in stats" :key="card.label"
                        class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                        <p class="text-sm text-slate-500">{{ card.label }}</p>
                        <p class="text-3xl font-bold text-slate-900 mt-2">{{ card.value }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ card.hint }}</p>
                    </article>
                </section>

                <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm lg:col-span-2">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-slate-800">Demandes recentes</h3>
                            <Link :href="route('demandeur.demandes.index')"
                                class="text-sm font-semibold text-teal-700 hover:text-teal-800">
                                Voir tout
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs uppercase font-semibold text-slate-600">
                                            Reference</th>
                                        <th class="px-6 py-3 text-left text-xs uppercase font-semibold text-slate-600">
                                            Service</th>
                                        <th class="px-6 py-3 text-left text-xs uppercase font-semibold text-slate-600">
                                            Date</th>
                                        <th class="px-6 py-3 text-left text-xs uppercase font-semibold text-slate-600">
                                            Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="request in recentRequests" :key="request.id">
                                        <td class="px-6 py-4 text-sm font-medium text-blue-600">{{ request.ref }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-700">{{ request.service }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-500">{{ request.date }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                :class="['px-3 py-1 rounded-full text-xs font-semibold', statusClass(request.status)]">
                                                {{ request.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                            <h3 class="text-base font-semibold text-slate-800">Acces rapide</h3>
                            <p class="text-sm text-slate-500 mt-1">Creer rapidement une nouvelle demande de retrait.</p>
                            <Link :href="route('demandeur.demandes.index')"
                                class="inline-flex items-center justify-center mt-4 w-full px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                Nouvelle demande
                            </Link>
                        </div>

                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                            <h3 class="text-base font-semibold text-slate-800 mb-3">Notifications</h3>
                            <div class="space-y-3">
                                <div v-for="notif in notifications" :key="notif.id"
                                    :class="['border-l-4 p-3 rounded-r-lg text-sm', notifClass(notif.type)]">
                                    {{ notif.message }}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</template>
