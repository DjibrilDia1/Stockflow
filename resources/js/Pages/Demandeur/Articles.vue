<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import NewRequestModal from '@/Components/NewRequestModal.vue';

const props = defineProps({
    articles: Array,
    categories: Array,
    articlesDisponibles: Array
});

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Demandeur');

const searchQuery = ref('');
const selectedCategory = ref('Toutes les categories');

const menu = [
    { name: 'Tableau de bord', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', route: 'demandeur.dashboard' },
    { name: 'Demandes', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', route: 'demandeur.demandes.index' },
    { name: 'Consultation articles', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', route: 'demandeur.articles.index' },
];

const filteredArticles = computed(() => {
    return props.articles.filter(article => {
        const matchesSearch = article.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            article.code.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesCategory = selectedCategory.value === 'Toutes les categories' ||
            article.category === selectedCategory.value;
        return matchesSearch && matchesCategory;
    });
});

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

// Logique de demande rapide
const showModal = ref(false);
const initialArticleId = ref(null);

const openRequestModal = (articleId) => {
    initialArticleId.value = articleId;
    showModal.value = true;
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
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

        <div class="ml-52 flex-1">
            <header
                class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4 text-slate-500">
                    <Link :href="route('demandeur.dashboard')"
                        class="text-slate-400 hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <span class="font-medium">Consultation des articles</span>
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
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Articles en stock</h2>
                    <p class="text-slate-500 mt-1">Consultez les disponibilités réelles avant de demander</p>
                </div>

                <!-- Nouveau composant partagé -->
                <NewRequestModal :show="showModal" :articles="articlesDisponibles" :initialArticleId="initialArticleId"
                    @close="showModal = false" />

                <section class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input v-model="searchQuery" type="text" placeholder="Rechercher par nom ou code..."
                            class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        <select v-model="selectedCategory"
                            class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            <option>Toutes les categories</option>
                            <option v-for="cat in categories" :key="cat.cat_id" :value="cat.cat_nom">
                                {{ cat.cat_nom }}
                            </option>
                        </select>
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Article
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Categorie
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Stock
                                    Total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-600">Etat</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-slate-600">Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="article in filteredArticles" :key="article.id" class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-semibold text-blue-600">{{ article.code }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ article.name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ article.category }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ article.stock }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="['px-3 py-1 rounded-full text-xs font-semibold', stockClass(article.status)]">
                                        {{ article.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button v-if="article.stock > 0" @click="openRequestModal(article.id)"
                                        class="px-4 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition-all">
                                        Demander
                                    </button>
                                    <span v-else class="text-xs text-red-500 italic">Indisponible</span>
                                </td>
                            </tr>
                            <tr v-if="filteredArticles.length === 0">
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                                    Aucun article trouvé.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </main>
        </div>
    </div>
</template>
