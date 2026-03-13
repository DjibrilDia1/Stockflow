<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Toast from '@/Components/Toast.vue';


const props = defineProps({
    stockMovements: Object,
    articles: Array,
    warehouses: Array,
    suppliers: Array,
});

const showAddMouvementModal = ref(false);

const movementForm = useForm({
    mvs_date_mouvement: new Date().toISOString().slice(0, 10),
    mvs_type: 'IN',
    mvs_art_id: '',
    mvs_ent_id: '',
    mvs_ent_dest_id: '', // Pour les transferts
    mvs_fou_id: '', // Pour les entrées
    mvs_quantite: 1,
    mvs_motif: ''
});


const openModal = (type = 'IN') => {
    movementForm.clearErrors();
    movementForm.mvs_date_mouvement = new Date().toISOString().slice(0, 10);
    movementForm.mvs_type = type;
    movementForm.mvs_art_id = '';
    movementForm.mvs_ent_id = '';
    movementForm.mvs_ent_dest_id = '';
    movementForm.mvs_quantite = 1;
    movementForm.mvs_motif = '';
    showAddMouvementModal.value = true;
};

// --- ACTION D'AJOUT ---
const addMouvement = () => {
    movementForm.post(route('gestionnaire.mouvements.store'), {
        onSuccess: () => {
            showAddMouvementModal.value = false;
            movementForm.reset();
        }
    });
};


const deleteMouvement = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce mouvement ? Cette action est irréversible.')) {
        router.delete(route('gestionnaire.mouvements.destroy', id));
    }
};


