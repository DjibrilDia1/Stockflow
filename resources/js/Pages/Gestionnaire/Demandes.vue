<script setup>
import { ref,computed } from 'vue';
import { Link, router , usePage } from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Gestionnaire');


const showAddDemandeModal = ref(false);

const newDemande = ref({
    ref: '', // Génère une ref simple
    demandeur: 'Entrée',
    entrepot: '',
    date: new Date().toISOString().substr(0, 10), // Date du jour par défaut
    statut: 'Brouillon',
    detail: ''
});

const addDemande = () => {
    const demandeToAdd = {
        ...newDemande.value,
        id: Date.now(),
        // On définit la classe selon le statut par défaut
        statutClass: 'bg-amber-100 text-amber-600'
    };

    demandes.value.push(demandeToAdd);

    // Fermeture et reset
    showAddDemandeModal.value = false;
    newDemande.value = {
        ref: 'DREQ-' + Math.floor(Math.random() * 1000),
        demandeur: 'Entrée',
        entrepot: '',
        date: new Date().toISOString().substr(0, 10),
        statut: 'Brouillon',
        detail: ''
    };
};

const deleteDemandes = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette demande ? Cette action est irréversible.')) {
        // On cible 'demandes.value' et non 'mouvements.value'
        demandes.value = demandes.value.filter(item => item.id !== id);
    }
};

// Données fictives
const demandes = ref([
    { id: 1, ref: 'DREQ-001', demandeur: 'Entrée', entrepot: 'Fournitures', date: '3/04/2024', statut: 'Brouillon', statutClass: 'bg-amber-100 text-amber-600', detail: '' },
    { id: 2, ref: 'DREQ-002', demandeur: 'Transfert', entrepot: 'Fournitures', date: '13/04/2024', statut: 'Validée', statutClass: 'bg-teal-600 text-white', detail: '+ Stylos noirs x 20' },
    { id: 3, ref: 'DREQ-003', demandeur: 'Ajustement', entrepot: 'Mobilier', date: '15/04/2024', statut: 'Servie', statutClass: 'bg-emerald-500 text-white', detail: '+ Cartouches d’encre x 10' },
    { id: 4, ref: 'DREQ-004', demandeur: 'Sortie', entrepot: 'Mobilier', date: '15/04/2024', statut: 'Rejetée', statutClass: 'bg-red-500 text-white', detail: '+ Besoin non justifiée' },
]);

