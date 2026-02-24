<script setup>
import { ref, computed } from 'vue';
import { Link, router , usePage } from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Gestionnaire');

const showAddModal = ref(false);
const showEditModal = ref(false);
const editingArticle = ref(null);

// --- SUPPRESSION ---
const deleteArticle = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        // En local : on filtre le tableau
        articles.value = articles.value.filter(art => art.id !== id);
        // En production : router.delete(route('articles.destroy', id))
    }
};

// --- MODIFICATION ---
const openEditModal = (article) => {
    // On crée une copie pour ne pas modifier le tableau en direct avant d'enregistrer
    editingArticle.value = { ...article };
    showEditModal.value = true;
};

const updateArticle = () => {
    const index = articles.value.findIndex(art => art.id === editingArticle.value.id);
    if (index !== -1) {
        // Mise à jour locale
        articles.value[index] = { ...editingArticle.value };
        showEditModal.value = false;
        editingArticle.value = null;
    }
};


// --- ÉTATS POUR LES CATÉGORIES ---
const showAddCategoryModal = ref(false);
const showEditCategoryModal = ref(false);
const editingCategory = ref(null);
const editingReference = ref(null);

const newCategory = ref({
    reference: '',
    name: '',
    description: ''
});

// --- ACTIONS CATÉGORIES ---

// Ajout
const addCategory = () => {
    // Simulation d'ID et ajout local
    categories.value.push({
        ...newCategory.value,
        id: Date.now()
    });
    showAddCategoryModal.value = false;
    newCategory.value = { reference: '', name: '', description: '' };
};

// Suppression
const deleteCategory = (id) => {
    if (confirm('Supprimer cette catégorie ?')) {
        categories.value = categories.value.filter(cat => cat.id !== id);
    }
};

// Modification
const openEditCategoryModal = (category) => {
    // On crée une copie profonde pour ne pas impacter le tableau avant validation
    editingCategory.value = { ...category };
    showEditCategoryModal.value = true;
};

const updateCategory = () => {
    const index = categories.value.findIndex(cat => cat.id === editingCategory.value.id);
    if (index !== -1) {
        // Mise à jour du tableau avec les nouvelles valeurs (nom ET référence)
        categories.value[index] = { ...editingCategory.value };
        showEditCategoryModal.value = false;
        editingCategory.value = null;
    }
};

// Simulation des données (À remplacer par les props de ton contrôleur Laravel)
const articles = ref([
    { id: 1, reference: 'REF-001', name: 'Papier A4', category: 'Fournitures', stock: 25, threshold: 'Campus A', status: 'Service RH' },
    { id: 2, reference: 'REF-002', name: 'Stylos Noirs', category: 'Fournitures', stock: 15, threshold: 'Magasin central', status: 'Achat fournisseur' },
    { id: 3, reference: 'REF-003', name: 'Chaise de bureau', category: 'Mobilier', stock: 5, threshold: 'Bâtiment B', status: 'Correction stock' },
]);

