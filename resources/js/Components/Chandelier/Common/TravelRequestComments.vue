<template>
  <div v-if="comments.length > 0">
    <Heading>Observaciones</Heading>
    <ul>
      <li v-for="comment in comments" class="flex flex-col">
        <div class="border rounded-md p-3 my-3">
          <small class="font-semibold text-zinc-700">
            {{comment.date}}
          </small>
          <p class="text-gray-600 mt-2">
            {{comment.comment}}
          </p>
        </div>
      </li>
    </ul>
  </div>
</template>
<script setup>
import Heading from '@/Components/Chandelier/Common/Heading.vue';

const props = defineProps({
  comments: {
    type: Object,
    required: true,
  }
})
</script>
