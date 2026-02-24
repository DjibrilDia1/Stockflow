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

const articles = ref([
    { id: 1, code: 'ART-001', name: 'Papier A4', category: 'Fournitures', stock: 320, status: 'Disponible' },
    { id: 2, code: 'ART-002', name: 'Stylos noirs', category: 'Fournitures', stock: 145, status: 'Disponible' },
    { id: 3, code: 'ART-003', name: 'Cartouches encre', category: 'Informatique', stock: 12, status: 'Stock bas' },
    { id: 4, code: 'ART-004', name: 'Chemises carton', category: 'Archivage', stock: 0, status: 'Rupture' },
]);

const stockClass = (status) => {
    const classes = {
        Disponible: 'bg-emerald-100 text-emerald-700',
        'Stock bas': 'bg-amber-100 text-amber-700',
        Rupture: 'bg-red-100 text-red-700',
    };

    return classes[status] ?? 'bg-slate-100 text-slate-700';
};

const logout = () => {
    if (confirm('Déconnexion ?')) router.post(route('logout'));
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

        <div class="ml-64 flex-1">
            <header class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4 text-slate-500">
                    <Link :href="route('demandeur.demandes.index')" class="text-slate-400 hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <span class="font-medium">Consultation des articles</span>
                </div>
                <div class="text-sm font-medium text-slate-700">{{ userName }}</div>
            </header>

            <main class="p-8 space-y-6">
                <div>
                        <h2 class="text-2xl font-bold text-slate-900">Consultation des articles</h2>
                        <p class="text-slate-500 mt-1">Visualiser les articles disponibles avant une demande</p>
                    </div>
                <section class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <input
                            type="text"
                            placeholder="Rechercher un article..."
                            class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none"
                        >
                        <select class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            <option>Toutes les categories</option>
                            <option>Fournitures</option>
                            <option>Informatique</option>
                            <option>Archivage</option>
                        </select>
                        <select class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            <option>Tous les statuts</option>
                            <option>Disponible</option>
                            <option>Stock bas</option>
                            <option>Rupture</option>
                        </select>
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Article</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Categorie</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Etat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="article in articles" :key="article.id" class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-semibold text-blue-600">{{ article.code }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ article.name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ article.category }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ article.stock }}</td>
                                <td class="px-6 py-4">
                                    <span :class="['px-3 py-1 rounded-full text-xs font-semibold', stockClass(article.status)]">
                                        {{ article.status }}
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
