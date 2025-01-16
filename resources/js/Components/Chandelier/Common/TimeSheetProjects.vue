<template>
  <fwb-table class="shadow-none">
    <thead>
    <tr>
      <TableHeaderItem>Proyecto</TableHeaderItem>
      <TableHeaderItem>Horas pendientes para asignar</TableHeaderItem>
      <TableHeaderItem>Horas asignadas</TableHeaderItem>
    </tr>
    </thead>
    <tbody class="m-3 divide-y-2 divide-slate-100">
    <fwb-table-row v-for="project in projects" :key="project.id">
      <fwb-table-cell>{{ project.name }}</fwb-table-cell>
      <fwb-table-cell>{{ remainingProjectHours(project) }}</fwb-table-cell>
      <fwb-table-cell>{{ totalProjectHours(project) }}</fwb-table-cell>
    </fwb-table-row>
    </tbody>
  </fwb-table>
</template>
<script setup>
import {
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
} from 'flowbite-vue';

import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import TableHeaderItem from '@/Components/Chandelier/Common/TableHeaderItem.vue';

const props = defineProps({
  projects: {
    type: Object,
    required: true,
  }
})

const remainingProjectHours = (project) => {
  const hours = parseFloat(project.budgetHours) - (parseFloat(project.accumulatedHours) + parseFloat(project.hours));
  
  return hours > 0 ? hours : 0;
}

const totalProjectHours = (project) => {
  let label = parseFloat(project.accumulatedHours) + parseFloat(project.hours)
  
  if(isExceedHours(project))
    label += ' / ' + project.budgetHours
  
  return label;
}

const isExceedHours = (project) => {
  return (parseFloat(project.accumulatedHours) + parseFloat(project.hours)) > parseFloat(project.budgetHours);
}
</script>
