<template>
  <div class="error-message">
    <ErrorMessage :name="name" class="text-red-600 text-sm" />
  </div>
</template>

<script setup>
import { ErrorMessage } from 'vee-validate';

const props = defineProps({
  name: {
    type: String,
    required: true,
  },
});
</script>