const navigation = [
    { name: 'Tableau de bord', route: 'gestionnaire.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', route: 'gestionnaire.articles.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', route: 'gestionnaire.mouvements.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', route: 'gestionnaire.demandes.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports', route: 'gestionnaire.rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Utilisateur', route: 'gestionnaire.utilisateurs.index', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { name: 'Services & Fournisseurs', route: 'gestionnaire.services-fournisseurs.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
];





const getTypeClass = (type) => {


    const baseClass = "px-3 py-1.5 rounded-md text-xs font-bold uppercase block text-center mx-auto max-w-[120px] border";





    if (type === 'IN') return `${baseClass} bg-teal-50 text-teal-700 border-teal-100`;


    if (type === 'TRANSFER') return `${baseClass} bg-blue-50 text-blue-700 border-blue-100`;


    if (type === 'ADJUST') return `${baseClass} bg-orange-50 text-orange-700 border-orange-100`;


    if (type === 'OUT') return `${baseClass} bg-red-50 text-red-700 border-red-100`;





    return `${baseClass} bg-slate-100 text-slate-600 border-slate-200`;


};





const logout = () => {


    if (confirm('Déconnexion ?')) router.post(route('logout'));


};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <Toast />
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
            <header class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex justify-between">
                <div class="flex items-center gap-4 text-slate-500">
                    <Link :href="route('gestionnaire.dashboard')" class="hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <span class="font-medium">Mouvements</span>
                </div>
                <div class="flex items-center gap-2 text-slate-700">
                    <span class="text-sm font-medium">Gestionnaire de compte</span>
                    <div class="w-9 h-9 bg-slate-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </header>

            <main class="p-8 space-y-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Gestion des mouvements</h2>
                        <p class="text-slate-500 text-sm">Gérer les entrées, sorties et ajustements de stock</p>
                    </div>
                    <button @click="showAddMouvementModal = true"
                        class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nouveau mouvement
                    </button>


                    <div v-if="showAddMouvementModal"
                        class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                            @click="showAddMouvementModal = false"></div>

                        <div
                            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
                            <div
                                class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                <h3 class="text-lg font-bold text-slate-800">Enregistrer un mouvement</h3>
                                <button @click="showAddMouvementModal = false"
                                    class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
                            </div>

                            <form @submit.prevent="addMouvement" class="p-6 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Date</label>
                                        <input v-model="movementForm.mvs_date_mouvement" type="date"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                                            :class="{ 'border-red-500': movementForm.errors.mvs_date_mouvement }">
                                        <div v-if="movementForm.errors.mvs_date_mouvement"
                                            class="text-red-500 text-xs mt-1">{{ movementForm.errors.mvs_date_mouvement
                                            }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Type de
                                            mouvement</label>
                                        <select v-model="movementForm.mvs_type"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                            :class="{ 'border-red-500': movementForm.errors.mvs_type }">
                                            <option value="IN">Entrée</option>
                                            <option value="OUT">Sortie</option>
                                            <option value="TRANSFER">Transfert</option>
                                            <option value="ADJUST">Ajustement</option>
                                        </select>
                                        <div v-if="movementForm.errors.mvs_type" class="text-red-500 text-xs mt-1">{{
                                            movementForm.errors.mvs_type }}</div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Article</label>
                                    <select v-model="movementForm.mvs_art_id"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                        :class="{ 'border-red-500': movementForm.errors.mvs_art_id }">
                                        <option value="" disabled>Sélectionner un article</option>
                                        <option v-for="art in articles" :key="art.art_id" :value="art.art_id">
                                            {{ art.art_nom }} ({{ art.art_reference }})
                                        </option>
                                    </select>
                                    <div v-if="movementForm.errors.mvs_art_id" class="text-red-500 text-xs mt-1">{{
                                        movementForm.errors.mvs_art_id }}</div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                                            {{ movementForm.mvs_type === 'TRANSFER' ? 'Entrepôt Source' : 'Entrepôt / Lieu' }}</label>
                                        <select v-model="movementForm.mvs_ent_id"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                            :class="{ 'border-red-500': movementForm.errors.mvs_ent_id }">
                                            <option value="" disabled>Sélectionner</option>
                                            <option v-for="ent in warehouses" :key="ent.ent_id" :value="ent.ent_id">
                                                {{ ent.ent_nom }}
                                            </option>
                                        </select>
                                        <div v-if="movementForm.errors.mvs_ent_id" class="text-red-500 text-xs mt-1">{{
                                            movementForm.errors.mvs_ent_id }}</div>
                                    </div>
                                    <div v-if="movementForm.mvs_type === 'TRANSFER'">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Entrepôt
                                            Destination</label>
                                        <select v-model="movementForm.mvs_ent_dest_id"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                            :class="{ 'border-red-500': movementForm.errors.mvs_ent_dest_id }">
                                            <option value="" disabled>Sélectionner</option>
                                            <option v-for="ent in warehouses" :key="ent.ent_id" :value="ent.ent_id">
                                                {{ ent.ent_nom }}
                                            </option>
                                        </select>
                                        <div v-if="movementForm.errors.mvs_ent_dest_id"
                                            class="text-red-500 text-xs mt-1">{{ movementForm.errors.mvs_ent_dest_id }}
                                        </div>
                                    </div>
                                    <div v-if="movementForm.mvs_type === 'IN'">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Fournisseur
                                            (Optionnel)</label>
                                        <select v-model="movementForm.mvs_fou_id"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm">
                                            <option value="">Aucun</option>
                                            <option v-for="fou in suppliers" :key="fou.fou_id" :value="fou.fou_id">
                                                {{ fou.fou_nom }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Quantité</label>
                                        <input v-model.number="movementForm.mvs_quantite" type="number"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                                            :class="{ 'border-red-500': movementForm.errors.mvs_quantite }">
                                        <div v-if="movementForm.errors.mvs_quantite" class="text-red-500 text-xs mt-1">
                                            {{ movementForm.errors.mvs_quantite }}</div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Motif /
                                        Raison</label>
                                    <textarea v-model="movementForm.mvs_motif" rows="2"
                                        placeholder="Ex: Réapprovisionnement mensuel"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                                        :class="{ 'border-red-500': movementForm.errors.mvs_motif }"></textarea>
                                    <div v-if="movementForm.errors.mvs_motif" class="text-red-500 text-xs mt-1">{{
                                        movementForm.errors.mvs_motif }}</div>
                                </div>

                                <div class="pt-4 flex gap-3">
                                    <button type="button" @click="showAddMouvementModal = false"
                                        class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors shadow-md"
                                        :disabled="movementForm.processing">
                                        {{ movementForm.processing ? 'Traitement...' : 'Confirmer le mouvement' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-wrap gap-4 items-center">
                    <div class="relative flex-1 min-w-[200px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" placeholder="Rechercher un mouvement..."
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

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <button @click="openModal('IN')"
                        class="flex items-center justify-center gap-2 p-3 bg-teal-50 text-teal-700 rounded-lg font-semibold hover:bg-teal-100 transition-colors border border-teal-100">
                        <span class="text-xl">+</span> Entrée
                    </button>

                    <button @click="openModal('OUT')"
                        class="flex items-center justify-center gap-2 p-3 bg-red-50 text-red-700 rounded-lg font-semibold hover:bg-red-100 transition-colors border border-red-100">
                        <span class="text-xl">-</span> Sortie
                    </button>

                    <button @click="openModal('ADJUST')"
                        class="flex items-center justify-center gap-2 p-3 bg-orange-50 text-orange-700 rounded-lg font-semibold hover:bg-orange-100 transition-colors border border-orange-100">
                        <span class="text-sm">⚠️</span> Ajustement
                    </button>

                    <button @click="openModal('TRANSFER')"
                        class="flex items-center justify-center gap-2 p-3 bg-blue-50 text-blue-700 rounded-lg font-semibold hover:bg-blue-100 transition-colors border border-blue-100">
                        <span class="text-sm">🔄</span> Transfert
                    </button>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden">
                    <div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden">
                        <div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-slate-50 border-b border-slate-100 text-slate-600 text-xs uppercase font-semibold">
                                        <th class="px-6 py-4">Date</th>
                                        <th class="px-6 py-4 text-center">Type</th>
                                        <th class="px-6 py-4">Article</th>
                                        <th class="px-6 py-4">Entrepôt</th>
                                        <th class="px-6 py-4">Quantité</th>
                                        <th class="px-6 py-4">Motif</th>
                                        <th class="px-6 py-4 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="movement in props.stockMovements.data" :key="movement.mvs_id"
                                        class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ new
                                            Date(movement.mvs_date_mouvement).toLocaleDateString('fr-FR') }}</td>

                                        <td class="px-6 py-4">
                                            <span :class="getTypeClass(movement.mvs_type)">
                                                {{ movement.mvs_type }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ movement.item ?
                                            movement.item.art_nom : 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-500">{{ movement.warehouse ?
                                            movement.warehouse.ent_nom : 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm font-bold"
                                            :class="movement.mvs_quantite > 0 ? 'text-emerald-600' : 'text-red-600'">
                                            {{ movement.mvs_quantite > 0 ? '+' : '' }}{{ movement.mvs_quantite }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500 italic">{{ movement.mvs_motif }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button @click="deleteMouvement(movement.mvs_id)"
                                                class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Supprimer le mouvement">
                                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="props.stockMovements.links && props.stockMovements.links.length > 3"
                        class="px-6 py-4 bg-white border-t border-slate-100 flex flex-col items-center gap-2">
                        <div class="flex items-center gap-2">
                            <Link v-for="(link, k) in props.stockMovements.links" :key="k" :href="link.url || '#'"
                                v-html="link.label"
                                class="px-3 py-1 text-sm rounded flex items-center justify-center transition-all"
                                :class="{
                                    'bg-teal-600 text-white font-bold': link.active,
                                    'text-slate-400 hover:text-teal-600': !link.active && link.url,
                                    'text-slate-300 cursor-not-allowed': !link.url
                                }" />
                        </div>
                        <span class="text-xs text-slate-500">
                            {{ props.stockMovements.from }}-{{ props.stockMovements.to }} sur {{
                                props.stockMovements.total }}
                        </span>
                    </div>
                </div>

            </main>
        </div>
    </div>
</template>
