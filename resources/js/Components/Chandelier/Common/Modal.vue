<template>
  <fwb-modal v-if="isOpen == true" @close="$emit('close-modal')">

    <template #header>
      <div class="flex items-center text-lg">
        <slot name="header"></slot>
      </div>
    </template>
    <template #body>
      <slot></slot>
    </template>
    <template #footer>
      <div class="flex justify-between" v-if="$slots.footer">
        <slot name="footer"></slot>
      </div>
    </template>
  </fwb-modal>
</template>

<script setup>
import { ref } from 'vue'
import { FwbButton, FwbModal } from 'flowbite-vue'

defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  }
});

</script>