const navigation = [
    { name: 'Tableau de bord', route: 'gestionnaire.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', route: 'gestionnaire.articles.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', route: 'gestionnaire.mouvements.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', route: 'gestionnaire.demandes.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports', route: 'gestionnaire.rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Utilisateur', route: 'gestionnaire.utilisateurs.index', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { name: 'Services & Fournisseurs',route:'gestionnaire.services-fournisseurs.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },

];

const getTypeClass = (type) => {
    // Classes de base identiques pour un design cohérent
    const baseClass = "px-3 py-1.5 rounded-md text-xs font-bold uppercase block text-center mx-auto max-w-[120px] border";

    if (type === 'Entrée') return `${baseClass} bg-teal-50 text-teal-700 border-teal-100`;
    if (type === 'Transfert') return `${baseClass} bg-blue-50 text-blue-700 border-blue-100`;
    if (type === 'Ajustement') return `${baseClass} bg-orange-50 text-orange-700 border-orange-100`;
    if (type === 'Sortie') return `${baseClass} bg-red-50 text-red-700 border-red-100`;

    return `${baseClass} bg-slate-100 text-slate-600 border-slate-200`;
};

const logout = () => {
    if (confirm('Déconnexion ?')) router.post(route('logout'));
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

            <nav class="px-3 py-6 space-y-1.5 flex-1 overflow-y-auto">
                <Link v-for="item in navigation" :key="item.name" :href="item.route ? route(item.route) : '#'" :class="[
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

        <div class="ml-52 flex-1 flex flex-col">
            <header
                class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4 text-slate-500">
                    <Link :href="route('gestionnaire.mouvements.index')" class="text-slate-400 hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <span class="font-medium">Demandes</span>
                </div>
                <div class="flex items-center gap-2 text-slate-700 hover:text-teal-600 cursor-pointer group">
                        <div class="text-sm font-medium text-slate-700">{{ userName }}</div>
                        <div class="w-9 h-9 flex items-center justify-center bg-slate-100 rounded-full group-hover:bg-teal-50 transition-colors">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                    </div>
            </header>

            <main class="p-10 space-y-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Demandes de retrait</h2>
                        <p class="text-slate-500 mt-1">Gérer les demandes de retrait d'articles</p>
                    </div>
                    <button @click="showAddDemandeModal = true"
                        class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-sm">
                        <span class="text-xl font-light">+</span> Nouvelle demande
                    </button>
                    <div v-if="showAddDemandeModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                            @click="showAddDemandeModal = false"></div>

                        <div
                            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
                            <div
                                class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                <h3 class="text-lg font-bold text-slate-800">Nouvelle demande de retrait</h3>
                                <button @click="showAddDemandeModal = false"
                                    class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
                            </div>

                            <form @submit.prevent="addDemande" class="p-6 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Référence</label>
                                        <input v-model="newDemande.ref" type="text"
                                            class="w-full px-4 py-2 border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Date</label>
                                        <input v-model="newDemande.date" type="date" required
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Type
                                            (Demandeur)</label>
                                        <select v-model="newDemande.demandeur"
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                                            <option value="Entrée">Entrée</option>
                                            <option value="Sortie">Sortie</option>
                                            <option value="Transfert">Transfert</option>
                                            <option value="Ajustement">Ajustement</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Entrepôt</label>
                                        <select v-model="newDemande.entrepot" required
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                                            <option value="" disabled>Choisir...</option>
                                            <option value="Fournitures">Fournitures</option>
                                            <option value="Mobilier">Mobilier</option>
                                            <option value="Informatique">Informatique</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Détails des
                                        articles</label>
                                    <textarea v-model="newDemande.detail" rows="3"
                                        placeholder="Ex: Stylos noirs x 20, Rames de papier x 5..."
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"></textarea>
                                </div>

                                <div class="pt-4 flex gap-3">
                                    <button type="button" @click="showAddDemandeModal = false"
                                        class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors shadow-md">
                                        Enregistrer la demande
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-wrap gap-4 items-center">
                    <div class="relative flex-1 max-w-xs">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" placeholder="Rechercher une demande..."
                            class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
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

                <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-4">Reference</th>
                                <th class="px-6 py-4 text-center">Type</th>
                                <th class="px-6 py-4 text-center">Entrepot</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Statut</th>
                                <th class="px-6 py-4">Details</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
    <tr v-for="item in demandes" :key="item.id" class="hover:bg-slate-50 transition-colors group">
        <td class="px-6 py-5 text-sm font-medium text-blue-500 underline cursor-pointer">{{ item.ref }}</td>
        
        <td class="px-6 py-5">
            <span :class="getTypeClass(item.demandeur)">{{ item.demandeur }}</span>
        </td>
        
        <td class="px-6 py-5 text-sm font-bold text-slate-700 text-center">{{ item.entrepot }}</td>
        
        <td class="px-6 py-5 text-sm text-slate-500">{{ item.date }}</td>
        
        <td class="px-6 py-5">
            <span :class="['px-4 py-1.5 rounded-md text-xs font-bold block min-w-[100px] text-center', item.statutClass]">
                {{ item.statut }}
            </span>
        </td>

        <td class="px-6 py-5 text-xs text-teal-600 font-semibold italic">
            {{ item.detail || '-' }}
        </td>

        <td class="px-6 py-4 text-center">
            <button @click="deleteDemandes(item.id)" class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </td>
    </tr>
</tbody>
                    </table>

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
            </main>
        </div>
    </div>
</template>

<style scoped>
table {
    border-spacing: 0;
}
</style>