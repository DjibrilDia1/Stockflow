<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted } from 'vue';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const page = usePage();

const redirectIfAuthenticated = () => {
    if (page.props.auth?.user) {
        router.visit(route('dashboard'), { replace: true });
    }
};

onMounted(() => {
    redirectIfAuthenticated();
    window.addEventListener('pageshow', redirectIfAuthenticated);
});

onBeforeUnmount(() => {
    window.removeEventListener('pageshow', redirectIfAuthenticated);
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion" />

    <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50/30 to-teal-50/40">
        <!-- Orbes décoratifs -->
        <div class="pointer-events-none absolute -left-32 top-32 h-96 w-96 rounded-full bg-teal-400/10 blur-3xl animate-float" />
        <div class="pointer-events-none absolute -right-32 bottom-20 h-96 w-96 rounded-full bg-blue-400/10 blur-3xl animate-float-delayed" />

        <div class="relative z-10 flex min-h-screen">
            <!-- Section Formulaire -->
            <section class="flex w-full items-center justify-center px-6 py-12 lg:w-1/2 lg:px-12">
                <div class="w-full max-w-md">
                    <!-- Logo et titre -->
                    <div class="mb-10 text-center">
                        <div class="mb-4 inline-flex items-center gap-2">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-teal-600 to-blue-600 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <h1 class="text-3xl font-bold tracking-tight">
                                <span class="bg-gradient-to-r from-teal-600 to-blue-600 bg-clip-text text-transparent">StockFlow</span>
                            </h1>
                        </div>
                        <p class="text-slate-600">Gestion intelligente de vos stocks et consommables</p>
                    </div>

                    <!-- Carte de connexion -->
                    <div class="rounded-2xl border border-slate-200/60 bg-white/90 p-8 shadow-xl backdrop-blur-sm">
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-slate-800">Bienvenue !</h2>
                            <p class="mt-2 text-sm text-slate-500">Connectez-vous pour accéder à votre espace</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-5">
                            <!-- Email -->
                            <div>
                                <InputLabel for="email" value="Adresse email" class="font-medium text-slate-700" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                        </svg>
                                    </div>
                                    <TextInput
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="block h-12 w-full rounded-xl border-slate-200 bg-slate-50/50 pl-11 transition focus:border-teal-500 focus:bg-white focus:ring-teal-500/20"
                                        placeholder="votre@email.com"
                                        required
                                        autofocus
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <!-- Mot de passe -->
                            <div>
                                <InputLabel for="password" value="Mot de passe" class="font-medium text-slate-700" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <TextInput
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        class="block h-12 w-full rounded-xl border-slate-200 bg-slate-50/50 pl-11 transition focus:border-teal-500 focus:bg-white focus:ring-teal-500/20"
                                        placeholder="••••••••"
                                        required
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <!-- Se souvenir / Mot de passe oublié -->
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2">
                                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded text-teal-600 transition focus:ring-teal-500" />
                                    <span class="text-sm text-slate-600">Se souvenir de moi</span>
                                </label>

                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-sm font-medium text-teal-600 transition hover:text-teal-700"
                                >
                                    Mot de passe oublié ?
                                </Link>
                            </div>

                            <!-- Bouton de connexion -->
                            <PrimaryButton
                                class="h-12 w-full justify-center rounded-xl bg-gradient-to-r from-teal-600 to-blue-600 text-base font-medium normal-case shadow-lg shadow-teal-600/25 transition hover:shadow-xl hover:shadow-teal-600/30"
                                :class="{ 'opacity-50': form.processing }"
                                :disabled="form.processing"
                            >
                                <svg v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Connexion...' : 'Se connecter' }}
                            </PrimaryButton>
                        </form>
                    </div>

                    <!-- Footer -->
                    <p class="mt-8 text-center text-xs text-slate-500">
                        En vous connectant, vous acceptez nos conditions d'utilisation
                    </p>
                </div>
            </section>

            <!-- Section Illustration -->
            <section class="relative hidden w-1/2 lg:flex">
                <!-- Gradient overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900/90 via-teal-900/85 to-blue-900/90" />

                <!-- Contenu -->
                <div class="relative z-10 flex w-full flex-col justify-between p-12">
                    <!-- En-tête -->
                    <div>
                        <h2 class="max-w-lg text-4xl font-bold leading-tight text-white">
                            Gérez vos stocks avec
                            <span class="bg-gradient-to-r from-teal-300 to-blue-300 bg-clip-text text-transparent"> simplicité et efficacité</span>
                        </h2>

                        <p class="mt-6 max-w-md text-lg text-white/80">
                            Une plateforme intuitive pour le suivi en temps réel de vos consommables, la prévention des ruptures et l'optimisation de vos approvisionnements.
                        </p>
                    </div>

                    <!-- Fonctionnalités -->
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm transition hover:bg-white/10">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-500/20">
                                <svg class="h-5 w-5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white">Suivi multi-entrepôts</h3>
                                <p class="mt-1 text-sm text-white/70">Visualisez vos stocks sur tous vos sites en temps réel</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm transition hover:bg-white/10">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-500/20">
                                <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white">Alertes automatiques</h3>
                                <p class="mt-1 text-sm text-white/70">Soyez prévenus avant les ruptures de stock</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm transition hover:bg-white/10">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-500/20">
                                <svg class="h-5 w-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white">Rapports détaillés</h3>
                                <p class="mt-1 text-sm text-white/70">Analysez vos consommations et optimisez vos achats</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center gap-8 border-t border-white/10 pt-6 text-sm text-white/60">
                        <span>© 2026 StockFlow</span>
                        <span>•</span>
                        <a href="#" class="transition hover:text-white">Aide</a>
                        <span>•</span>
                        <a href="#" class="transition hover:text-white">Documentation</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

@keyframes float-delayed {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-15px);
    }
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 10s ease-in-out infinite;
}
</style>