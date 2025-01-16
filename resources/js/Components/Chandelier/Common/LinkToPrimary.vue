<template>
  <Link
    class="rounded-md p-2 m-2 ms-0
    inline-block
    transition
    transition-all
    ease-in-out
    text-gray-100
    uppercase
    text-sm
    font-medium
    bg-cyan-600
    w-fit
    shadow-sm
    active:bg-cyan-800
    active:shadow-lg">
    <slot>
    </slot>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
