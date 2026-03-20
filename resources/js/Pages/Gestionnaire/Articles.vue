<script setup>
import { ref, computed } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import Toast from '@/Components/Toast.vue';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Gestionnaire');

// Onglet actif : 'articles' ou 'categories'
const activeTab = ref('articles');

const props = defineProps({
    items: Object,
    categories: Object,
    categories_all: Array,
    warehouses: Array,
});

const searchQuery = ref('');
const filterCategory = ref('');

const filteredArticles = computed(() => {
    return props.items?.data?.filter(item => {
        const matchesSearch = !searchQuery.value ||
            (item.art_nom ?? '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (item.art_reference ?? '').toLowerCase().includes(searchQuery.value.toLowerCase());

        const matchesCategory = !filterCategory.value || item.art_cat_id == filterCategory.value;

        return matchesSearch && matchesCategory;
    }) ?? [];
});

const filteredCategories = computed(() => {
    return props.categories?.data?.filter(cat =>
        (cat.cat_nom ?? '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        (cat.cat_code ?? '').toLowerCase().includes(searchQuery.value.toLowerCase())
    ) ?? [];
});

const showAddModal = ref(false);
const showEditModal = ref(false);

const articleForm = useForm({
    art_id: null,
    art_reference: '',
    art_nom: '',
    art_unite: 'unité',
    art_cat_id: '',
    art_seuil_alerte: 0,
    art_stock_securite: 0,
    art_prix_defaut: 0
});

const categoryForm = useForm({
    cat_id: null,
    cat_code: '',
    cat_nom: '',
    cat_description: ''
});

// --- ARTICLES ---
const addArticle = () => {
    articleForm.post(route('gestionnaire.articles.store'), {
        onSuccess: () => {
            showAddModal.value = false;
            articleForm.reset();
        }
    });
};

const openEditModal = (article) => {
    articleForm.clearErrors();
    articleForm.art_id = article.art_id;
    articleForm.art_reference = article.art_reference;
    articleForm.art_nom = article.art_nom;
    articleForm.art_unite = article.art_unite;
    articleForm.art_cat_id = article.art_cat_id;
    articleForm.art_seuil_alerte = article.art_seuil_alerte;
    articleForm.art_stock_securite = article.art_stock_securite;
    articleForm.art_prix_defaut = article.art_prix_defaut;
    showEditModal.value = true;
};

const updateArticle = () => {
    articleForm.put(route('gestionnaire.articles.update', articleForm.art_id), {
        onSuccess: () => {
            showEditModal.value = false;
            articleForm.reset();
        }
    });
};

const deleteArticle = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        router.delete(route('gestionnaire.articles.destroy', id));
    }
};

// --- CATÉGORIES ---
const showAddCategoryModal = ref(false);
const showEditCategoryModal = ref(false);

const addCategory = () => {
    categoryForm.post(route('gestionnaire.categories.store'), {
        onSuccess: () => {
            showAddCategoryModal.value = false;
            categoryForm.reset();
        }
    });
};

const openEditCategoryModal = (category) => {
    categoryForm.clearErrors();
    categoryForm.cat_id = category.cat_id;
    categoryForm.cat_code = category.cat_code;
    categoryForm.cat_nom = category.cat_nom;
    categoryForm.cat_description = category.cat_description;
    showEditCategoryModal.value = true;
};

const updateCategory = () => {
    categoryForm.put(route('gestionnaire.categories.update', categoryForm.cat_id), {
        onSuccess: () => {
            showEditCategoryModal.value = false;
            categoryForm.reset();
        }
    });
};

const deleteCategory = (id) => {
    if (confirm('Supprimer cette catégorie ?')) {
        router.delete(route('gestionnaire.categories.destroy', id));
    }
};

