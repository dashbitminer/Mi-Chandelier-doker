<template>
  <td class="py-4 my-4 font-light">
    {{ value }}
  </td>
</template>
<script setup>

defineOptions({
  name: 'TableDataItem'
})

defineProps({
  value: {
    type: String,
    required: true
  },
  deletable: {
    type: Boolean,
    required: false,
  }
})

</script>