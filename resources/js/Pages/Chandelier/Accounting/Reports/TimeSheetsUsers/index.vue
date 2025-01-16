<template>
  <Head>
    <title>Hojas de tiempo</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Impresión hojas de tiempo por empleados</AreaTitle>

      <div class="flex justify-between items-center">
        {{timeSheetTemplate.title}}
        <button
          @click="toggleAllUsers"
          class="rounded-md p-2 m-2 ms-0 transition transition-all ease-in-out bg-cyan-600 w-fit shadow-sm active:bg-cyan-800 active:shadow-lg text-white"
        >
          {{ isSelectedAllUsers() ? 'Deseleccionar todo' : 'Seleccionar todo' }}
        </button>
      </div>

      <fwb-table class="shadow-none">
        <thead>
          <tr>
            <TableHeaderItem>Empleado</TableHeaderItem>
          </tr>
        </thead>
        <tbody class="">
        <fwb-table-row v-for="timeSheet in timeSheets.data" :key="timeSheet.id">
          <td class="px-6 py-4 first:font-medium first:text-gray-900 first:dark:text-white first:whitespace-nowrap">
            <input
              type="checkbox"
              class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
              @click="toogleUser(timeSheet)"
              :checked="isSelectedUser(timeSheet)"
            >
            <span class="ms-2 text-sm text-gray-600">{{ timeSheet.userName }}</span>
          </td>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination
        :model-value="currentPage"
        :total-items="timeSheets.meta.total"
        :per-page="timeSheets.meta.per_page"
        show-icons
        :show-labels="false"
        @update:model-value="handlePageChange($event)"
      />

      <div class="mt-8" v-show="form.selectedUsers.length > 0">
        <p class="mb-3 text-base text-gray-900">Empelados seleccionados:</p>
        <ul>
          <li v-for="user in form.selectedUsers" :key="user.id">
            <span class="ms-2 text-sm text-gray-600">{{user.name}}</span>
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
  timeSheets: {
    type: Object,
    required: true,
  },
});

const { currentCountry } = useCountry();

const form = reactive({
  selectedUsers: []
})

const isLoading = ref(false);
const currentPage = ref(props.timeSheets.meta.current_page || 1);

const handlePageChange = (newPage) => {
  router.get(route('accounting.time-sheet-templates.time-sheets.index', { page: newPage, time_sheet_template: props.timeSheetTemplate.id, country: currentCountry.value }), {}, { preserveScroll: true });
};

const toogleUser = (timeSheet) => {
  const selectedUsers = form.selectedUsers.find(item => item.id === timeSheet.user_id)


  if(selectedUsers){
    form.selectedUsers = form.selectedUsers.filter(item => item.id !== timeSheet.user_id)
  } else{
    form.selectedUsers.push({ id: timeSheet.user_id, name: timeSheet.userName })
  }
}

const isSelectedUser = (timeSheet) => {
  const indexToUpdate = form.selectedUsers.findIndex(item => item.id === timeSheet.user_id);
  
  return indexToUpdate !== -1 ? true : false;
}

const toggleAllUsers = () => {
  if(!isSelectedAllUsers()){
    props.timeSheets.data.forEach((timeSheet) => {
      if(!isSelectedUser(timeSheet))
        toogleUser(timeSheet);
    });
  } else{
    form.selectedUsers = [];
  }
};

const isSelectedAllUsers = () => {
  const userIDs = props.timeSheets.data.map(item => item.user_id);
  
  return form.selectedUsers.filter(item => userIDs.includes(item.id)).length == userIDs.length;
}

const download = async () => {
  const users = form.selectedUsers.map(user => user.id);
  isLoading.value = true;

  try {
    const response = await axios.get(route('accounting.time-sheet-templates.time-sheets-users.download', {time_sheet_template: props.timeSheetTemplate.id, country: currentCountry.value}), {
      params: {
        data: {
          users: users.join(',')
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
