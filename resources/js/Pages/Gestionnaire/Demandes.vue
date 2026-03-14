<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Toast from '@/Components/Toast.vue';

const page = usePage();
const showDetailModal = ref(false);
const selectedDemande = ref(null);

const openDetail = (demande) => {
    selectedDemande.value = demande;
    showDetailModal.value = true;
};
const userName = computed(() => page.props.auth?.user?.name ?? 'Gestionnaire');


const props = defineProps({
    withdrawRequests: Object,
});

const searchQuery = ref('');
const filterStatus = ref('');

const filteredDemandes = computed(() => {
    return props.withdrawRequests?.data?.filter(d => {
        const matchesSearch = !searchQuery.value || 
            (d.dso_code ?? '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (d.user?.name ?? '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (d.service?.ser_nom ?? '').toLowerCase().includes(searchQuery.value.toLowerCase());
        
        const matchesStatus = !filterStatus.value || d.dso_statut === filterStatus.value;

        return matchesSearch && matchesStatus;
    }) ?? [];
});

const showAddDemandeModal = ref(false);

const deleteDemandes = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette demande ? Cette action est irréversible.')) {
        router.delete(route('gestionnaire.demandes.destroy', id));
    }
};

const validateRequest = (id, status) => {
    const action = status === 'APPROVED' ? 'approuver' : 'rejeter';
    if (confirm(`Voulez-vous vraiment ${action} cette demande ?`)) {
        router.post(route('gestionnaire.demandes.validate', id), {
            status: status
        });
    }
};

const getStatusClass = (status) => {
    if (status === 'DRAFT') return 'bg-amber-100 text-amber-600';
    if (status === 'APPROVED') return 'bg-teal-600 text-white';
    if (status === 'FULFILLED') return 'bg-emerald-500 text-white';
    if (status === 'REJECTED') return 'bg-red-500 text-white';
    return 'bg-slate-100 text-slate-600';
};

