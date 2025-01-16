<template>
  <Head>
    <title>Cursos completados</title>
  </Head>

  <ChandelierPage>
    <AreaTitle>
      Formaciones - Cursos completados
    </AreaTitle>

    <section v-if="courses && courses.data && courses.data.length > 0">
      <fwb-table class="shadow-none">
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="course in courses.data" :key="course.id">
          <fwb-table-cell>{{ course.name }}</fwb-table-cell>
          <fwb-table-cell>
            <button
              class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
              @click="goToTopics(course.id)">
              Resumen
            </button>
          </fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination
        class="mt-4"
        :model-value="currentPage"
        :total-items="courses.meta.total"
        :per-page="courses.meta.per_page"
        show-icons
        :show-labels="false"
        @update:model-value="handlePageChange($event)"
      />
    </section>

    <fwb-alert v-else icon type="warning" class="mt-7">
      No hay cursos completados actualmente
    </fwb-alert>
  </ChandelierPage>
</template>

<script setup>
import { ref } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3';
import { useCountry } from '@/Composables/useCountry';

import MainLayout from "@/Layouts/MainLayout.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";

import {
  FwbAlert,
  FwbA,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
  FwbPagination
} from 'flowbite-vue';

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  courses: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry();
const currentPage = ref(props.courses.meta.current_page || 1);

const handlePageChange = (newPage) => {
  router.get(route('user.elearning.courses.completed', { page: newPage, country: currentCountry.value }), {}, { preserveScroll: true });
};

const goToTopics = (id) => {
  router.get(route('user.elearning.topics.index', { course: id, country: currentCountry.value }));
  // return route('user.elearning.topics.index', {id});
}
</script>

<style scoped>
  .line-clamp {
    display: -webkit-box;
    line-clamp: 8;
    -webkit-line-clamp: 8;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
</style>
