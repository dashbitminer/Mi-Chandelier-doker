<script setup>
import { ref } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import { useCountry } from '@/Composables/useCountry';
import MainLayout from '@/Layouts/MainLayout.vue'; // Next, import the layout component

// Then, import the components that will be used in the template
import AreaTitle from '@/Components/Chandelier/Common/AreaTitle.vue';
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue';
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
  travelRequestsActive: {
    type: Object,
    required: true,
  },
  travelRequestsExpired: {
    type: Object,
    required: true,
  },
  travelRequestReviews: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry()

const currentPageReview = ref(props.travelRequestReviews.current_page || 1)
const currentPageActive = ref(props.travelRequestsActive.current_page || 1)
const currentPageExpired = ref(props.travelRequestsExpired.current_page || 1)

const handlePageChange = (type, page) => {
  const params = {
    country: currentCountry.value
  }

  switch(type) {
    case 'active':
      params.active_page = page
      break
    case 'expired':
      params.expired_page = page
      break
    case 'reviews':
      params.reviews_page = page
      break
  }

  router.get(route('travel-requests.index', params), {}, {
    preserveState: false,
    preserveScroll: true,
    replace: true
  })
}

function goToDetail(id) {
  return route('travel-requests.show', { travel_request: id, country: currentCountry.value });
}

function goToEdit(id) {
  return route('travel-requests.edit', {country: currentCountry.value, travel_request: id});
}

function goToTravelRequestReview(id) {
  return route('travel-request-reviews.show', {country: currentCountry.value, travel_request_review: id});
}
function goToTravelRequestReviewEdit(id) {
  return route('travel-request-reviews.edit', {country: currentCountry.value, travel_request_review: id});
}

function goToCreate() {
  return route('travel-requests.create', { country: currentCountry.value })
}

const isEditable = (travelRequest) => {
  return travelRequest.status === 'pending' || travelRequest.status === 'rechazada';
}

