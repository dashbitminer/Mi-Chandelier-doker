<script setup>
import { ref } from 'vue';
import { Link, Head } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import MainLayout from '@/Layouts/MainLayout.vue'; // Next, import the layout component
import { useCountry } from '@/Composables/useCountry';

// Then, import the components that will be used in the template
import AreaTitle from '@/Components/Chandelier/Common/AreaTitle.vue';
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue';
import TableDataItem from '@/Components/Chandelier/Common/TableDataItem.vue';
import TableHeaderItem from '@/Components/Chandelier/Common/TableHeaderItem.vue';
import LinkToPrimary from '@/Components/Chandelier/Common/LinkToPrimary.vue';

import {
  FwbA,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
  FwbPagination,
} from 'flowbite-vue';

// Finally, define the props and options objects and set the layout
defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  timeSheets: {
    type: Object,
    required: true,
  },
  timeSheetReviews: {
    type: Object,
    required: true,
  },
});

const { currentCountry } = useCountry();

const currentPageActive = ref(1)
const currentPageExpired = ref(1)

function goToDetail(id) {
  return route('time-sheets.show', { time_sheet: id, country: currentCountry.value });
}

function goToEdit(id) {
  return route('time-sheets.edit', { time_sheet: id, country: currentCountry.value });
}

function goToTimeSheetReview(id) {
  return route('time-sheet-reviews.show', { time_sheet_review: id, country: currentCountry.value });
}
function goToTimeSheetReviewEdit(id) {
  return route('time-sheet-reviews.edit', { time_sheet_review: id, country: currentCountry.value });
}

const isAbleTimeSheetEditLink = (status) => {
  return status === 'pending' || status === 'incomplete';
}

const isAbleTimeSheetShowLink = (status) => {
  return status === 'completed' || status === 'approved' || status === 'rejected';
}

const isAbleTimeSheetReviewEditLink = (status) => {
  return status === 'pending';
}

const isAbleTimeSheetReviewShowLink = (status) => {
  return status === 'completed';
}

</script>

<template>

  <Head>
    <title>Hojas de tiempo</title>
  </Head>
  <main>
    <div class="flex-1 flex flex-col">
      <div class="flex-1">

        <ChandelierPage class="mt-8" v-if="props.timeSheetReviews.total > 0">
          <AreaTitle>Hojas de tiempo pendientes de aprobación</AreaTitle>
          <fwb-table class="shadow-none">
            <thead>
            <tr>
              <TableHeaderItem>Colaborador</TableHeaderItem>
              <TableHeaderItem>Mes</TableHeaderItem>
              <TableHeaderItem>Estado</TableHeaderItem>
              <TableHeaderItem>Acciones</TableHeaderItem>
            </tr>
            </thead>
            <tbody class="m-3 divide-y-2 divide-slate-100">
            <fwb-table-row v-for="timeSheetReview in timeSheetReviews.data" :key="timeSheetReview.id">
              <fwb-table-cell>{{ timeSheetReview.timeSheet.userName }}</fwb-table-cell>
              <fwb-table-cell>{{ timeSheetReview.timeSheet.title }}</fwb-table-cell>
              <fwb-table-cell>{{ timeSheetReview.timeSheet.statusName }}</fwb-table-cell>
              <td class="justify-center">
                <LinkToPrimary v-if="isAbleTimeSheetReviewShowLink(timeSheetReview.queue)" :href="goToTimeSheetReview(timeSheetReview.id)">
                  Ver detalle
                </LinkToPrimary>
                <LinkToPrimary v-if="isAbleTimeSheetReviewEditLink(timeSheetReview.queue)" :href="goToTimeSheetReviewEdit(timeSheetReview.id)">
                  Editar
                </LinkToPrimary>
              </td>
            </fwb-table-row>
            </tbody>
          </fwb-table>
          <fwb-pagination v-model="currentPageExpired" :total-items="timeSheets.total" :per-page="5"
                          show-icons :show-labels="false"/>
        </ChandelierPage>

        <ChandelierPage class="mt-8">
          <AreaTitle>Hojas de tiempos activas</AreaTitle>
          <fwb-table class="shadow-none">
            <thead>
            <tr>
              <TableHeaderItem>Mes</TableHeaderItem>
              <TableHeaderItem>Estado</TableHeaderItem>
              <TableHeaderItem></TableHeaderItem>
            </tr>
            </thead>
            <tbody class="m-3 divide-y-2 divide-slate-100">
            <fwb-table-row v-for="timeSheet in timeSheets.data" :key="timeSheet.id">
              <fwb-table-cell>{{ timeSheet.title }}</fwb-table-cell>
              <fwb-table-cell>{{ timeSheet.statusName }}</fwb-table-cell>
              <td class="text-right">
                <LinkToPrimary v-if="isAbleTimeSheetShowLink(timeSheet.status)" :href="goToDetail(timeSheet.id)">
                  Ver detalle
                </LinkToPrimary>
                <LinkToPrimary v-if="isAbleTimeSheetEditLink(timeSheet.status)" :href="goToEdit(timeSheet.id)">
                  Editar
                </LinkToPrimary>
              </td>
            </fwb-table-row>
            </tbody>
          </fwb-table>
          <fwb-pagination v-model="currentPageExpired" :total-items="timeSheets.total" :per-page="5"
                          show-icons :show-labels="false"/>
        </ChandelierPage>

      </div>
    </div>
  </main>
</template>
