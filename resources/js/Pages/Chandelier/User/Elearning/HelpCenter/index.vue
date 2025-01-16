<template>
  <Head>
    <title>Centro de ayuda</title>
  </Head>

  <ChandelierPage>
    <AreaTitle>
      Centro de ayuda - temas disponibles
    </AreaTitle>

    <p class="mb-6">
      Bienvenido {{ user.name }}, aquí te mostramos una lista de los temas que actualmente están disponibles en la plataforma.
    </p>

    <section v-if="courses && courses.data && courses.data.length > 0">
      <div class="grid grid-cols-3 gap-4">
        <div v-for="course in courses.data" :key="course.id">
          <Courses :course="course" @goToTopics="goToTopics" />
        </div>
      </div>
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
      No hay cursos disponibles actualmente
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
import Courses from '../partials/courses.vue';

import {
  FwbAlert,
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
  router.get(route('user.elearning.help-center.index', { page: newPage, country: currentCountry.value }), {}, { preserveScroll: true });
};

const goToTopics = (id) => {
  router.get(route('user.elearning.subjects.index', { help_center: id, country: currentCountry.value }));
}
</script>