const download = async (travelRequest) => {
  travelRequest.isLoading = true;

  try {
    const response = await axios.get(route('travel-requests.download', {country: currentCountry.value, travel_request: travelRequest.id}), {
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

const isAbleTravelRequestEditLink = (status) => {
  return status === 'pending';
}

const isAbleTravelRequestShowLink = (status) => {
  return status === 'completed' || status === 'approved' || status === 'rejected';
}

const isAbleTravelRequestReviewEditLink = (status) => {
  return status === 'pending';
}

const isAbleTravelRequestReviewShowLink = (status) => {
  return status === 'completed';
}
</script>

<template>

  <Head>
    <title>Solicitudes de viaje</title>
  </Head>
  <main>
    <div class="flex-1 flex flex-col">
      <div class="flex-1">

        <ChandelierPage class="mt-8" v-if="props.travelRequestReviews?.total > 0">
          <AreaTitle>Solicitudes pendientes de aprobación</AreaTitle>
          <fwb-table class="shadow-none" striped>
            <thead>
            <tr>
              <TableHeaderItem>Proyecto</TableHeaderItem>
              <TableHeaderItem>Solicitante</TableHeaderItem>
              <TableHeaderItem>Fecha inicio</TableHeaderItem>
              <TableHeaderItem>Fecha fin</TableHeaderItem>
              <TableHeaderItem>Total</TableHeaderItem>
              <TableHeaderItem>Estado</TableHeaderItem>
              <TableHeaderItem></TableHeaderItem>
            </tr>
            </thead>
            <tbody class="m-3 divide-y-2 divide-slate-100">
            <fwb-table-row v-for="travelRequestReview in props.travelRequestReviews.data" :key="travelRequestReview.id">
              <fwb-table-cell>{{ travelRequestReview.travelRequest.projectName }}</fwb-table-cell>
              <fwb-table-cell>{{ travelRequestReview.travelRequest.userName }}</fwb-table-cell>
              <fwb-table-cell>{{ travelRequestReview.travelRequest.departureDateFormatted }}</fwb-table-cell>
              <fwb-table-cell>{{ travelRequestReview.travelRequest.arrivalDateFormatted }}</fwb-table-cell>
              <fwb-table-cell>{{ travelRequestReview.travelRequest.totalFormatted }}</fwb-table-cell>
              <fwb-table-cell>{{ travelRequestReview.travelRequest.statusName }}</fwb-table-cell>
              <td class="text-right">
                <LinkToPrimary v-if="isAbleTravelRequestReviewShowLink(travelRequestReview.queue)" :href="goToTravelRequestReview(travelRequestReview.id)">
                  Ver detalle
                </LinkToPrimary>
                <LinkToPrimary v-if="isAbleTravelRequestReviewEditLink(travelRequestReview.queue)" :href="goToTravelRequestReviewEdit(travelRequestReview.id)">
                  Revisar
                </LinkToPrimary>
              </td>
            </fwb-table-row>
            </tbody>
          </fwb-table>
          <fwb-pagination
            v-model="currentPageReview"
            :total-items="props.travelRequestReviews.total"
            :per-page="props.travelRequestReviews.per_page"
            show-icons
            :show-labels="false"
            @update:model-value="handlePageChange('reviews', $event)"
          />
        </ChandelierPage>

        <ChandelierPage class="mt-8">
          <div class="flex justify-between items-center">
            <AreaTitle>Solicitudes personales activas</AreaTitle>
            <div class="-mt-4 ms-6">

              <LinkToPrimary :href="goToCreate()"> <!-- Use native intertia routing -->
                  <span class="mdi mdi-plus"></span>
                  Crear Solicitud de Viaje
              </LinkToPrimary>
            </div>
          </div>
          <fwb-table class="mt-8 shadow-none" striped>
            <fwb-table-head>
              <TableHeaderItem>Fecha inicio</TableHeaderItem>
              <TableHeaderItem>Fecha fin</TableHeaderItem>
              <TableHeaderItem>Total</TableHeaderItem>
              <TableHeaderItem>Asignado a</TableHeaderItem>
              <TableHeaderItem>Estado</TableHeaderItem>
              <!-- <TableHeaderItem>
                Acciones
              </TableHeaderItem> -->
            </fwb-table-head>
            <fwb-table-body>
              <fwb-table-row v-for="travelRequest in props.travelRequestsActive.data" :key="travelRequest.id">
                <fwb-table-cell>{{ travelRequest.departureDateFormatted }}</fwb-table-cell>
                <fwb-table-cell>{{ travelRequest.arrivalDateFormatted }}</fwb-table-cell>
                <fwb-table-cell>${{ travelRequest.totalFormatted }}</fwb-table-cell>
                <fwb-table-cell>{{ travelRequest.reviewerName }}</fwb-table-cell>
                <fwb-table-cell>{{ travelRequest.statusName }}</fwb-table-cell>
                <td class="text-right">
                  <LinkToPrimary v-if="isAbleTravelRequestShowLink(travelRequest.status)" :href="goToDetail(travelRequest.id)">
                    Ver detalle
                  </LinkToPrimary>
                  <LinkToPrimary v-if="isAbleTravelRequestEditLink(travelRequest.status)" :href="goToEdit(travelRequest.id)">
                    Editar
                  </LinkToPrimary>
                  <LinkToPrimary
                    v-if="travelRequest.status == 'approved'"
                    :disabled="travelRequest.isLoading"
                    @click="download(travelRequest)">
                    <i class="mdi mdi-publish"></i>
                    {{ travelRequest.isLoading ? 'Descargando...' : 'Descargar PDF' }}
                  </LinkToPrimary>
                </td>
              </fwb-table-row>
            </fwb-table-body>
          </fwb-table>
          <br/>
          <fwb-pagination
            :model-value="currentPageActive"
            :total-items="props.travelRequestsActive.total"
            :per-page="props.travelRequestsActive.per_page"
            show-icons
            :show-labels="false"
            @update:model-value="handlePageChange('active', $event)"
          />
        </ChandelierPage>

        <ChandelierPage class="mt-8" v-if="props.travelRequestsExpired && props.travelRequestsExpired?.total > 0">
          <AreaTitle>Solicitudes personales pasadas</AreaTitle>
          <fwb-table class="shadow-none" striped>
            <thead>
            <tr>
              <TableHeaderItem>Fecha inicio</TableHeaderItem>
              <TableHeaderItem>Fecha fin</TableHeaderItem>
              <TableHeaderItem>Total</TableHeaderItem>
              <TableHeaderItem>Estado</TableHeaderItem>
            </tr>
            </thead>
            <tbody class="m-3 divide-y-2 divide-slate-100">
            <fwb-table-row v-for="travelRequest in props.travelRequestsExpired.data" :key="travelRequest.id">
              <fwb-table-cell>{{ travelRequest.departureDateFormatted }}</fwb-table-cell>
              <fwb-table-cell>{{ travelRequest.arrivalDateFormatted }}</fwb-table-cell>
              <fwb-table-cell>{{ travelRequest.totalFormatted }}</fwb-table-cell>
              <fwb-table-cell>{{ travelRequest.statusName }}</fwb-table-cell>
            </fwb-table-row>
            </tbody>
          </fwb-table>
          <fwb-pagination
            :model-value="currentPageExpired"
            :total-items="props.travelRequestsExpired.total"
            :per-page="props.travelRequestsExpired.per_page"
            show-icons
            :show-labels="false"
            @update:model-value="handlePageChange('expired', $event)"
          />
        </ChandelierPage>

      </div>
    </div>
  </main>
</template>
