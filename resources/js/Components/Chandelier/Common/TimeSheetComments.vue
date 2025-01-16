<template>
  <div v-if="comments.length > 0">
    <AreaTitle class="mb-4">Observaciones:</AreaTitle>
    <ul>
      <li v-for="comment in comments">
        <div class="border rounded-md p-3 my-3">
          <div class="flex gap-3 items-center">
            <p class="font-semibold text-zinc-700 text-sm">
              {{comment.date}}
            </p>
          </div>
          <p class="text-gray-600 mt-2">
            {{comment.comment}}
          </p>
        </div>
      </li>
    </ul>
  </div>
</template>
<script setup>
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";

const props = defineProps({
  comments: {
    type: Object,
    required: true,
  }
})
</script>
