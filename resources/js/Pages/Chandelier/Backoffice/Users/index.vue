<template>
  <Head>
    <title>Usuarios</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Usuarios</AreaTitle>

      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Correo electrónico</TableHeaderItem>
          <TableHeaderItem>Nombres</TableHeaderItem>
          <TableHeaderItem>Apellidos</TableHeaderItem>
          <TableHeaderItem>Rol</TableHeaderItem>
          <TableHeaderItem></TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="user in users.data" :key="user.id">
          <fwb-table-cell>{{ user.email }}</fwb-table-cell>
          <fwb-table-cell>{{ user.firstName }}</fwb-table-cell>
          <fwb-table-cell>{{ user.lastName }}</fwb-table-cell>
          <fwb-table-cell>{{ user.role ? user.role : '-sin asignar-' }}</fwb-table-cell>
          <fwb-table-cell>
            <span class="mr-5">{{ user.active == 'true' ? 'Deshabilitar' : 'Habilitar' }}</span>
            <fwb-toggle
              v-model="user.active"
              @change="toggleUser(user.id)"
            />
            <LinkToPrimary :href="goToEdit(user.id)">
              Editar
            </LinkToPrimary>
          </fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination
        class="mt-4"
        :model-value="currentPage"
        :total-items="users.meta.total"
        :per-page="users.meta.per_page"
        show-icons
        :show-labels="false"
        @update:model-value="handlePageChange($event)"
      />
    </ChandelierPage>
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
import LinkToPrimary from '@/Components/Chandelier/Common/LinkToPrimary.vue';

import {
  FwbA,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
  FwbPagination,
  FwbToggle
} from 'flowbite-vue';

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  users: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry()
const currentPage = ref(props.users.meta.current_page || 1);


const handlePageChange = (newPage) => {
  router.get(route('backoffice.users.index', { page: newPage, country: currentCountry.value }), {}, { preserveScroll: true });
};

const toggleUser = (id) => {
  axios.put(route('backoffice.users.toggle', { country: currentCountry.value, user: id }))
    .then((response) => {
      if(response.status === 200){

        const index = props.users.data.findIndex(user => user.id === response.data.id);
        if (index !== -1)
          props.users.data[index] = response.data;

      }
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}

const goToEdit = (id) => {
  return route('backoffice.users.edit', {user: id, country: currentCountry.value});
}
</script>