const navigation = [
    { name: 'Tableau de bord', route: 'gestionnaire.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', route: 'gestionnaire.articles.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', route: 'gestionnaire.mouvements.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', route: 'gestionnaire.demandes.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports', route: 'gestionnaire.rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Utilisateurs', route: 'gestionnaire.utilisateurs.index', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { name: 'Services & Fournitures', route: 'gestionnaire.services-fournisseurs.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
];

const logout = () => {
    if (confirm('Déconnexion ?')) router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <Toast />
        <!-- Sidebar -->
        <aside class="fixed left-0 top-0 h-screen w-52 bg-slate-800 shadow-2xl z-50 flex flex-col">
            <div class="px-6 py-6 border-b border-slate-700/50">
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    <span class="text-blue-400">Stock</span><span class="text-teal-400">Flow</span>
                </h1>
                <p class="text-xs text-slate-300 mt-2">Espace Gestionnaire</p>
            </div>

            <nav class="px-3 py-6 space-y-1.5 flex-1">
                <Link v-for="item in navigation" :key="item.name" :href="route(item.route)" :class="[
                    route().current(item.route)
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
            <!-- Header -->
            <header
                class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4 text-slate-500">
                    <Link :href="route('gestionnaire.dashboard')" class="hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <span class="font-medium">Articles & Catégories</span>
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

            <main class="p-8 space-y-8">
                <!-- ONGLETS -->
                <div class="flex gap-1 bg-slate-100 p-1 rounded-xl w-fit">
                    <button @click="activeTab = 'articles'" :class="[
                        'flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all',
                        activeTab === 'articles'
                            ? 'bg-white text-teal-700 shadow-sm border border-slate-200'
                            : 'text-slate-500 hover:text-slate-700'
                    ]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Gestion des articles
                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold"
                            :class="activeTab === 'articles' ? 'bg-teal-100 text-teal-700' : 'bg-slate-200 text-slate-500'">
                            {{ items.total }}
                        </span>
                    </button>
                    <button @click="activeTab = 'categories'" :class="[
                        'flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all',
                        activeTab === 'categories'
                            ? 'bg-white text-teal-700 shadow-sm border border-slate-200'
                            : 'text-slate-500 hover:text-slate-700'
                    ]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Catégories articles
                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold"
                            :class="activeTab === 'categories' ? 'bg-teal-100 text-teal-700' : 'bg-slate-200 text-slate-500'">
                            {{ categories.total }}
                        </span>
                    </button>
                </div>
                <!-- SECTION ARTICLES -->
                <section v-show="activeTab === 'articles'" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800">Gestion des articles</h2>
                            <p class="text-slate-500 text-sm">Ajouter, modifier ou supprimer vos articles en stock</p>
                        </div>
                        <button @click="showAddModal = true; articleForm.reset(); articleForm.clearErrors();"
                            class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Nouvel Article
                        </button>

                    </div>

                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-wrap gap-4 items-center">
                        <div class="relative flex-1 min-w-[300px]">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input v-model="searchQuery" type="text" placeholder="Rechercher par nom ou référence..."
                                class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <select v-model="filterCategory"
                            class="bg-slate-50 border border-slate-200 rounded-lg appearance-none text-sm outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">Toutes les catégories</option>
                            <option v-for="cat in categories_all" :key="cat.cat_id" :value="cat.cat_id">{{ cat.cat_nom
                                }}</option>
                        </select>
                    </div>

                    <div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-100 text-slate-600 text-xs uppercase font-semibold">
                                    <th class="px-6 py-4">Référence</th>
                                    <th class="px-6 py-4">Nom</th>
                                    <th class="px-6 py-4">Catégorie</th>
                                    <th class="px-6 py-4">Stock Total</th>
                                    <th class="px-6 py-4">Seuil</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="item in filteredArticles" :key="item.art_id"
                                    class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-blue-600">{{ item.art_reference }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-700">{{ item.art_nom }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ item.category ?
                                        item.category.cat_nom : 'N/A' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-slate-700 mb-1">Total: {{ item.total_stock ||
                                            0 }}</div>
                                        <div class="space-y-0.5">
                                            <div v-for="stock in item.item_stocks" :key="stock.sta_id"
                                                class="text-[10px] text-slate-400 leading-tight">
                                                <span class="font-medium text-slate-500">{{ stock.warehouse?.ent_nom
                                                }}:</span> {{ stock.sta_quantite }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ item.art_seuil_alerte }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-3">
                                            <button @click="openEditModal(item)"
                                                class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button @click="deleteArticle(item.art_id)"
                                                class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredArticles.length === 0">
                                    <td colspan="6" class="px-6 py-10 text-center text-slate-400 text-sm">
                                        Aucun article trouvé pour « {{ searchQuery }} »
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Pagination Articles -->
                        <div
                            class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex flex-col items-center gap-2">
                            <div class="flex items-center gap-1">
                                <Link v-for="(link, k) in items.links" :key="k" :href="link.url || '#'"
                                    v-html="link.label" class="px-3 py-1 text-sm rounded transition-all"
                                    :class="{ 'bg-teal-600 text-white font-bold': link.active, 'text-slate-400 hover:text-teal-600': !link.active && link.url, 'text-slate-300 cursor-not-allowed': !link.url }" />
                            </div>
                            <span class="text-xs text-slate-500">{{ items.from }}-{{ items.to }} sur {{ items.total }}
                                articles</span>
                        </div>
                    </div>
                </section>

                <!-- SECTION CATÉGORIES -->
                <section v-show="activeTab === 'categories'" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800">Gestion des catégories</h2>
                            <p class="text-slate-500 text-sm">Organisez vos articles par types</p>
                        </div>
                        <button @click="showAddCategoryModal = true; categoryForm.reset(); categoryForm.clearErrors();"
                            class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Nouvelle Catégorie
                        </button>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                        <div class="relative max-w-md">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input v-model="searchQuery" type="text" placeholder="Rechercher par nom ou code..."
                                class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-100 text-slate-600 text-xs uppercase font-semibold">
                                    <th class="px-6 py-4">Code</th>
                                    <th class="px-6 py-4">Nom</th>
                                    <th class="px-6 py-4">Description</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="cat in filteredCategories" :key="cat.cat_id"
                                    class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-blue-600">{{ cat.cat_code }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-700">{{ cat.cat_nom }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ cat.cat_description }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-3">
                                            <button @click="openEditCategoryModal(cat)"
                                                class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button @click="deleteCategory(cat.cat_id)"
                                                class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredCategories.length === 0">
                                    <td colspan="4" class="px-6 py-10 text-center text-slate-400 text-sm">
                                        Aucune catégorie trouvée pour « {{ searchQuery }} »
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Pagination Catégories -->
                        <div
                            class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex flex-col items-center gap-2">
                            <div class="flex items-center gap-1">
                                <Link v-for="(link, k) in categories.links" :key="k" :href="link.url || '#'"
                                    v-html="link.label" class="px-3 py-1 text-sm rounded transition-all"
                                    :class="{ 'bg-teal-600 text-white font-bold': link.active, 'text-slate-400 hover:text-teal-600': !link.active && link.url, 'text-slate-300 cursor-not-allowed': !link.url }" />
                            </div>
                            <span class="text-xs text-slate-500">{{ categories.from }}-{{ categories.to }} sur {{
                                categories.total }} catégories</span>
                        </div>
                    </div>
                </section>
            </main>
        </div>

        <!-- MODAL AJOUT ARTICLE -->
        <div v-if="showAddModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showAddModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800">Ajouter un article</h3>
                    <button @click="showAddModal = false"
                        class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
                </div>
                <form @submit.prevent="addArticle" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Référence</label>
                        <input v-model="articleForm.art_reference" type="text"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                            :class="{ 'border-red-500': articleForm.errors.art_reference }">
                        <div v-if="articleForm.errors.art_reference" class="text-red-500 text-xs mt-1">{{
                            articleForm.errors.art_reference }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nom</label>
                        <input v-model="articleForm.art_nom" type="text"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                            :class="{ 'border-red-500': articleForm.errors.art_nom }">
                        <div v-if="articleForm.errors.art_nom" class="text-red-500 text-xs mt-1">{{
                            articleForm.errors.art_nom }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Catégorie</label>
                            <select v-model="articleForm.art_cat_id"
                                class="w-full px-4 py-2 border rounded-lg outline-none text-sm"
                                :class="{ 'border-red-500': articleForm.errors.art_cat_id }">
                                <option value="" disabled>Sélectionner</option>
                                <option v-for="cat in categories_all" :key="cat.cat_id" :value="cat.cat_id">{{
                                    cat.cat_nom }}</option>
                            </select>
                            <div v-if="articleForm.errors.art_cat_id" class="text-red-500 text-xs mt-1">{{
                                articleForm.errors.art_cat_id }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Unité</label>
                            <input v-model="articleForm.art_unite" type="text"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                                :class="{ 'border-red-500': articleForm.errors.art_unite }">
                            <div v-if="articleForm.errors.art_unite" class="text-red-500 text-xs mt-1">{{
                                articleForm.errors.art_unite }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Seuil Alerte</label>
                            <input v-model="articleForm.art_seuil_alerte" type="number"
                                class="w-full px-4 py-2 border rounded-lg outline-none"
                                :class="{ 'border-red-500': articleForm.errors.art_seuil_alerte }">
                            <div v-if="articleForm.errors.art_seuil_alerte" class="text-red-500 text-xs mt-1">{{
                                articleForm.errors.art_seuil_alerte }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Prix par défaut</label>
                            <input v-model="articleForm.art_prix_defaut" type="number" step="0.01"
                                class="w-full px-4 py-2 border rounded-lg outline-none"
                                :class="{ 'border-red-500': articleForm.errors.art_prix_defaut }">
                            <div v-if="articleForm.errors.art_prix_defaut" class="text-red-500 text-xs mt-1">{{
                                articleForm.errors.art_prix_defaut }}</div>
                        </div>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="showAddModal = false"
                            class="flex-1 px-4 py-2 border rounded-lg">Annuler</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700 shadow-md transition-all"
                            :disabled="articleForm.processing">
                            {{ articleForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL MODIFICATION ARTICLE -->
        <div v-if="showEditModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showEditModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-blue-50">
                    <h3 class="text-lg font-bold text-slate-800">Modifier l'article</h3>
                    <button @click="showEditModal = false"
                        class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
                </div>
                <form @submit.prevent="updateArticle" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Référence</label>
                        <input v-model="articleForm.art_reference" type="text"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                            :class="{ 'border-red-500': articleForm.errors.art_reference }">
                        <div v-if="articleForm.errors.art_reference" class="text-red-500 text-xs mt-1">{{
                            articleForm.errors.art_reference }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nom</label>
                        <input v-model="articleForm.art_nom" type="text"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                            :class="{ 'border-red-500': articleForm.errors.art_nom }">
                        <div v-if="articleForm.errors.art_nom" class="text-red-500 text-xs mt-1">{{
                            articleForm.errors.art_nom }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Catégorie</label>
                            <select v-model="articleForm.art_cat_id"
                                class="w-full px-4 py-2 border rounded-lg outline-none text-sm"
                                :class="{ 'border-red-500': articleForm.errors.art_cat_id }">
                                <option v-for="cat in categories_all" :key="cat.cat_id" :value="cat.cat_id">{{
                                    cat.cat_nom }}</option>
                            </select>
                            <div v-if="articleForm.errors.art_cat_id" class="text-red-500 text-xs mt-1">{{
                                articleForm.errors.art_cat_id }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Unité</label>
                            <input v-model="articleForm.art_unite" type="text"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                                :class="{ 'border-red-500': articleForm.errors.art_unite }">
                            <div v-if="articleForm.errors.art_unite" class="text-red-500 text-xs mt-1">{{
                                articleForm.errors.art_unite }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Seuil Alerte</label>
                            <input v-model="articleForm.art_seuil_alerte" type="number"
                                class="w-full px-4 py-2 border rounded-lg outline-none"
                                :class="{ 'border-red-500': articleForm.errors.art_seuil_alerte }">
                            <div v-if="articleForm.errors.art_seuil_alerte" class="text-red-500 text-xs mt-1">{{
                                articleForm.errors.art_seuil_alerte }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Prix par défaut</label>
                            <input v-model="articleForm.art_prix_defaut" type="number" step="0.01"
                                class="w-full px-4 py-2 border rounded-lg outline-none"
                                :class="{ 'border-red-500': articleForm.errors.art_prix_defaut }">
                            <div v-if="articleForm.errors.art_prix_defaut" class="text-red-500 text-xs mt-1">{{
                                articleForm.errors.art_prix_defaut }}</div>
                        </div>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="showEditModal = false"
                            class="flex-1 px-4 py-2 border rounded-lg">Annuler</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700 shadow-md transition-all"
                            :disabled="articleForm.processing">
                            {{ articleForm.processing ? 'Mise à jour...' : 'Mettre à jour' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL AJOUT CATÉGORIE -->
        <div v-if="showAddCategoryModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showAddCategoryModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 overflow-hidden animate-fade-in">
                <div class="flex items-center justify-between border-b pb-2 mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Nouvelle Catégorie</h3>

                    <button @click="showAddCategoryModal = false"
                        class="text-slate-400 hover:text-slate-600 text-2xl leading-none">
                        &times;
                    </button>
                </div>
                <form @submit.prevent="addCategory" class="space-y-4">
                    <div>
                        <input v-model="categoryForm.cat_code" placeholder="Référence (Code)"
                            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-teal-500 outline-none"
                            :class="{ 'border-red-500': categoryForm.errors.cat_code }">
                        <div v-if="categoryForm.errors.cat_code" class="text-red-500 text-xs mt-1">{{
                            categoryForm.errors.cat_code }}</div>
                    </div>
                    <div>
                        <input v-model="categoryForm.cat_nom" placeholder="Nom"
                            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-teal-500 outline-none"
                            :class="{ 'border-red-500': categoryForm.errors.cat_nom }">
                        <div v-if="categoryForm.errors.cat_nom" class="text-red-500 text-xs mt-1">{{
                            categoryForm.errors.cat_nom }}</div>
                    </div>
                    <div>
                        <textarea v-model="categoryForm.cat_description" placeholder="Description" rows="3"
                            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-teal-500 outline-none"
                            :class="{ 'border-red-500': categoryForm.errors.cat_description }"></textarea>
                        <div v-if="categoryForm.errors.cat_description" class="text-red-500 text-xs mt-1">{{
                            categoryForm.errors.cat_description }}</div>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="showAddCategoryModal = false"
                            class="flex-1 px-4 py-2 border rounded-lg">Annuler</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700"
                            :disabled="categoryForm.processing">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL MODIFICATION CATÉGORIE -->
        <div v-if="showEditCategoryModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showEditCategoryModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 overflow-hidden animate-fade-in">
                <h3 class="text-lg font-bold mb-4 text-slate-800 border-b pb-2">Modifier la catégorie</h3>
                <form @submit.prevent="updateCategory" class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">Code</label>
                        <input v-model="categoryForm.cat_code"
                            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-teal-500 outline-none"
                            :class="{ 'border-red-500': categoryForm.errors.cat_code }">
                        <div v-if="categoryForm.errors.cat_code" class="text-red-500 text-xs mt-1">{{
                            categoryForm.errors.cat_code }}</div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">Nom</label>
                        <input v-model="categoryForm.cat_nom"
                            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-teal-500 outline-none"
                            :class="{ 'border-red-500': categoryForm.errors.cat_nom }">
                        <div v-if="categoryForm.errors.cat_nom" class="text-red-500 text-xs mt-1">{{
                            categoryForm.errors.cat_nom }}</div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">Description</label>
                        <textarea v-model="categoryForm.cat_description" rows="3"
                            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-teal-500 outline-none"
                            :class="{ 'border-red-500': categoryForm.errors.cat_description }"></textarea>
                        <div v-if="categoryForm.errors.cat_description" class="text-red-500 text-xs mt-1">{{
                            categoryForm.errors.cat_description }}</div>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="showEditCategoryModal = false"
                            class="flex-1 px-4 py-2 border rounded-lg">Annuler</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700"
                            :disabled="categoryForm.processing">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
