<template>
  <Head>
    <title>Categorias</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Categorias</AreaTitle>

      <LinkToPrimary :href="goToNew()">
        Crear categoria
      </LinkToPrimary>

      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Categoria</TableHeaderItem>
          <TableHeaderItem></TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="category in categories.data" :key="category.id">
          <fwb-table-cell>{{ category.name }}</fwb-table-cell>
          <fwb-table-cell>
            <span class="mr-5">{{ category.active == 'true' ? 'Deshabilitar' : 'Habilitar' }}</span>
            <fwb-toggle
              v-model="category.active"
              @change="toggleCategory(category.id)"
            />
            <LinkToPrimary :href="goToEdit(category.id)">
              Editar
            </LinkToPrimary>
            <ButtonPrimary @click="submitDeleteCategory(category.id)">
              Eliminar
            </ButtonPrimary>
          </fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination
        class="mt-4"
        :model-value="currentPage"
        :total-items="categories.meta.total"
        :per-page="categories.meta.per_page"
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
import ButtonPrimary from '@/Components/Chandelier/Common/ButtonPrimary.vue';

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
  categories: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry()
const currentPage = ref(props.categories.meta.current_page || 1);

const goToNew = () => {
  return route('elearning.categories.create', { country: currentCountry.value });
}

const goToEdit = (id) => {
  return route('elearning.categories.edit', {category: id, country: currentCountry.value});
}

const handlePageChange = (newPage) => {
  router.get(route('elearning.categories.index', { page: newPage, country: currentCountry.value }), {}, { preserveScroll: true });
};

const submitDeleteCategory = (id) => {
  router.delete(route('elearning.categories.destroy', { category: id, country: currentCountry.value }))
}

const toggleCategory = (id) => {
  axios.put(route('elearning.categories.toggle', { country: currentCountry.value, category: id }))
    .then((response) => {
      if(response.status === 200){

        const index = props.categories.data.findIndex(category => category.id === response.data.id);
        if (index !== -1)
          props.categories.data[index] = response.data;

      }
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}
</script>
