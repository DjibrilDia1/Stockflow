<script setup>
import { ref, watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    articles: {
        type: Array,
        default: () => []
    },
    initialArticleId: [Number, String],
});

const emit = defineEmits(['close']);

const form = useForm({
    article_id: props.initialArticleId || '',
    entrepot_id: '',
    quantite: 1,
    motif: '',
});

// Surveiller le changement d'article initial
watch(() => props.initialArticleId, (newId) => {
    if (newId) form.article_id = newId;
}, { immediate: true });

// Trouver l'article sélectionné pour afficher ses entrepôts
const selectedArticleData = computed(() => {
    if (!props.articles) return null;
    return props.articles.find(a => a.id == form.article_id);
});

// Filtrer les entrepôts : afficher uniquement ceux avec stock > 0
const availableWarehouses = computed(() => {
    if (!selectedArticleData.value || !selectedArticleData.value.warehouses) return [];
    return selectedArticleData.value.warehouses.filter(w => w.qty > 0);
});

// Trouver l'entrepôt sélectionné pour vérifier son stock
const selectedWarehouse = computed(() => {
    return availableWarehouses.value.find(w => w.id == form.entrepot_id);
});

// Vérifier si la quantité demandée est valide
const isQuantityInvalid = computed(() => {
    if (!selectedWarehouse.value) return false;
    return form.quantite > selectedWarehouse.value.qty;
});

// Reset l'entrepôt si l'article change
watch(() => form.article_id, () => {
    form.entrepot_id = '';
});

const submit = () => {
    if (isQuantityInvalid.value) {
        return;
    }
    form.post(route('demandeur.demandes.store'), {
        onSuccess: () => {
            emit('close');
            form.reset();
        },
        preserveScroll: true
    });
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="$emit('close')"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800">Nouvelle demande de retrait</h3>
                <button @click="$emit('close')" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-5">
                <!-- Sélection de l'article -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Article</label>
                    <select v-model="form.article_id" required :disabled="!!initialArticleId"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none disabled:bg-slate-100"
                        :class="form.errors.article_id ? 'border-red-500' : 'border-slate-200'">
                        <option value="" disabled>Choisir un article...</option>
                        <option v-for="art in articles" :key="art.id" :value="art.id">
                            {{ art.nom }}
                        </option>
                    </select>
                    <p v-if="form.errors.article_id" class="text-red-500 text-xs mt-1">{{ form.errors.article_id }}</p>
                </div>

                <!-- Sélection de l'entrepôt (filtré) -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Entrepôt de retrait</label>
                    <select v-model="form.entrepot_id" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none"
                        :class="form.errors.entrepot_id ? 'border-red-500' : 'border-slate-200'">
                        <option value="" disabled>Sélectionner un entrepôt...</option>
                        <option v-for="ent in availableWarehouses" :key="ent.id" :value="ent.id">
                            {{ ent.name }} (Disponible: {{ ent.qty }})
                        </option>
                    </select>
                    <p v-if="form.errors.entrepot_id" class="text-red-500 text-xs mt-1">{{ form.errors.entrepot_id }}</p>
                    <p v-if="form.article_id && availableWarehouses.length === 0" class="text-red-500 text-xs mt-1 italic">
                        Aucun stock disponible pour cet article dans les entrepôts.
                    </p>
                </div>

                <!-- Quantité -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Quantité demandée</label>
                    <input v-model="form.quantite" type="number" min="1" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 outline-none"
                        :class="(isQuantityInvalid || form.errors.quantite) ? 'border-red-500 focus:ring-red-200' : 'border-slate-200 focus:ring-teal-500'">
                    <p v-if="form.errors.quantite" class="text-red-500 text-xs mt-1">{{ form.errors.quantite }}</p>
                    <p v-if="isQuantityInvalid" class="text-red-500 text-xs mt-1 font-medium">
                        Attention : Stock insuffisant (Max: {{ selectedWarehouse?.qty }})
                    </p>
                </div>

                <!-- Motif -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Motif / Commentaire</label>
                    <textarea v-model="form.motif" rows="3" placeholder="Ex: Besoins pour le service administratif..."
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm"
                        :class="form.errors.motif ? 'border-red-500' : 'border-slate-200'"></textarea>
                    <p v-if="form.errors.motif" class="text-red-500 text-xs mt-1">{{ form.errors.motif }}</p>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="$emit('close')"
                        class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" :disabled="form.processing || !form.entrepot_id || isQuantityInvalid"
                        class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700 transition-colors shadow-md disabled:opacity-50">
                        {{ form.processing ? 'Envoi...' : 'Confirmer la demande' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
