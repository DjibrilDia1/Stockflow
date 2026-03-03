<script setup>
import { ref, computed } from 'vue';
import { Link, router ,usePage} from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Gestionnaire');

// État pour basculer entre les deux onglets
const activeTab = ref('services'); // 'services' ou 'fournisseurs'

const showModal = ref(false);
const isEditing = ref(false);

// Formulaire générique qui s'adapte selon l'onglet
const formData = ref({
    id: null,
    nom: '',
    contact: '',
    email: '',
    adresse: '',
    responsable: '', // Spécifique Service
    categorie: 'Fournitures de bureau' ,// Spécifique Fournisseur
    etage: '',  
    effectif: 0
});

// Données fictives
const services = ref([
    { id: 1, nom: 'Logistique', responsable: 'Amadou Diallo', effectif: 12, etage: 'RDC' },
    { id: 2, nom: 'Ressources Humaines', responsable: 'Fatou Sarr', effectif: 5, etage: '2ème' },
    { id: 3, nom: 'Direction Générale', responsable: 'Ibrahim Sow', effectif: 3, etage: '4ème' },
]);

const fournisseurs = ref([
    { id: 1, nom: 'Bureau Vallée', contact: '77 123 45 67', email: 'contact@bvallee.sn', categorie: 'Fournitures' },
    { id: 2, nom: 'Senelec', contact: '33 800 00 00', email: 'pro@senelec.sn', categorie: 'Énergie' },
    { id: 3, nom: 'Top Informatique', contact: '70 987 65 43', email: 'sales@topinfo.sn', categorie: 'Informatique' },
]);

// Logique d'ouverture Modal
const openAddModal = () => {
    isEditing.value = false;
    formData.value = { 
        id: null, 
        nom: '', 
        contact: '', 
        email: '', 
        adresse: '', 
        responsable: '', 
        categorie: '',
        etage: '', 
        effectif: 0 
    };
    showModal.value = true;
};

const openEditModal = (item) => {
    isEditing.value = true;
    formData.value = { ...item };
    showModal.value = true;
};

const deleteItem = (id) => {
    if (confirm('Confirmer la suppression ?')) {
        if (activeTab.value === 'services') {
            services.value = services.value.filter(s => s.id !== id);
        } else {
            fournisseurs.value = fournisseurs.value.filter(f => f.id !== id);
        }
    }
};

const saveEntry = () => {
    if (isEditing.value) {
        if (activeTab.value === 'services') {
            const index = services.value.findIndex(s => s.id === formData.value.id);
            if (index !== -1) {
                // On remplace l'élément à l'index donné par les nouvelles données
                services.value.splice(index, 1, { ...formData.value });
            }
        } else {
            const index = fournisseurs.value.findIndex(f => f.id === formData.value.id);
            if (index !== -1) {
                fournisseurs.value.splice(index, 1, { ...formData.value });
            }
        }
    } else {
        // Logique d'ajout
        const newEntry = { ...formData.value, id: Date.now() };
        if (activeTab.value === 'services') {
            services.value.push(newEntry);
        } else {
            fournisseurs.value.push(newEntry);
        }
    }
    showModal.value = false;
};

