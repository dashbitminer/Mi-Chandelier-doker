<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

</script>
<template>
  <!-- <nav> -->
  <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
    Dashboard
  </NavLink>
  <NavLink :href="route('projects.index')" :active="route().current('projects')">
    Projects
  </NavLink>
  <NavLink :href="route('travel-requests.index')" :active="route().current('travel-requests')">
    Travel Requests
  </NavLink>
</template>