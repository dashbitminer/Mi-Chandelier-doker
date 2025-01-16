<template>
  <Link
    class="rounded-md p-2 mx-1 text-white transition transition-all ease-in-out bg-lime-500 w-fit shadow-sm active:bg-lime-600 active:shadow-lg">
    <slot>
    </slot>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
