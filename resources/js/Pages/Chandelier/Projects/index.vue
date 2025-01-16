<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue'
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";

defineProps({
  team: Object,
  availableRoles: Array,
  permissions: Object,
  projects: Object,
});

defineOptions({
  layout: MainLayout,
});

</script>

<template>
  <ChandelierPage>
    <AreaTitle>Proyectos</AreaTitle>
    <div class="container mx-auto">
      <table class="min-w-full bg-white">
        <thead>
        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
          <th class="py-3 px-6 text-left">Nombre</th>
          <th class="py-3 px-6 text-left">Descripción</th>
        </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
        <tr v-for="project in projects" :key="project.id" class="border-b border-gray-200 hover:bg-gray-100">

          <td class="py-3 px-6 text-left">{{ project.name }}</td>
          <td class="py-3 px-6 text-left">{{ project.description }}</td>
        </tr>
        </tbody>
      </table>
    </div>
  </ChandelierPage>
</template>
