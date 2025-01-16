<template>
  <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-8">
    <div class="rounded-lg bg-gray-100 p-4 text-zinc-600">
      <article class="mb-5">
        <p class="font-semibold mr-2 text-sm">Proyecto:</p>
        <span class="text-slate-600 font-medium break-words"> {{ travelRequest.projectName }} </span>
      </article>

      <article class="mb-5">
        <p class="font-semibold mr-2 text-sm">Solicitante:</p>
        <span class="text-slate-600 font-medium break-words"> {{ travelRequest.userName }} </span>
      </article>

      <article class="mb-5">
        <p class="font-semibold mr-2 text-sm">Fecha inicio del viaje:</p>
        <span class="text-slate-600 font-medium break-words"> {{ travelRequest.departureDateFormatted }} </span>
      </article>

      <article class="mb-5">
        <p class="font-semibold mr-2 text-sm">Fecha fin del viaje:</p>
        <span class="text-slate-600 font-medium break-words"> {{ travelRequest.arrivalDateFormatted }} </span>
      </article>

      <article class="mb-5">
        <p class="font-semibold mr-2 text-sm">Tiempo de viaje:</p>
        <span class="text-slate-600 font-medium break-words">{{ travelRequest.travelDays }} días</span>
      </article>

      <article class="mb-5">
        <p class="font-semibold mr-2 text-sm">Descripción</p>
        <span class="text-slate-600 font-medium break-words"> {{ travelRequest.description }} </span>
      </article>

      <article class="mb-5">
        <p class="font-semibold mr-2 text-sm">Estado</p>
        <span class="text-slate-600 font-medium break-words"> {{ travelRequest.statusName }} </span>
      </article>
    </div>
    <div class="rounded-lg lg:col-span-2">
      <AreaTitle class="ms-6 mb-8">Gastos del viaje</AreaTitle>

      <fwb-table class="m-2 mt-0 table-auto shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Tipo</TableHeaderItem>
          <TableHeaderItem>Monto</TableHeaderItem>
          <TableHeaderItem>Comentario</TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <tr v-for="expense in travelRequest.expenses" :key="expense.id" class="border-b border-gray-200 hover:bg-gray-100">
          <TableDataItem :value=expense.expenseKindName></TableDataItem>
          <TableDataItem :value=expense.amountFormatted></TableDataItem>
          <TableDataItem :value=expense.comment></TableDataItem>
        </tr>
        <tr>
          <table-data-item class="font-semibold text-lg" value="Total"></table-data-item>
          <table-data-item value=""></table-data-item>
          <table-data-item class="font-semibold text-lg" :value="'$' + travelRequest.totalFormatted"></table-data-item>
        </tr>
        </tbody>
      </fwb-table>
    </div>
  </div>
</template>
<script setup>
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import TableDataItem from "@/Components/Chandelier/Common/TableDataItem.vue";
import TableHeaderItem from "@/Components/Chandelier/Common/TableHeaderItem.vue";
import { FwbTable } from 'flowbite-vue';

const props = defineProps({
  travelRequest: {
    type: Object,
    required: true,
  }
})
</script>
