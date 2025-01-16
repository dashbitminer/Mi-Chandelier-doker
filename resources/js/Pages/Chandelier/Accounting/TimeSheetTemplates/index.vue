<template>
  <Head>
    <title>Hojas de tiempo</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Hojas de tiempo</AreaTitle>

      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Periodo</TableHeaderItem>
          <TableHeaderItem></TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="timeSheetTemplate in timeSheetTemplates.data" :key="timeSheetTemplate.id">
          <fwb-table-cell>{{ timeSheetTemplate.title }}</fwb-table-cell>
          <fwb-table-cell>
            <LinkToPrimary v-if="timeSheetTemplate.status === 'unpublish'" :href="goToEdit(timeSheetTemplate.id)">
              Editar
            </LinkToPrimary>
            <LinkToPrimary v-if="timeSheetTemplate.status === 'publish'" :href="goToTimeSheetStatusesReport(timeSheetTemplate.id)">
              Ver reporte
            </LinkToPrimary>
            <LinkToPrimary v-if="timeSheetTemplate.status === 'publish'" :href="goToTimeSheetsByProjectsReport(timeSheetTemplate.id)">
              Imprimir por proyectos
            </LinkToPrimary>
            <LinkToPrimary v-if="timeSheetTemplate.status === 'publish'" :href="goToTimeSheetsByUsersReport(timeSheetTemplate.id)">
              Imprimir por empleados
            </LinkToPrimary>
          </fwb-table-cell>

        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination v-model="currentPageExpired" :total-items="timeSheetTemplates.total" :per-page="5"
                      show-icons :show-labels="false"/>
    </ChandelierPage>
  </main>
</template>

<script setup>
import { ref } from 'vue';
import { Link, Head } from '@inertiajs/vue3'; // Firstly import the Link and Head components
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
  timeSheetTemplates: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry();

const currentPageActive = ref(1);
const currentPageExpired = ref(1);

const goToEdit = (id) => {
  return route('accounting.time-sheet-templates.edit', {time_sheet_template: id, country: currentCountry.value });
}

const goToTimeSheetStatusesReport = (id) => {
  return route('accounting.time-sheet-templates.time-sheet-statuses.index', {time_sheet_template: id, country: currentCountry.value });
}

const goToTimeSheetsByProjectsReport = (id) => {
  return route('accounting.time-sheet-templates.time-sheets.index', { time_sheet_template: id, country: currentCountry.value });
}

const goToTimeSheetsByUsersReport = (id) => {
  return route('accounting.time-sheet-templates.time-sheets-users.index', { time_sheet_template: id, country: currentCountry.value });
}

const publish = () => {

}
</script>
