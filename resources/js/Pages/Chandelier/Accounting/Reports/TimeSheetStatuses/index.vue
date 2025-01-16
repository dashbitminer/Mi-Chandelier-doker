<template>
  <Head>
    <title>Hojas de tiempo</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Reporte hojas de tiempo</AreaTitle>
      {{timeSheetTemplate.title}}

      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Proyecto</TableHeaderItem>
          <TableHeaderItem></TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="project in projects.data" :key="project.id">
          <fwb-table-cell>{{ project.name }}</fwb-table-cell>
          <fwb-table-cell>
            <LinkToPrimary :href="goToPreview(project.project_id)">Ver</LinkToPrimary>
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
import { Link, Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import { useCountry } from '@/Composables/useCountry';

import LinkToPrimary from "@/Components/Chandelier/Common/LinkToPrimary.vue";
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import TableHeaderItem from "@/Components/Chandelier/Common/TableHeaderItem.vue";
import MainLayout from "@/Layouts/MainLayout.vue";

import {
  FwbA,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
  FwbPagination,
} from 'flowbite-vue';

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  timeSheetTemplate: {
    type: Object,
    required: true
  },
  projects: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry();

const currentPage = ref(props.projects.meta.current_page || 1)

const handlePageChange = (newPage) => {
  router.get(route('accounting.time-sheet-templates.time-sheet-statuses.index', { page: newPage, time_sheet_template: props.timeSheetTemplate.id, country: currentCountry.value }), {}, { preserveScroll: true });
};

const goToPreview = (id) => {
  return route('accounting.time-sheet-templates.time-sheet-statuses.preview', {
    time_sheet_template: props.timeSheetTemplate.id, project: id, country: currentCountry.value
  });
}
</script>
