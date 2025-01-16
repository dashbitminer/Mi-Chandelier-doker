<template>
  <Head>
    <title>Hojas de tiempo</title>
  </Head>

  <main>
    <AreaTitle>Reporte hojas de tiempo</AreaTitle>
    {{timeSheetTemplate.title}}

    <ChandelierPage class="mt-8">
      <AreaTitle>Usuarios pendiente de crear sus hojas de tiempo</AreaTitle>
      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Usuario</TableHeaderItem>
          <TableHeaderItem>Supervisor</TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="timeSheetProject in timeSheetProjectsIncompleted.data" :key="timeSheetProject.id">
          <fwb-table-cell>
            {{ timeSheetProject.userName }}
            <br />
            {{ timeSheetProject.userEmail }}
          </fwb-table-cell>
          <fwb-table-cell>
            {{ timeSheetProject.reviewerName }}
            <br />
            {{ timeSheetProject.reviewerEmail }}
          </fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination v-model="currentPageExpired" :total-items="timeSheetProjectsIncompleted.total" :per-page="5"
                      show-icons :show-labels="false"/>
    </ChandelierPage>

    <ChandelierPage class="mt-8">
      <AreaTitle>Usuarios pendiente de revision de sus hojas de tiempo</AreaTitle>
      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Usuario</TableHeaderItem>
          <TableHeaderItem>Supervisor</TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="timeSheetProject in timeSheetProjectsCompleted.data" :key="timeSheetProject.id">
          <fwb-table-cell>
            {{ timeSheetProject.userName }}
            <br />
            {{ timeSheetProject.userEmail }}
          </fwb-table-cell>
          <fwb-table-cell>
            {{ timeSheetProject.reviewerName }}
            <br />
            {{ timeSheetProject.reviewerEmail }}
          </fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination v-model="currentPageExpired" :total-items="timeSheetProjectsCompleted.total" :per-page="5"
                      show-icons :show-labels="false"/>
    </ChandelierPage>

    <ChandelierPage class="mt-8">
      <AreaTitle>Usuarios con proceso completo de revision de sus hojas de tiempo</AreaTitle>
      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Usuario</TableHeaderItem>
          <TableHeaderItem>Supervisor</TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="timeSheetProject in timeSheetProjectsApproved.data" :key="project.id">
          <fwb-table-cell>
            {{ timeSheetProject.userName }}
            <br />
            {{ timeSheetProject.userEmail }}
          </fwb-table-cell>
          <fwb-table-cell>
            {{ timeSheetProject.reviewerName }}
            <br />
            {{ timeSheetProject.reviewerEmail }}
          </fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination v-model="currentPageExpired" :total-items="timeSheetProjectsApproved.total" :per-page="5"
                      show-icons :show-labels="false"/>
    </ChandelierPage>

    <AreaTitle>Desea descargar un archivo con:</AreaTitle>

    <ul class="list-none ml-5">
      <li class="mt-5">
        <label class="flex items-center">
          <Checkbox v-model:checked="form.incompleted" name="incompleted"/>
          <span class="ms-2 text-sm text-gray-600">Usuarios pendiente de crear sus hojas de tiempo</span>
        </label>
      </li>
      <li class="mt-5">
        <label class="flex items-center">
          <Checkbox v-model:checked="form.completed" name="completed"/>
          <span class="ms-2 text-sm text-gray-600">Usuarios pendiente de revision de sus hojas de tiempo</span>
        </label>
      </li>
      <li class="mt-5">
        <label class="flex items-center">
          <Checkbox v-model:checked="form.approved" name="approved"/>
          <span class="ms-2 text-sm text-gray-600">Usuarios con proceso completo de revision de sus hojas de tiempo</span>
        </label>
      </li>
    </ul>

    <div class="flex justify-end">
      <button
        type="button"
        @click="download()"
        class="rounded-md p-2 m-2 ms-0 mt-7 transition transition-all ease-in-out bg-cyan-600 w-fit shadow-sm active:bg-cyan-800 active:shadow-lg text-white"
      >
        <i class="mdi mdi-download"></i>
        Descargar
      </button>
    </div>
  </main>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import axios from 'axios';
import { useCountry } from '@/Composables/useCountry';

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
import Checkbox from "@/Components/Checkbox.vue";

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  project: {
    type: Object,
    required: true
  },
  timeSheetTemplate: {
    type: Object,
    required: true,
  },
  timeSheetProjectsIncompleted: {
    type: Object,
    required: true,
  },
  timeSheetProjectsCompleted: {
    type: Object,
    required: true,
  },
  timeSheetProjectsApproved: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry();

const form = reactive({
  incompleted: false,
  completed: false,
  approved: false
});

const currentPageActive = ref(1);
const currentPageExpired = ref(1);

const download = async () => {
  const statuses = []
  if(form.incompleted) statuses.push('incompleted')
  if(form.completed) statuses.push('completed')
  if(form.approved) statuses.push('approved')

  if(statuses.length == 0){
    alert('Debe seleccionar por lo menos una opción de descarga')
    return;
  }

  try {
    const response = await axios.get(route('accounting.time-sheet-templates.time-sheet-statuses.download', {
      time_sheet_template: props.timeSheetTemplate.id, project: props.project.id, country: currentCountry.value
    }), {
      params: {
        data: {
          statuses: statuses.join(',')
        }
      },
      responseType: 'blob'
    });

    const contentDisposition = response.headers['content-disposition'];
    let filename = 'informe-de-usuarios-para-hojas-de-tiempo.pdf';
    if (contentDisposition) {
      const matches = contentDisposition.match(/filename="?(.+)"?/);
      if (matches[1]) {
        filename = matches[1];
      }
    }

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', filename);

    document.body.appendChild(link);
    link.click();
  } catch (error) {
    console.error('Error al generar el PDF:', error);
  }

  /*router.get(route('accounting.time-sheet-templates.time-sheet-statuses.download', {time_sheet_template: props.timeSheetTemplate.id, project: props.project.id, country: currentCountry.value}), {
    data: {
      statuses: statuses.join(',')
    },
  }, {
    preserveState: true,
  });*/
}
</script>
