<template>
  <Head>
    <title>Hojas de tiempo</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Impresión hojas de tiempo por proyectos</AreaTitle>

      <div class="flex justify-between items-center">
        {{timeSheetTemplate.title}}
        <button
          @click="toggleAllProjects"
          class="rounded-md p-2 m-2 ms-0 transition transition-all ease-in-out bg-cyan-600 w-fit shadow-sm active:bg-cyan-800 active:shadow-lg text-white"
        >
          {{ isSelectedAllProjects() ? 'Deseleccionar todo' : 'Seleccionar todo' }}
        </button>
      </div>

      <fwb-table class="shadow-none">
        <thead>
          <tr>
            <TableHeaderItem>Proyecto</TableHeaderItem>
          </tr>
        </thead>
        <tbody class="">
        <fwb-table-row v-for="countryProject in countryProjects.data" :key="countryProject.id">
          <td class="px-6 py-4 first:font-medium first:text-gray-900 first:dark:text-white first:whitespace-nowrap">
            <input
              type="checkbox"
              class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
              @click="toogleProject(countryProject)"
              :checked="isSelectedProject(countryProject)"
            >
            <span class="ms-2 text-sm text-gray-600">{{ countryProject.name }}</span>
          </td>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination
        :model-value="currentPage"
        :total-items="countryProjects.meta.total"
        :per-page="countryProjects.meta.per_page"
        show-icons
        :show-labels="false"
        @update:model-value="handlePageChange($event)"
      />

      <div class="mt-8" v-show="form.selectedProjects.length > 0">
        <p class="mb-3 text-base text-gray-900">Proyectos seleccionados:</p>
        <ul>
          <li v-for="project in form.selectedProjects" :key="project.id">
            <span class="ms-2 text-sm text-gray-600">{{project.name}}</span>
          </li>
        </ul>

        <div class="flex justify-end">
          <button
            type="button"
            @click="download()"
            class="rounded-md p-2 m-2 ms-0 transition transition-all ease-in-out bg-cyan-600 w-fit shadow-sm active:bg-cyan-800 active:shadow-lg text-white"
          >
            <i class="mdi mdi-publish"></i>
            {{ isLoading ? 'Descargando...' : 'Descargar PDF' }}
          </button>
        </div>
      </div>
    </ChandelierPage>
  </main>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
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
} from 'flowbite-vue';
import Checkbox from "@/Components/Checkbox.vue";
defineOptions({
  layout: MainLayout,
});
const props = defineProps({
  timeSheetTemplate: {
    type: Object,
    required: true
  },
  countryProjects: {
    type: Object,
    required: true,
  },
});

const { currentCountry } = useCountry();

const form = reactive({
  selectedProjects: []
})

const isLoading = ref(false);
const currentPage = ref(props.countryProjects.meta.current_page || 1);

const handlePageChange = (newPage) => {
  router.get(route('accounting.time-sheet-templates.time-sheets.index', { page: newPage, time_sheet_template: props.timeSheetTemplate.id, country: currentCountry.value }), {}, { preserveScroll: true });
};

const toogleProject = (countryProject) => {
  const selectedProjects = form.selectedProjects.find(item => item.id === countryProject.project_id)


  if(selectedProjects){
    form.selectedProjects = form.selectedProjects.filter(item => item.id !== countryProject.project_id)
  } else{
    form.selectedProjects.push({ id: countryProject.project_id, name: countryProject.name })
  }
}

const isSelectedProject = (countryProject) => {
  const indexToUpdate = form.selectedProjects.findIndex(item => item.id === countryProject.project_id);
  
  return indexToUpdate !== -1 ? true : false;
}

const toggleAllProjects = () => {
  if(!isSelectedAllProjects()){
    props.countryProjects.data.forEach((countryProject) => {
      if(!isSelectedProject(countryProject))
        toogleProject(countryProject);
    });
  } else{
    form.selectedProjects = [];
  }
};

const isSelectedAllProjects = () => {
  const projectIDs = props.countryProjects.data.map(item => item.id);
  
  return form.selectedProjects.filter(item => projectIDs.includes(item.id)).length == projectIDs.length;
}

const download = async () => {
  const projects = form.selectedProjects.map(project => project.id);
  isLoading.value = true;

  try {
    const response = await axios.get(route('accounting.time-sheet-templates.time-sheets.download', {time_sheet_template: props.timeSheetTemplate.id, country: currentCountry.value}), {
      params: {
        data: {
          projects: projects.join(',')
        }
      },
      responseType: 'blob'
    });
    const contentDisposition = response.headers['content-disposition'];
    let filename = 'hojas-de-tiempo.pdf';
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
  } finally {
    isLoading.value = false;
  }
}
</script>
