<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const visible = ref(false);
const message = ref('');
const type = ref('success');

let timeoutId = null;

const showToast = (msg, msgType) => {
    if (!msg) return;
    message.value = msg;
    type.value = msgType;
    visible.value = true;
    
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
        visible.value = false;
    }, 5000);
};

// Surveiller les changements globaux de la page
watch(() => usePage().props.flash, (flash) => {
    if (flash && flash.success) {
        showToast(flash.success, 'success');
    } else if (flash && flash.error) {
        showToast(flash.error, 'error');
    }
}, { deep: true });

onMounted(() => {
    const flash = usePage().props.flash;
    if (flash && flash.success) {
        showToast(flash.success, 'success');
    } else if (flash && flash.error) {
        showToast(flash.error, 'error');
    }
});
</script>

<template>
    <div class="fixed top-4 right-4 z-[9999] space-y-2 max-w-sm w-full pointer-events-none px-4 sm:px-0">
        <Transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="visible" class="pointer-events-auto bg-white border-l-4 shadow-2xl rounded-lg overflow-hidden flex items-center p-4"
                :class="type === 'success' ? 'border-teal-500' : 'border-red-500'">
                <div class="flex-shrink-0">
                    <svg v-if="type === 'success'" class="h-6 w-6 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-bold" :class="type === 'success' ? 'text-teal-900' : 'text-red-900'">
                        {{ type === 'success' ? 'Succès' : 'Erreur' }}
                    </p>
                    <p class="text-sm text-slate-600">
                        {{ message }}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <button @click="visible = false" class="text-slate-400 hover:text-slate-500 focus:outline-none">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>
