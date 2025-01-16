<template>
  <Head>
    <title>Clases</title>
  </Head>

  <ChandelierPage>
    <AreaTitle>
      Formaciones - {{ course.name }}
    </AreaTitle>

    <p class="mb-7">{{ course.description }}</p>

    <section v-if="topics && topics.data && topics.data.length > 0">
      <fwb-accordion class="mt-7">
        <fwb-accordion-panel v-for="topic in topics.data" :key="topic.id">
          <fwb-accordion-header>
            <div class="flex justify-between">
              <span>
                {{ topic.name }}
              </span>
              <span
                v-if="userCourse.status ==='pending'"
                class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg mr-5 w-32"
                :class="topic.status === 'pending' ? 'bg-red-700' : 'bg-green-500'"
              >
              {{ topic.status === 'pending' ? 'Pendiente' : 'Completado' }}
              </span>
            </div>
          </fwb-accordion-header>
          <fwb-accordion-content>
            <div>
              <p class="mb-2 text-gray-500 dark:text-gray-400">
                {{ topic.description }}
              </p>

              <div class="my-4 flex justify-end">
                <Link
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                  :href="goToShow(topic.id)">
                  Ver clase
                  <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                  </svg>
                </Link>
              </div>
            </div>
          </fwb-accordion-content>
        </fwb-accordion-panel>
      </fwb-accordion>

      <fwb-pagination
        class="mt-4"
        :model-value="currentPage"
        :total-items="topics.meta.total"
        :per-page="topics.meta.per_page"
        show-icons
        :show-labels="false"
        @update:model-value="handlePageChange($event)"
      />
    </section>

    <fwb-alert v-else icon type="warning" class="mt-7">
      Este curso no ha iniciado sus clases
    </fwb-alert>

    <div v-if="userCourse.status === 'completed'" class="text-center my-6">
      <p>¡Felicidades por completar el curso, Tu esfuerzo ha dado frutos. </p>
      <p>Haz clic en el botón a continuación para descargar tu certificado.</p>
      <p>¡Excelente trabajo!</p>
      <button
        class="inline-flex items-center px-3 py-2 my-5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
        @click="openCertificate">Descargar certificado</button>
    </div>

  </ChandelierPage>
</template>

<script setup>
import { ref } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3';
import { useCountry } from '@/Composables/useCountry';

import MainLayout from "@/Layouts/MainLayout.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  userCourse: {
    type: Object,
    required: true
  },
  course: {
    type: Object,
    required: true
  },
  topics: {
    type: Object,
    required: true,
  }
});

import {
  FwbAccordion,
  FwbAccordionContent,
  FwbAccordionHeader,
  FwbAccordionPanel,
  FwbAlert,
  FwbPagination
} from 'flowbite-vue';

const { currentCountry } = useCountry()
const currentPage = ref(props.topics.meta.current_page || 1)

const goToShow = (topicID) => {
  return route('user.elearning.topics.show', { course: props.course.id, topic: topicID,  country: currentCountry.value });
}

const handlePageChange = (newPage) => {
  router.get(route('user.elearning.topics.index', { course: props.course.id, page: newPage, country: currentCountry.value }), {}, { preserveScroll: true });
};

const openCertificate = () => {
  const pdfUrl = 'certificate';
  window.open(pdfUrl, '_blank');
}
</script>
