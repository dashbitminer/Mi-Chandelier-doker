<template>
  <Link
    class="rounded-md p-5 mx-1 transition transition-all ease-in-out w-fit shadow-sm active:shadow-lg">
    <slot>
    </slot>
  </Link>
</template>

<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
</script>
