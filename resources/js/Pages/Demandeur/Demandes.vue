<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage , useForm } from '@inertiajs/vue3';

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
    if (confirm('Déconnexion ?')) router.post(route('logout'));
};

const showModal = ref(false);

const form = useForm({
    article_id: '',
    quantite: 1,
    motif: '',
    urgence: 'Normale'
});

const submitRequest = () => {
    // 1. Trouver le nom de l'article sélectionné pour l'affichage dans le tableau
    const articleChoisi = articlesDisponibles.value.find(a => a.id === form.article_id);

    // 2. Créer l'objet de la nouvelle demande
    const nouvelleDemande = {
        id: Date.now(), // Un ID unique basé sur l'heure
        ref: `DR-2026-0${Math.floor(Math.random() * 100)}`, // Référence aléatoire
        type: 'Retrait',
        article: articleChoisi ? articleChoisi.nom : 'Article inconnu',
        qty: form.quantite,
        date: new Date().toLocaleDateString('fr-FR'), // Date d'aujourd'hui
        status: 'En validation'
    };

    // 3. Ajouter la demande au début de la liste (tableau requests)
    requests.value.push(nouvelleDemande);

    // 4. Fermer le modal et remettre le formulaire à zéro
    showModal.value = false;
    form.reset();
    
    // Petit message de confirmation
    console.log("Demande ajoutée localement !");
};
const articlesDisponibles = ref([
    { id: 1, nom: 'Papier A4' },
    { id: 2, nom: 'Stylos noirs' },
    { id: 3, nom: 'Cartouches d\'encre' },
    { id: 4, nom: 'Classeurs' },
]);
    
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
                    <Link :href="route('demandeur.dashboard')" class="text-slate-400 hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <span class="font-medium">Demandes</span>
                </div>
                <div class="text-sm font-medium text-slate-700">{{ userName }}</div>
            </header>

            <main class="p-10 space-y-8">
                <div>
                        <h2 class="text-2xl font-bold text-slate-900">Demandes de retrait</h2>
                        <p class="text-slate-500 mt-1">Suivi de vos demandes et historique</p>
                    </div>
                <section class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex flex-col md:flex-row gap-3">
                        <input
                            type="text"
                            placeholder="Rechercher une demande..."
                            class="flex-1 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none"
                        >
                        <button 
    @click="showModal = true"
    class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm flex items-center gap-2"
>
    <span class="text-lg">+</span> Nouvelle demande
</button>
                    </div>
                    <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Créer une demande de retrait</h3>
            <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>

        <form @submit.prevent="submitRequest" class="p-6 space-y-5">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Article demandé</label>
                <select v-model="form.article_id" required
                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                    <option value="" disabled>Sélectionner un article...</option>
                    <option v-for="art in articlesDisponibles" :key="art.id" :value="art.id">
                        {{ art.nom }}
                    </option>
                </select>
                <div v-if="form.errors.article_id" class="text-red-500 text-xs mt-1">{{ form.errors.article_id }}</div>
            </div>

           
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Quantité</label>
                    <input v-model="form.quantite" type="number" min="1" required
                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                </div>
            

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Motif de la demande</label>
                <textarea v-model="form.motif" rows="3" 
                    placeholder="Ex: Utilisation pour le service RH..."
                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"></textarea>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" @click="showModal = false"
                    class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                    Annuler
                </button>
                <button type="submit" :disabled="form.processing"
                    class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors shadow-md disabled:opacity-50">
                    {{ form.processing ? 'Envoi...' : 'Envoyer la demande' }}
                </button>
            </div>
        </form>
    </div>
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