const navigation = [
    { name: 'Tableau de bord', route: 'gestionnaire.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', route: 'gestionnaire.articles.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', route: 'gestionnaire.mouvements.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', route: 'gestionnaire.demandes.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports', route: 'gestionnaire.rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Utilisateur', route: 'gestionnaire.utilisateurs.index', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { name: 'Services & Fournisseurs',route:'gestionnaire.services-fournisseurs.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
];

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
                <Link v-for="item in navigation" :key="item.name" :href="route(item.route)"
    :class="[
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
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Déconnexion
                </button>
            </div>
        </aside>

        <div class="ml-52 flex-1">
            <header class="bg-white border-b border-slate-200 sticky top-0 z-10 px-8 py-4 flex justify-between">
                <div class="flex items-center gap-4 text-slate-500">
                    <Link :href="route('gestionnaire.utilisateurs.index')" class="hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <span class="font-medium">Référentiel</span>
                </div>
            </header>

            <main class="p-8 space-y-6">
                <div class="flex justify-between items-end">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Services & Fournisseurs</h2>
                        <p class="text-slate-500 text-sm">Gérez les entités internes et vos partenaires externes</p>
                    </div>
                    <button @click="openAddModal" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        {{ activeTab === 'services' ? 'Ajouter un Service' : 'Nouveau Fournisseur' }}
                    </button>
                </div>

                <div class="flex gap-2 p-1 bg-slate-200/50 rounded-xl w-fit">
                    <button @click="activeTab = 'services'" 
                        :class="[activeTab === 'services' ? 'bg-white text-teal-600 shadow-sm' : 'text-slate-500 hover:text-slate-700']"
                        class="px-6 py-2 rounded-lg text-sm font-bold transition-all">
                        Services Internes
                    </button>
                    <button @click="activeTab = 'fournisseurs'" 
                        :class="[activeTab === 'fournisseurs' ? 'bg-white text-teal-600 shadow-sm' : 'text-slate-500 hover:text-slate-700']"
                        class="px-6 py-2 rounded-lg text-sm font-bold transition-all">
                        Fournisseurs
                    </button>
                </div>

                <div v-if="activeTab === 'services'" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase">
                            <tr>
                                <th class="px-6 py-4">Nom du Service</th>
                                <th class="px-6 py-4">Responsable</th>
                                <th class="px-6 py-4">Localisation</th>
                                <th class="px-6 py-4 text-center">Effectif</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="s in services" :key="s.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-700">{{ s.nom }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ s.responsable }}</td>
                                <td class="px-6 py-4 text-sm"><span class="bg-slate-100 px-2 py-1 rounded text-slate-500">{{ s.etage }}</span></td>
                                <td class="px-6 py-4 text-center text-sm font-medium">{{ s.effectif }} pers.</td>
                                <td class="px-6 py-4 text-right">
                                    
                                    <button @click="openEditModal(s)" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
    </svg>
</button>
<button @click="deleteItem(s.id)" class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
    </svg>
</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <table class="w-full text-left">
        <thead class="bg-slate-50 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase">
            <tr>
                <th class="px-6 py-4">Raison Sociale</th>
                <th class="px-6 py-4">Catégorie</th>
                <th class="px-6 py-4">Contact</th>
                <th class="px-6 py-4">Email</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            <tr v-for="f in fournisseurs" :key="f.id" class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4 font-bold text-slate-700">{{ f.nom }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase">{{ f.categorie }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ f.contact }}</td>
                <td class="px-6 py-4 text-sm text-teal-600 underline">{{ f.email }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <button @click="openEditModal(f)" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <button @click="deleteItem(f.id)" class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
                </div>
            </main>
        </div>

        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800">{{ isEditing ? 'Modifier' : 'Ajouter' }} {{ activeTab === 'services' ? 'le Service' : 'le Fournisseur' }}</h3>
                        <button @click="showModal = false" class="text-slate-400 text-2xl">&times;</button>
                    </div>
                    <form @submit.prevent="saveEntry" class="p-6 space-y-4">
                        <div>
    <label class="block text-sm font-semibold text-slate-700 mb-1">Nom / Raison Sociale</label>
    <input v-model="formData.nom" type="text" required class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-teal-500">
</div>

<div v-if="activeTab === 'services'" class="space-y-4">
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Responsable</label>
        <input v-model="formData.responsable" type="text" class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-teal-500">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Localisation (Étage)</label>
            <input v-model="formData.etage" type="text" placeholder="ex: 2ème étage" class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Effectif</label>
            <input v-model="formData.effectif" type="number" class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-teal-500">
        </div>
    </div>
</div>
<div v-if="activeTab === 'fournisseurs'" class="space-y-4">
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Catégorie d'activité</label>
        <input 
            v-model="formData.categorie" 
            type="text" 
            placeholder="ex: Plomberie, Sécurité, etc."
            class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-teal-500"
        >
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Téléphone</label>
            <input v-model="formData.contact" type="text" class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
            <input v-model="formData.email" type="email" class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-teal-500">
        </div>
    </div>
</div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="showModal = false" class="flex-1 py-2 border border-slate-200 rounded-lg font-semibold text-slate-600">Annuler</button>
                            <button type="submit" class="flex-1 py-2 bg-teal-600 text-white rounded-lg font-semibold shadow-md">{{ isEditing ? 'Mettre à jour' : 'Enregistrer' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </div>
</template>