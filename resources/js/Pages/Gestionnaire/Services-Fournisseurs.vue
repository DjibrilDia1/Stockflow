<script setup>
import GestionnaireLayout from '@/Layouts/GestionnaireLayout.vue';
defineOptions({ layout: GestionnaireLayout });

import { ref, computed } from 'vue';
import { Link, router, usePage, useForm } from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Gestionnaire');

const props = defineProps({
    services: {
        type: Object,
        default: () => ({ data: [], links: [], total: 0 })
    },
    fournisseurs: {
        type: Object,
        default: () => ({ data: [], links: [], total: 0 })
    },
});

const activeTab = ref('services');
const showModal = ref(false);
const isEditing = ref(false);

// Recherche
const searchService = ref('');
const searchFournisseur = ref('');

const filteredServices = computed(() =>
    props.services?.data?.filter(s =>
        (s.ser_nom ?? '').toLowerCase().includes(searchService.value.toLowerCase()) ||
        (s.ser_code ?? '').toLowerCase().includes(searchService.value.toLowerCase())
    ) ?? []
);

const filteredFournisseurs = computed(() =>
    props.fournisseurs?.data?.filter(f =>
        (f.fou_nom ?? '').toLowerCase().includes(searchFournisseur.value.toLowerCase()) ||
        (f.fou_email ?? '').toLowerCase().includes(searchFournisseur.value.toLowerCase())
    ) ?? []
);

// Formulaires
const serviceForm = useForm({
    ser_id: null,
    ser_nom: '',
    ser_code: '',
    ser_type: '',
});

const fournisseurForm = useForm({
    fou_id: null,
    fou_nom: '',
    fou_email: '',
    fou_telephone: '',
    fou_adresse: '',
});

// Logique Modal
const openAddModal = () => {
    isEditing.value = false;
    if (activeTab.value === 'services') {
        serviceForm.reset();
        serviceForm.clearErrors();
    } else {
        fournisseurForm.reset();
        fournisseurForm.clearErrors();
    }
    showModal.value = true;
};

const openEditModal = (item) => {
    isEditing.value = true;
    if (activeTab.value === 'services') {
        serviceForm.clearErrors();
        serviceForm.ser_id = item.ser_id;
        serviceForm.ser_nom = item.ser_nom;
        serviceForm.ser_code = item.ser_code;
        serviceForm.ser_type = item.ser_type ?? '';
    } else {
        fournisseurForm.clearErrors();
        fournisseurForm.fou_id = item.fou_id;
        fournisseurForm.fou_nom = item.fou_nom;
        fournisseurForm.fou_email = item.fou_email;
        fournisseurForm.fou_telephone = item.fou_telephone;
        fournisseurForm.fou_adresse = item.fou_adresse;
    }
    showModal.value = true;
};

const deleteItem = (id) => {
    if (confirm('Confirmer la suppression ?')) {
        if (activeTab.value === 'services') {
            router.delete(route('gestionnaire.services.destroy', id));
        } else {
            router.delete(route('gestionnaire.fournisseurs.destroy', id));
        }
    }
};

const saveEntry = () => {
    if (activeTab.value === 'services') {
        if (isEditing.value) {
            serviceForm.put(route('gestionnaire.services.update', serviceForm.ser_id), {
                onSuccess: () => showModal.value = false
            });
        } else {
            serviceForm.post(route('gestionnaire.services.store'), {
                onSuccess: () => showModal.value = false
            });
        }
    } else {
        if (isEditing.value) {
            fournisseurForm.put(route('gestionnaire.fournisseurs.update', fournisseurForm.fou_id), {
                onSuccess: () => showModal.value = false
            });
        } else {
            fournisseurForm.post(route('gestionnaire.fournisseurs.store'), {
                onSuccess: () => showModal.value = false
            });
        }
    }
};
</script>