const categories = ref([
    { id: 1, reference: 'REF-001', name: 'Papier A4', description: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas, eaque.' },
    { id: 2, reference: 'REF-002', name: 'Stylos Noirs', description: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas, eaque.' },
    { id: 3, reference: 'REF-003', name: 'Chaise de bureau', description: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas, eaque.' },
]);

const searchQuery = ref('');
const selectedCategory = ref('tous');
const selectedWarehouse = ref('tous');

const navigation = [
    { name: 'Tableau de bord', route: 'gestionnaire.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', route: 'gestionnaire.articles.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', route: 'gestionnaire.mouvements.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', route: 'gestionnaire.demandes.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports', route: 'gestionnaire.rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Utilisateur', route: 'gestionnaire.utilisateurs.index', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { name: 'Services & Fournisseurs',route:'gestionnaire.services-fournisseurs.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },

];

const logout = () => {
    if (confirm('Déconnexion ?')) router.post(route('logout'));
};

const newArticle = ref({
    reference: '',
    name: '',
    category: '',
    stock: 0,
    threshold: '',
    status: ''
});

const addArticle = () => {
    // Ici tu feras ton appel API (ex: router.post)
    console.log("Article ajouté :", newArticle.value);

    // Simulation d'ajout local
    articles.value.push({ ...newArticle.value, id: Date.now() });

    // Fermer et réinitialiser
    showAddModal.value = false;
    newArticle.value = { reference: '', name: '', category: '', stock: 0, threshold: '', status: '' };
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <aside class="fixed left-0 top-0 h-screen w-52 bg-slate-800 shadow-2xl z-50 flex flex-col">
            <div class="px-6 py-6 border-b border-slate-700/50">
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    <span class="text-blue-400">Stock</span><span class="text-teal-400">Flow</span>
                </h1>
                <p class="text-xs text-slate-300 mt-2">Espace Gestionnaire</p>
            </div>

            <nav class="px-3 py-6 space-y-1.5 flex-1">
                <Link v-for="item in navigation" :key="item.name" :href="item.route ? route(item.route) : '#'" :class="[
                    item.name === 'Articles'
                        ? 'bg-teal-600 text-white'
                        : 'text-slate-300 hover:bg-slate-700',
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


        <div v-if="showAddCategoryModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showAddCategoryModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">Nouvelle Catégorie</h3>
                <form @submit.prevent="addCategory" class="space-y-4">
                    <input v-model="newCategory.reference" placeholder="Référence"
                        class="w-full border border-slate-200 rounded-lg p-2 focus:ring-2 focus:ring-teal-500 outline-none"
                        required>
                    <input v-model="newCategory.name" placeholder="Nom"
                        class="w-full border border-slate-200 rounded-lg p-2 focus:ring-2 focus:ring-teal-500 outline-none"
                        required>
                    <textarea v-model="newCategory.description" placeholder="Description"
                        class="w-full border border-slate-200 rounded-lg p-2 focus:ring-2 focus:ring-teal-500 outline-none"
                        rows="3"></textarea>
                    <div class="flex gap-2">
                        <button type="button" @click="showAddCategoryModal = false"
                            class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showEditCategoryModal && editingCategory"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showEditCategoryModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">Modifier la Catégorie</h3>
                <form @submit.prevent="updateCategory" class="space-y-4">
                    <label class="block text-sm font-medium">Référence</label>
                    <input v-model="editingCategory.reference" class="w-full border p-2 rounded" required>

                    <label class="block text-sm font-medium">Nom</label>
                    <input v-model="editingCategory.name" class="w-full border p-2 rounded" required>

                    <label class="block text-sm font-medium">Description</label>
                    <textarea v-model="editingCategory.description" class="w-full border p-2 rounded"></textarea>

                    <div class="flex gap-2">
                        <button type="button" @click="showEditCategoryModal = false"
                            class="flex-1 py-2 border rounded">Annuler</button>
                        <button type="submit" class="flex-1 py-2 bg-teal-600 text-white rounded">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="ml-52 flex-1">
            <header
                class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4 text-slate-500">
                    <Link :href="route('gestionnaire.dashboard')" class="hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <span class="font-medium">Articles</span>
                </div>
                <div class="flex items-center gap-2 text-slate-700 hover:text-teal-600 cursor-pointer group">
                        <div class="text-sm font-medium text-slate-700">{{ userName }}</div>
                        <div class="w-9 h-9 flex items-center justify-center bg-slate-100 rounded-full group-hover:bg-teal-50 transition-colors">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                    </div>
            </header>

            <main class="p-8 space-y-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Gestion de vos articles</h2>
                        <p class="text-slate-500 text-sm">Ajouter, modifier , supprimer des articles</p>
                    </div>
                    <button @click="showAddModal = true"
                        class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nouveau Article
                    </button>

                    <div v-if="showAddModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                    </div>

                    <div v-if="showEditModal && editingArticle"
                        class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showEditModal = false">
                        </div>
                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-blue-50">
                                <h3 class="text-lg font-bold text-slate-800">Modifier l'article</h3>
                                <button @click="showEditModal = false"
                                    class="text-slate-400 hover:text-slate-600">&times;</button>
                            </div>

                            <form @submit.prevent="updateArticle" class="p-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nom de
                                        l'article</label>
                                    <input v-model="editingArticle.name" type="text" required
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Catégorie</label>
                                        <input v-model="editingArticle.category" type="text"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Statut</label>
                                        <input v-model="editingArticle.status" type="text"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Stock</label>
                                        <input v-model="editingArticle.stock" type="number"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Seuil
                                            (Lieu)</label>
                                        <input v-model="editingArticle.threshold" type="text"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                    </div>
                                </div>

                                <div class="pt-4 flex gap-3">
                                    <button type="button" @click="showEditModal = false"
                                        class="flex-1 px-4 py-2 border text-slate-600 rounded-lg hover:bg-slate-50">Annuler</button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700">Mettre
                                        à jour</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div v-if="showAddModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showAddModal = false">
                        </div>

                        <div
                            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
                            <div
                                class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                <h3 class="text-lg font-bold text-slate-800">Ajouter un nouvel article</h3>
                                <button @click="showAddModal = false"
                                    class="text-slate-400 hover:text-slate-600">&times;</button>
                            </div>

                            <form @submit.prevent="addArticle" class="p-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Référence</label>
                                    <input v-model="newArticle.reference" type="text" required
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nom de
                                        l'article</label>
                                    <input v-model="newArticle.name" type="text" required
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Catégorie</label>
                                        <input v-model="newArticle.category" type="text" placeholder="Ex: Fournitures"
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Seuil
                                            (Lieu)</label>
                                        <input v-model="newArticle.threshold" type="text" placeholder="Ex: Campus A"
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Status</label>
                                        <input v-model="newArticle.status" type="text" placeholder="Ex: En stock"
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Stock</label>
                                        <input v-model="newArticle.stock" type="number"
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                                    </div>
                                </div>

                                <div class="pt-4 flex gap-3">
                                    <button type="button" @click="showAddModal = false"
                                        class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors">
                                        Enregistrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-wrap gap-4 items-center">
                    <div class="relative flex-1 min-w-[200px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" placeholder="Rechercher un article..."
                            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>
                    <select
                        class="bg-slate-50 border border-slate-200 rounded-lg px-10 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-500">
                        <option>Type tous</option>
                    </select>
                    <select
                        class="bg-slate-50 border border-slate-200 rounded-lg px-10 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-500">
                        <option>Article Tous</option>
                    </select>
                    <select
                        class="bg-slate-50 border border-slate-200 rounded-lg px-10 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-500">
                        <option>Entrepot Tous</option>
                    </select>
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1">
                        <input type="date"
                            class="bg-transparent border-none text-sm text-slate-500 focus:ring-0 outline-none">
                        <span class="text-slate-400">-</span>
                        <input type="date"
                            class="bg-transparent border-none text-sm text-slate-500 focus:ring-0 outline-none">
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50 border-b border-slate-100 text-slate-600 text-xs uppercase font-semibold">
                                <th class="px-6 py-4">Reference</th>
                                <th class="px-6 py-4">Nom</th>
                                <th class="px-6 py-4">Categorie</th>
                                <th class="px-6 py-4">Stock Total</th>
                                <th class="px-6 py-4">Seuil</th>
                                <th class="px-6 py-4">Statut</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="art in articles" :key="art.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-blue-600">{{ art.reference }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ art.name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ art.category }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ art.stock }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ art.threshold }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full ">
                                        {{ art.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-3">
                                        <button @click="openEditModal(art)"
                                            class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button @click="deleteArticle(art.id)"
                                            class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex flex-col items-center gap-3">
                        <div class="px-8 py-5 flex flex-col items-center gap-2 bg-white border-t border-slate-100">
                            <div class="flex items-center gap-2">
                                <button class="text-slate-400 hover:text-teal-600 font-bold">&lt;</button>
                                <button
                                    class="w-8 h-8 bg-teal-600 text-white rounded flex items-center justify-center text-sm font-bold">1</button>
                                <button class="text-slate-400 hover:text-teal-600 font-bold">&gt;</button>
                            </div>
                            <span class="text-xs text-slate-500">1-3 Sur 3</span>
                        </div>
                    </div>
                </div>

                <main class="p-8 space-y-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800">Gestion des catégories</h2>
                            <p class="text-slate-500 text-sm">Ajouter, modifier , supprimer des articles</p>
                        </div>
                        <button @click="showAddCategoryModal = true"
                            class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Nouvelle Catégorie
                        </button>
                    </div>

                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-wrap gap-4 items-center">
                        <div class="relative flex-1 min-w-[200px]">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" placeholder="Rechercher une catégorie..."
                                class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>



                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1">
                            <input type="date"
                                class="bg-transparent border-none text-sm text-slate-500 focus:ring-0 outline-none">
                            <span class="text-slate-400">-</span>
                            <input type="date"
                                class="bg-transparent border-none text-sm text-slate-500 focus:ring-0 outline-none">
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-100 text-slate-600 text-xs uppercase font-semibold">
                                    <th class="px-6 py-4">Reference</th>
                                    <th class="px-6 py-4">Nom Catégories</th>
                                    <th class="px-6 py-4">Descriptions</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="cat in categories" :key="cat.id" class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-blue-600">{{ cat.reference }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-700">{{ cat.name }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ cat.description }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-3">
                                            <button @click="openEditCategoryModal(cat)"
                                                class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button @click="deleteCategory(cat.id)"
                                                class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
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
                            </tbody>
                        </table>

                        <div
                            class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex flex-col items-center gap-3">
                            <div class="px-8 py-5 flex flex-col items-center gap-2 bg-white border-t border-slate-100">
                                <div class="flex items-center gap-2">
                                    <button class="text-slate-400 hover:text-teal-600 font-bold">&lt;</button>
                                    <button
                                        class="w-8 h-8 bg-teal-600 text-white rounded flex items-center justify-center text-sm font-bold">1</button>
                                    <button class="text-slate-400 hover:text-teal-600 font-bold">&gt;</button>
                                </div>
                                <span class="text-xs text-slate-500">1-3 Sur 3</span>
                            </div>
                        </div>
                    </div>
                </main>

            </main>
        </div>
    </div>
</template>