const navigation = [
    { name: 'Tableau de bord', route: 'gestionnaire.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', route: 'gestionnaire.articles.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', route: 'gestionnaire.mouvements.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', route: 'gestionnaire.demandes.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports', route: 'gestionnaire.rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Utilisateurs', route: 'gestionnaire.utilisateurs.index', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { name: 'Services & Fournisseurs', route: 'gestionnaire.services-fournisseurs.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },

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
        <Toast />
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
                    <Link :href="route('gestionnaire.mouvements.index')"
                        class="text-slate-400 hover:text-teal-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <span class="font-medium">Demandes</span>
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

            <main class="p-10 space-y-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Demandes de retrait</h2>
                        <p class="text-slate-500 mt-1">Gérer les demandes de retrait d'articles</p>
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
                        <input v-model="searchQuery" type="text" placeholder="Rechercher une demande..."
                            class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>
                    <select v-model="filterStatus"
                        class="bg-slate-50 border border-slate-200 rounded-lg pl-4 pr-10 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-500 appearance-none bg-no-repeat bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%236b7280%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-[position:right_0.5rem_center] bg-[length:1.25em_1.25em]">
                        <option value="">Tous les statuts</option>
                        <option value="DRAFT">Brouillon</option>
                        <option value="APPROVED">Approuvé</option>
                        <option value="REJECTED">Rejeté</option>
                        <option value="FULFILLED">Terminé</option>
                    </select>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Service / Demandeur</th>
                                <th class="px-6 py-4">Articles</th>
                                <th class="px-6 py-4">Quantité</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Statut</th>
                                <th class="px-6 py-4 text-center">Détails</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="item in filteredDemandes" :key="item.dso_id"
                                class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-5 text-sm font-medium text-blue-500 underline cursor-pointer">#{{
                                    item.dso_id }}</td>

                                <td class="px-6 py-5">
                                    <div class="text-sm font-bold text-slate-700">{{ item.service?.ser_nom || 'Sans Service' }}</div>
                                    <div class="text-xs text-slate-500">Demandeur: {{ item.requester?.name || 'N/A' }}
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-sm text-slate-600">
                                    <div v-for="line in item.lines" :key="line.lds_id">
                                        • {{ line.item?.art_nom || 'Article inconnu' }}
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-sm text-slate-600">
                                    <div v-for="line in item.lines" :key="line.lds_id">
                                        {{ line.lds_qte_demandee }}
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-sm text-slate-500">{{ new Date(item.dso_created_at ||
                                    Date.now()).toLocaleDateString('fr-FR') }}</td>

                                <td class="px-6 py-5">
                                    <span
                                        :class="['px-3 py-1.5 rounded-md text-[10px] font-bold block min-w-[90px] text-center', getStatusClass(item.dso_statut)]">
                                        {{ item.dso_statut }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <button @click="openDetail(item)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-200 rounded-lg hover:bg-teal-600 hover:text-white hover:border-teal-600 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Voir plus
                                    </button>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <template v-if="item.dso_statut === 'DRAFT'">
                                            <button @click="validateRequest(item.dso_id, 'APPROVED')"
                                                class="p-1.5 bg-teal-50 text-teal-600 hover:bg-teal-600 hover:text-white rounded-lg transition-all title='Approuver'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button @click="validateRequest(item.dso_id, 'REJECTED')"
                                                class="p-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-all"
                                                title="Rejeter">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </template>
                                        <button @click="deleteDemandes(item.dso_id)"
                                            class="p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination Dynamique -->
                    <div class="px-8 py-5 flex flex-col items-center gap-2 bg-white border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <Link v-for="(link, k) in props.withdrawRequests.links" :key="k" :href="link.url || '#'"
                                v-html="link.label" class="px-3 py-1 text-sm rounded transition-all"
                                :class="{ 'bg-teal-600 text-white font-bold': link.active, 'text-slate-400 hover:text-teal-600': !link.active && link.url, 'text-slate-300 cursor-not-allowed': !link.url }" />
                        </div>
                        <span class="text-xs text-slate-500">{{ props.withdrawRequests.from }}-{{
                            props.withdrawRequests.to }} sur {{
                                props.withdrawRequests.total }}</span>
                    </div>
                </div>
                <Teleport to="body">
                    <div v-if="showDetailModal && selectedDemande"
                        class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
                            @click="showDetailModal = false"></div>

                        <div
                            class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">

                            <!-- En-tête -->
                            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-teal-50">
                                <div>
                                    <h3 class="text-sm font-bold text-teal-800 uppercase tracking-wider">
                                        Détails de la demande
                                    </h3>
                                    <p class="text-xs text-teal-600 mt-0.5">
                                        DSO-{{ String(selectedDemande.dso_id).padStart(5, '0') }}
                                    </p>
                                </div>
                                <button @click="showDetailModal = false"
                                    class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
                            </div>

                            <!-- Corps -->
                            <div class="p-6 space-y-5 max-h-[65vh] overflow-y-auto">

                                <!-- Infos générales -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Service</p>
                                        <p class="text-sm font-medium text-slate-700">
                                            {{ selectedDemande.service?.ser_nom || 'Sans service' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Demandeur</p>
                                        <p class="text-sm font-medium text-slate-700">
                                            {{ selectedDemande.requester?.name || 'N/A' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Date</p>
                                        <p class="text-sm font-medium text-slate-700">
                                            {{ new Date(selectedDemande.dso_created_at || Date.now()).toLocaleDateString('fr-FR') }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Statut</p>
                                        <span :class="['px-2.5 py-1 rounded-md text-[10px] font-bold uppercase', getStatusClass(selectedDemande.dso_statut)]">
                                            {{ selectedDemande.dso_statut }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Liste des articles -->
                                <div>
                                    <p class="text-xs text-slate-400 uppercase font-semibold mb-2">Articles demandés</p>
                                    <div class="rounded-lg border border-slate-200 overflow-hidden">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr class="bg-slate-50 text-xs text-slate-500 uppercase">
                                                    <th class="px-4 py-2 text-left font-semibold">Article</th>
                                                    <th class="px-4 py-2 text-center font-semibold">Qté demandée</th>
                                                    <th class="px-4 py-2 text-center font-semibold">Qté servie</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                <tr v-for="line in selectedDemande.lines" :key="line.lds_id"
                                                    class="hover:bg-slate-50">
                                                    <td class="px-4 py-2.5 text-slate-700 font-medium">
                                                        {{ line.item?.art_nom || 'Article inconnu' }}
                                                    </td>
                                                    <td class="px-4 py-2.5 text-center text-slate-600">
                                                        {{ line.lds_qte_demandee }}
                                                    </td>
                                                    <td class="px-4 py-2.5 text-center">
                                                        <span :class="line.lds_qte_servie > 0 ? 'text-teal-600 font-semibold' : 'text-slate-400'">
                                                            {{ line.lds_qte_servie ?? 0 }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr v-if="!selectedDemande.lines?.length">
                                                    <td colspan="3" class="px-4 py-3 text-center text-slate-400 text-xs">
                                                        Aucun article dans cette demande
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Pied -->
                            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                                <button @click="showDetailModal = false"
                                    class="px-5 py-2 bg-slate-800 text-white text-sm rounded-lg font-semibold hover:bg-slate-700 transition-colors">
                                    Fermer
                                </button>
                            </div>
                        </div>
                    </div>
                </Teleport>
            </main>
        </div>
    </div>
</template>

<style scoped>
table {
    border-spacing: 0;
}
</style>