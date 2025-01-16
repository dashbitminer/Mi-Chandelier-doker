<template>
  <Head title="Editar solicitud de viaje" />

  <main>
    <ChandelierPage>
      <AreaTitle>Tipo de pago</AreaTitle>

      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem></TableHeaderItem>
          <TableHeaderItem></TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row>
          <fwb-table-cell>{{paymentPeriod.periodName}}</fwb-table-cell>
          <fwb-table-cell>
            <LinkToPrimary :href="goToNew()">Editar</LinkToPrimary>
          </fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>
    </ChandelierPage>

  </main>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

import {
  FwbA,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
  FwbPagination,
} from 'flowbite-vue';

import Layout from '@/Layouts/MainLayout.vue';
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue';
import AreaTitle from '@/Components/Chandelier/Common/AreaTitle.vue';
import LinkToPrimary from "@/Components/Chandelier/Common/LinkToPrimary.vue";
import TableHeaderItem from "@/Components/Chandelier/Common/TableHeaderItem.vue";
import { useCountry } from '@/Composables/useCountry';

defineOptions({
  layout: Layout,
});

const props = defineProps({
  paymentPeriod: Object,
});

const { currentCountry } = useCountry()

function goToNew() {
  return route('accounting.payment-periods.create', { country: currentCountry.value });
}
</script>
