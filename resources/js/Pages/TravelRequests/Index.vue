<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';


defineProps({
    team: Object,
    availableRoles: Array,
    permissions: Object,
    travel_requests: Object,
});
</script>

<template>
  <AppLayout :team="team">
    <div class="container mx-auto">
      <table class="min-w-full bg-white">
        <thead>
          <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
            <th class="py-3 px-6 text-left">ID</th>
            <th class="py-3 px-6 text-left">Name</th>
            <th class="py-3 px-6 text-left">description</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
          <tr v-for="travel_requests in travel_requests" :key="travel_request.id" class="border-b border-gray-200 hover:bg-gray-100">
            <td class="py-3 px-6 text-left">{{ travel_requests.id }}</td>
            <td class="py-3 px-6 text-left">{{ travel_requests.name }}</td>
            <td class="py-3 px-6 text-left">{{ travel_requests.description }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>