<template>
    <main class="p-8 space-y-6">
        <div class="flex justify-between items-end">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Services & Fournisseurs</h2>
                <p class="text-slate-500 text-sm">Gérez les entités internes et vos partenaires externes</p>
            </div>
            <button @click="openAddModal"
                class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ activeTab === 'services' ? 'Ajouter un Service' : 'Nouveau Fournisseur' }}
            </button>
        </div>

        <!-- ===================== MODAL ===================== -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <!-- Fond sombre -->
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>

                <!-- Boîte du modal -->
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">

                    <!-- En-tête -->
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">
                            <template v-if="activeTab === 'services'">
                                {{ isEditing ? 'Modifier le service' : 'Nouveau Service' }}
                            </template>
                            <template v-else>
                                {{ isEditing ? 'Modifier le fournisseur' : 'Nouveau Fournisseur' }}
                            </template>
                        </h3>
                        <button @click="showModal = false"
                            class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
                    </div>

                    <!-- ---- Formulaire SERVICE ---- -->
                    <form v-if="activeTab === 'services'" @submit.prevent="saveEntry" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nom du service</label>
                            <input v-model="serviceForm.ser_nom" type="text" placeholder="Ex: Direction Informatique"
                                required
                                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                                :class="{ 'border-red-500': serviceForm.errors.ser_nom }">
                            <div v-if="serviceForm.errors.ser_nom" class="text-red-500 text-xs mt-1">
                                {{ serviceForm.errors.ser_nom }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Code</label>
                            <input v-model="serviceForm.ser_code" type="text" placeholder="Ex: DIR-INF"
                                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                                :class="{ 'border-red-500': serviceForm.errors.ser_code }">
                            <div v-if="serviceForm.errors.ser_code" class="text-red-500 text-xs mt-1">
                                {{ serviceForm.errors.ser_code }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Type</label>
                            <input v-model="serviceForm.ser_type" type="text" placeholder="Ex: Administratif, Technique..."
                                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                                :class="{ 'border-red-500': serviceForm.errors.ser_type }">
                            <div v-if="serviceForm.errors.ser_type" class="text-red-500 text-xs mt-1">
                                {{ serviceForm.errors.ser_type }}
                            </div>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="showModal = false"
                                class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                Annuler
                            </button>
                            <button type="submit" :disabled="serviceForm.processing"
                                class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors shadow-md disabled:opacity-60">
                                {{ isEditing ? 'Mettre à jour' : 'Créer le service' }}
                            </button>
                        </div>
                    </form>

                    <!-- ---- Formulaire FOURNISSEUR ---- -->
                    <form v-else @submit.prevent="saveEntry" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nom / Raison sociale</label>
                            <input v-model="fournisseurForm.fou_nom" type="text" placeholder="Ex: SEN-Informatique"
                                required
                                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                                :class="{ 'border-red-500': fournisseurForm.errors.fou_nom }">
                            <div v-if="fournisseurForm.errors.fou_nom" class="text-red-500 text-xs mt-1">
                                {{ fournisseurForm.errors.fou_nom }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                            <input v-model="fournisseurForm.fou_email" type="email"
                                placeholder="Ex: contact@fournisseur.sn"
                                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                                :class="{ 'border-red-500': fournisseurForm.errors.fou_email }">
                            <div v-if="fournisseurForm.errors.fou_email" class="text-red-500 text-xs mt-1">
                                {{ fournisseurForm.errors.fou_email }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Téléphone</label>
                                <input v-model="fournisseurForm.fou_telephone" type="text"
                                    placeholder="Ex: 338606060"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                                    :class="{ 'border-red-500': fournisseurForm.errors.fou_telephone }">
                                <div v-if="fournisseurForm.errors.fou_telephone" class="text-red-500 text-xs mt-1">
                                    {{ fournisseurForm.errors.fou_telephone }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Adresse</label>
                                <input v-model="fournisseurForm.fou_adresse" type="text"
                                    placeholder="Ex: Sicap Liberté 6, Dakar"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                                    :class="{ 'border-red-500': fournisseurForm.errors.fou_adresse }">
                                <div v-if="fournisseurForm.errors.fou_adresse" class="text-red-500 text-xs mt-1">
                                    {{ fournisseurForm.errors.fou_adresse }}
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="showModal = false"
                                class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                Annuler
                            </button>
                            <button type="submit" :disabled="fournisseurForm.processing"
                                class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors shadow-md disabled:opacity-60">
                                {{ isEditing ? 'Mettre à jour' : 'Créer le fournisseur' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </Teleport>
        <!-- ================================================= -->

        <!-- Tabs Navigation -->
        <div class="flex gap-1 bg-slate-100 p-1 rounded-xl w-fit">
            <button @click="activeTab = 'services'" :class="[
                'flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all',
                activeTab === 'services'
                    ? 'bg-white text-teal-700 shadow-sm border border-slate-200'
                    : 'text-slate-500 hover:text-slate-700'
            ]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Services Internes
                <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold"
                    :class="activeTab === 'services' ? 'bg-teal-100 text-teal-700' : 'bg-slate-200 text-slate-500'">
                    {{ props.services?.total ?? 0 }}
                </span>
            </button>
            <button @click="activeTab = 'fournisseurs'" :class="[
                'flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all',
                activeTab === 'fournisseurs'
                    ? 'bg-white text-teal-700 shadow-sm border border-slate-200'
                    : 'text-slate-500 hover:text-slate-700'
            ]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Fournisseurs
                <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold"
                    :class="activeTab === 'fournisseurs' ? 'bg-teal-100 text-teal-700' : 'bg-slate-200 text-slate-500'">
                    {{ props.fournisseurs?.total ?? 0 }}
                </span>
            </button>
        </div>

        <!-- Table Services -->
        <div v-show="activeTab === 'services'"
            class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <div class="relative max-w-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input v-model="searchService" type="text" placeholder="Rechercher par nom ou code..."
                        class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none bg-slate-50" />
                </div>
            </div>

            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase">
                    <tr>
                        <th class="px-6 py-4">Nom du Service</th>
                        <th class="px-6 py-4">Code</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Utilisateurs</th>
                        <th class="px-6 py-4">Date de création</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="s in filteredServices" :key="s.ser_id" class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ s.ser_nom }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ s.ser_code || '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ s.ser_type || '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ s.users_count || 0 }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ s.ser_created_at ? new Date(s.ser_created_at).toLocaleDateString() : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button @click="openEditModal(s)"
                                    class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button @click="deleteItem(s.ser_id)"
                                    class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredServices.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400 text-sm">
                            Aucun service trouvé pour « {{ searchService }} »
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex flex-col items-center gap-2">
                <div class="flex items-center gap-1">
                    <Link preserve-scroll v-for="(link, k) in (props.services?.links ?? [])" :key="k"
                        :href="link.url || '#'" v-html="link.label" class="px-3 py-1 text-sm rounded transition-all"
                        :class="{ 'bg-teal-600 text-white font-bold': link.active, 'text-slate-400 hover:text-teal-600': !link.active && link.url, 'text-slate-300 cursor-not-allowed': !link.url }" />
                </div>
                <span class="text-xs text-slate-400">{{ props.services?.from ?? 0 }}–{{ props.services?.to ?? 0 }} sur
                    {{ props.services?.total ?? 0 }} services</span>
            </div>
        </div>

        <!-- Table Fournisseurs -->
        <div v-show="activeTab === 'fournisseurs'"
            class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <div class="relative max-w-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input v-model="searchFournisseur" type="text" placeholder="Rechercher par nom ou email..."
                        class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none bg-slate-50" />
                </div>
            </div>

            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase">
                    <tr>
                        <th class="px-6 py-4">Nom / Raison Sociale</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Adresse</th>
                        <th class="px-6 py-4">Date de création</th>
                        <th class="px-6 py-4">Téléphone</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="f in filteredFournisseurs" :key="f.fou_id"
                        class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ f.fou_nom }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ f.fou_email || '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ f.fou_adresse || '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ f.fou_created_at ? new Date(f.fou_created_at).toLocaleDateString() : '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ f.fou_telephone || '—' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button @click="openEditModal(f)"
                                    class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button @click="deleteItem(f.fou_id)"
                                    class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredFournisseurs.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400 text-sm">
                            Aucun fournisseur trouvé pour « {{ searchFournisseur }} »
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex flex-col items-center gap-2">
                <div class="flex items-center gap-1">
                    <Link preserve-scroll v-for="(link, k) in (props.fournisseurs?.links ?? [])" :key="k"
                        :href="link.url || '#'" v-html="link.label" class="px-3 py-1 text-sm rounded transition-all"
                        :class="{ 'bg-teal-600 text-white font-bold': link.active, 'text-slate-400 hover:text-teal-600': !link.active && link.url, 'text-slate-300 cursor-not-allowed': !link.url }" />
                </div>
                <span class="text-xs text-slate-400">{{ props.fournisseurs?.from ?? 0 }}–{{ props.fournisseurs?.to ?? 0
                    }} sur {{ props.fournisseurs?.total ?? 0 }} fournisseurs</span>
            </div>
        </div>
    </main>
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