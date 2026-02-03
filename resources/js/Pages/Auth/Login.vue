<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion"/>

    <div class="flex min-h-screen">
        <div class="flex w-full flex-col justify-center px-8 md:w-1/2 lg:px-24 bg-white">
            <div class="mb-12">
                <h1 class="text-3xl font-extrabold tracking-tight">
                    <span class="text-blue-600">Stock</span><span class="text-green-500">Flow</span>
                </h1>
            </div>

            <h2 class="text-2xl font-bold text-teal-700 leading-snug mb-2">
                Une visibilité totale sur vos consommables, <br> en un seul endroit.
            </h2>
            <p class="text-gray-500 mb-8">Bienvenue ! Connectez vous a votre compte</p>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="email" value="Adresse email" class="text-gray-700 font-medium" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full bg-gray-100 border-none rounded-lg h-12 focus:ring-teal-500"
                        v-model="form.email"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-6">
                    <InputLabel for="password" value="Mot de passe" class="text-gray-700 font-medium" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full bg-gray-100 border-none rounded-lg h-12 focus:ring-teal-500"
                        v-model="form.password"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" class="text-teal-600" />
                        <span class="ms-2 text-sm text-gray-500">Se souvenir</span>
                    </label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-gray-500 hover:text-gray-800"
                    >
                        Mot de passe oublie ?
                    </Link>
                </div>

                <div class="mt-10 flex">
                    <PrimaryButton
                        class="w-full justify-center bg-teal-700 hover:bg-teal-800 py-3 rounded-lg text-lg normal-case border-none transition"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                    Se connecter
                </PrimaryButton>

    </div>
            </form>
        </div>

        <div class="hidden md:flex md:w-1/2 bg-gray-50 flex-col items-center justify-center p-12">
            <div class="absolute top-8 right-12 flex space-x-8 text-sm font-medium text-gray-600">
                <a href="#" class="border-b-2 border-teal-600 pb-1 text-gray-900">Accueil</a>
                <a href="#">À propos de nous</a>
                <a href="#">Blog</a>
                <a href="#">Tarification</a>
            </div>

            <img 
                src="/images/login-illustration.png" 
                alt="Illustration" 
                class="max-w-md w-full"
            />
        </div>
    </div>
</template>