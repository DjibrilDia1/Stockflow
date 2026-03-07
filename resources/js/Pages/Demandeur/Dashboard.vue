<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Demandeur');

const props = defineProps({
    stats: Object,
    recentRequests: Array,
    notifications: Array,
});

const statsCards = computed(() => [
    { label: 'Demandes en cours', value: props.stats.inProgress, class: "card-hover bg-white rounded-xl p-6 shadow-md border border-slate-100 animate-fade-in", style: "animation-delay: 0.1s", hint: 'En validation ou préparation' },
    { label: 'Demandes traitées', value: props.stats.processed, class: "card-hover bg-white rounded-xl p-6 shadow-md border border-slate-100 animate-fade-in", style: "animation-delay: 0.2s", hint: 'Terminées avec succès' },
    { label: 'Demandes rejetées', value: props.stats.rejected, color: "text-4xl font-bold text-red-600 mb-1", class: "card-hover bg-white rounded-xl p-6 shadow-md border border-slate-100 animate-fade-in", style: "animation-delay: 0.3s", hint: 'Nécessitent une correction' },
]);

const menu = [
    { name: 'Tableau de bord', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', route: 'demandeur.dashboard' },
    { name: 'Demandes', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', route: 'demandeur.demandes.index' },
    { name: 'Consultation articles', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', route: 'demandeur.articles.index' },
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
    if (confirm('Déconnexion ?')) router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <!-- ==================================================  SECTION SIDEBAR =========================================================-->
        <aside class="fixed left-0 top-0 h-screen w-52 bg-slate-800 shadow-2xl z-50 flex flex-col">
            <div class="px-6 py-6 border-b border-slate-700/50">
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    <span class="text-blue-400">Stock</span><span class="text-teal-400">Flow</span>
                </h1>
                <p class="text-xs text-slate-300 mt-2">Espace Demandeur</p>
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

        <!-- ==================================================  SECTION CONTENUE PRINCIPAL =========================================================-->
        <div class="ml-52 flex-1">
            <!-- ================================================== SECTION HEADER  =========================================================-->
            <header
                class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Dashboard</h2>
                    <p class="text-sm text-slate-500">Vue d'ensemble de vos demandes</p>
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

            <!-- ================================================== SECTION PRINCIPAL =========================================================-->
            <main class="p-8 space-y-8">
                <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <article v-for="card in statsCards" :key="card.label" :class="card.class" :style="card.style">
                        <p class="text-sm text-slate-500">{{ card.label }}</p>

                        <p :class="[card.color ? card.color : 'text-3xl font-bold text-slate-900 mt-2']">
                            {{ card.value }}
                        </p>

                        <p class="text-xs text-slate-500 mt-1 italic">{{ card.hint }}</p>
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
                                    <tr v-for="request in props.recentRequests" :key="request.id">
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
                                    <tr v-if="props.recentRequests.length === 0">
                                        <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                                            Aucune demande récente.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                            <<<<<<< HEAD <h3 class="text-base font-semibold text-slate-800">Acces rapide</h3>
                                <Link :href="route('demandeur.demandes.index')"
                                    class="text-sm text-slate-500 mt-1 block hover:text-teal-600 transition-colors cursor-pointer">
                                    Créer rapidement une nouvelle demande de retrait.
                                </Link>
                                =======
                                <h3 class="text-base font-semibold text-slate-800">Actions rapides</h3>
                                <p class="text-sm text-slate-500 mt-1">
                                    Besoin d'un article ? Créez une demande en quelques clics.
                                </p>
                                >>>>>>> fbc3247d5e81a96e28f6c406cd4d9f3dbdbbe7e0
                                <Link :href="route('demandeur.demandes.index')"
                                    class="inline-flex items-center justify-center mt-4 w-full px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition-all shadow-lg hover:shadow-teal-200">
                                    <span class="mr-2 text-lg">+</span> Faire une demande
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

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
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
    from {
        width: 0;
    }
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
