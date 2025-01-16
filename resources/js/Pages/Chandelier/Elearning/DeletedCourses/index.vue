<template>
  <Head>
    <title>Cursos eliminados</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Cursos eliminados</AreaTitle>

      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Curso</TableHeaderItem>
          <TableHeaderItem></TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="course in courses.data" :key="course.id">
          <fwb-table-cell>{{ course.name }}</fwb-table-cell>
          <fwb-table-cell>
            <ButtonPrimary @click="restore(course.id)">
              Restaurar
            </ButtonPrimary>
          </fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination
        class="mt-4"
        :model-value="currentPage"
        :total-items="courses.total"
        :per-page="courses.per_page"
        show-icons
        :show-labels="false"
        @update:model-value="handlePageChange($event)"
      />
    </ChandelierPage>
  </main>
</template>

<script setup>
import { ref } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import { useCountry } from '@/Composables/useCountry';

import LinkToPrimary from "@/Components/Chandelier/Common/LinkToPrimary.vue";
import ButtonPrimary from '@/Components/Chandelier/Common/ButtonPrimary.vue';
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

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  courses: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry();
const currentPage = ref(props.courses.current_page || 1);

const handlePageChange = (newPage) => {
  router.get(route('elearning.deleted-courses.index', { page: newPage, country: currentCountry.value }), {}, { preserveScroll: true });
};


const restore = (id) => {
  router.post(route('elearning.deleted-courses.restore', { course: id, country: currentCountry.value }), {
  }, {
    preserveState: true,
  });
}
</script>
