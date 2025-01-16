<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GoogleSignIn from "@/Pages/Auth/GoogleSignIn.vue";

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

function loginWithGoogle() {
  window.location.href = '/auth/google';
}

const submit = () => {
  form.transform(data => ({
    ...data,
    remember: form.remember ? 'on' : '',
  })).post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <Head title="Log in"/>

  <AuthenticationCard>
    <template #logo>
      <img class="w-96" src="/chandelier-logo-2.png"/>
    </template>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div>
        <InputLabel for="email" value="Email"/>
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="username"
        />
        <InputError class="mt-2" :message="form.errors.email"/>
      </div>

      <div class="mt-4">
        <InputLabel for="password" value="Contraseña"/>
        <TextInput
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="current-password"
        />
        <InputError class="mt-2" :message="form.errors.password"/>
      </div>


      <div class="block mt-4">
        <label class="flex items-center">
          <Checkbox v-model:checked="form.remember" name="remember"/>
          <span class="ms-2 text-sm text-gray-600">Mantener sesión</span>
        </label>
      </div>

      <div class="g-signin2" data-width="300" data-height="200" data-longtitle="true"></div>

      <div class="flex items-center justify-end mt-4">
        <Link v-if="canResetPassword" :href="route('password.request')"
              class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          Olvidaste tu contraseña?
        </Link>
        <GoogleSignIn
          @click="loginWithGoogle"/>
        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Iniciar Sesión
        </PrimaryButton>
      </div>
    </form>
    <template #logoBottom>
      <img class="w-16 mt-6" src="/chandelier-logo.png"/>
    </template>
  </AuthenticationCard>
</template>
