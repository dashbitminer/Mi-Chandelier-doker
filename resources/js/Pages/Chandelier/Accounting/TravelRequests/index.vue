<template>

  <Head>
    <title>Solicitudes de viaje</title>
  </Head>
  <main>
    <div class="flex-1 flex flex-col">
      <div class="flex-1">

        <ChandelierPage class="mt-8">
          <AreaTitle>Solicitudes de viaje</AreaTitle>

          <fwb-table class="mt-8 shadow-none" striped>
            <fwb-table-head>
              <TableHeaderItem>Proyecto</TableHeaderItem>
              <TableHeaderItem>Solicitante</TableHeaderItem>
              <TableHeaderItem>Fecha inicio</TableHeaderItem>
              <TableHeaderItem>Fecha fin</TableHeaderItem>
              <TableHeaderItem>Total</TableHeaderItem>
              <TableHeaderItem>Estado</TableHeaderItem>
              <!-- <TableHeaderItem>
                Acciones
              </TableHeaderItem> -->
            </fwb-table-head>
            <fwb-table-body>
              <fwb-table-row v-for="travelRequest in props.travelRequests.data" :key="travelRequest.id">
                <fwb-table-cell>{{ travelRequest.projectName }}</fwb-table-cell>
                <fwb-table-cell>{{ travelRequest.userName }}</fwb-table-cell>
                <fwb-table-cell>{{ travelRequest.departureDateFormatted }}</fwb-table-cell>
                <fwb-table-cell>{{ travelRequest.arrivalDateFormatted }}</fwb-table-cell>
                <fwb-table-cell>{{ travelRequest.totalFormatted }}</fwb-table-cell>
                <fwb-table-cell>{{ travelRequest.statusName }}</fwb-table-cell>
                <td class="text-right">
                  <button
                    :disabled="travelRequest.isLoading"
                    type="button"
                    @click="download(travelRequest)"
                    class="rounded-md p-2 m-2 ms-0 transition transition-all ease-in-out bg-cyan-600 w-fit shadow-sm active:bg-cyan-800 active:shadow-lg text-white"
                  >
                    <i class="mdi mdi-publish"></i>
                    {{ travelRequest.isLoading ? 'Descargando...' : 'Descargar PDF' }}
                  </button>
                </td>
              </fwb-table-row>
            </fwb-table-body>
          </fwb-table>
          <br/>
          <fwb-pagination v-model="currentPageActive" :total-items="props.travelRequests.total" :per-page="5"
                          show-icons :show-labels="false"/>
        </ChandelierPage>

      </div>
    </div>
  </main>
</template>

<script setup>
import { ref } from 'vue';
import { Link, Head } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import MainLayout from '@/Layouts/MainLayout.vue'; // Next, import the layout component
import { useCountry } from '@/Composables/useCountry';

// Then, import the components that will be used in the template
import AreaTitle from '@/Components/Chandelier/Common/AreaTitle.vue';
import PositiveButton from '@/Components/Chandelier/Common/ButtonPositive.vue';
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue';
import TableHeaderItem from '@/Components/Chandelier/Common/TableHeaderItem.vue';
import ButtonPositiveSmall from '@/Components/Chandelier/Common/ButtonPositiveSmall.vue';

import {
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
  travelRequests: {
    type: Object,
    required: true,
  },
});

const { currentCountry } = useCountry()

const currentPageActive = ref(1)
const currentPageExpired = ref(1)

const download = async (travelRequest) => {
  travelRequest.isLoading = true;

  try {
    const response = await axios.get(route('accounting.travel-requests.download', {travel_request: travelRequest.id, country: currentCountry.value}), {
      responseType: 'blob'
    });
    const contentDisposition = response.headers['content-disposition'];
    let filename = 'solicitu-de-viaje.pdf';
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
    travelRequest.isLoading = false;
  }
}
</script>
