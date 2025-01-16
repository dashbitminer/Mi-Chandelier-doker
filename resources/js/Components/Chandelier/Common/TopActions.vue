<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import CountrySelector from '@/Components/CountrySelector.vue';

const logout = () => {
  router.post(route('logout'));
};

const page = usePage()
const auth = computed(() => page.props.auth)

</script>
<template>
  <input id="search" type="text" class="rounded-md bg-slate-100 me-6 border-slate-400" name="" placeholder="Búsqueda"
         isrequired="false">
  <Link href="route('profile.show')">
    <button class="group m-2">
    <span
      class="text-slate-700 me-1 text-3xl mdi mdi-account-circle group-hover:text-primary-500 transition transition-all duration-150 ease-in-out "/>
      <span
        class="m-2 group-hover:text-primary-500 transition transition-all duration-150 ease-in-out ">
        {{ auth.user.name }}
      </span>
    </button>
  </Link>


  <!-- <select class="m-3 me-4 text-3xl transition transition-all transition transition-all duration-150 ease-in-out bg-opacity-0 ">🇸🇻</select> -->
  <CountrySelector />

  <button
    class="p-1 text-slate-700 mt-2 m-3 text-3xl mdi mdi-menu hover:text-primary-500  transition transition-all duration-150 ease-in-out"/>

  <button
    class="p-1 text-slate-700 mt-2 m-3 text-3xl mdi mdi-bell hover:text-primary-500  transition transition-all duration-150 ease-in-out"/>

  <form method="POST" @submit.prevent="logout">
    <button
      class="p-1 text-slate-700 mt-2 m-3 text-3xl mdi mdi-logout hover:text-primary-500 transition transition-all duration-150 ease-in-out"/>
  </form>

</template>
