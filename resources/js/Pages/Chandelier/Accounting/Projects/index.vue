<template>
  <Head>
    <title>Proyectos</title>
  </Head>

  <main>
    <Head title="Proyectos" />
    <ChandelierPage class="mt-8">
      <AreaTitle>Proyectos</AreaTitle>

      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Nombre</TableHeaderItem>
          <TableHeaderItem>Trabaja fin de semana?</TableHeaderItem>
          <TableHeaderItem>Hoja de tiempo habilitada?</TableHeaderItem>
          <TableHeaderItem class="w-60"></TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="project in projects.data" :key="project.id">
          <fwb-table-cell>{{ project.name }}</fwb-table-cell>
          <fwb-table-cell>{{ project.requireWorkingWeekendLabel }}</fwb-table-cell>
          <fwb-table-cell>{{ project.requireTimeSheetLabel }}</fwb-table-cell>
          <fwb-table-cell>
            <span class="mr-5">{{ project.requireTimeSheet ? 'Deshabilitar' : 'Habilitar' }}</span>
            <fwb-toggle
              v-model="project.requireTimeSheet"
              @change="updateProject(project.id)"
            />
            <LinkToPrimary :href="goToEdit(project.id)">Editar</LinkToPrimary>
          </fwb-table-cell>

        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination
        :model-value="currentPage"
        :total-items="projects.meta.total"
        :per-page="projects.meta.per_page"
        show-icons
        :show-labels="false"
        @update:model-value="handlePageChange($event)"
      />
    </ChandelierPage>
  </main>
</template>

<script setup>
import { ref } from 'vue';
import { router, Head } from '@inertiajs/vue3'
import axios from 'axios';

import LinkToPrimary from "@/Components/Chandelier/Common/LinkToPrimary.vue";
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import TableHeaderItem from "@/Components/Chandelier/Common/TableHeaderItem.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import { useCountry } from '@/Composables/useCountry';

import {
  FwbA,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
  FwbPagination,
  FwbToggle,
} from 'flowbite-vue';

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  projects: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry()

const currentPage = ref(props.projects.meta.current_page || 1)

const updateProject = (id) => {
  axios.put(route('accounting.projects.toggle', { project: id, country: currentCountry.value }))
    .then((response) => {
      if(response.status === 200){

        const index = props.projects.data.findIndex(project => project.id === response.data.id);
        if (index !== -1)
          props.projects.data[index] = response.data;

      }
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}

const handlePageChange = (newPage) => {
  router.get(route('accounting.projects.index', { page: newPage, country: currentCountry.value }), {}, { preserveScroll: true });
};

const goToEdit = (id) => {
  return route('accounting.projects.edit', {project: id, country: currentCountry.value });
}
</script>
