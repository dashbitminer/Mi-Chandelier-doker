<template>
  <Head>
    <title>Temas</title>
  </Head>

  <ChandelierPage>
    <AreaTitle>
      Centro de ayuda - {{ course.name }}
    </AreaTitle>

    <p class="mb-7">{{ course.description }}</p>

    <fwb-accordion class="mt-7" v-if="topics && topics.data && topics.data.length > 0">
      <fwb-accordion-panel v-for="topic in topics.data" :key="topic.id">
        <fwb-accordion-header>
          <div class="flex justify-between">
            <span>
              {{ topic.name }}
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
                Ver tema
                <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                </svg>
              </Link>
            </div>
          </div>
        </fwb-accordion-content>
      </fwb-accordion-panel>
    </fwb-accordion>

    <fwb-alert v-else icon type="warning" class="mt-7">
      Este curso no tiene temas disponibles
    </fwb-alert>
  </ChandelierPage>
</template>

<script setup>
// import { ref, reactive } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3';

import MainLayout from "@/Layouts/MainLayout.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";

import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
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
  FwbAlert
} from 'flowbite-vue';

const goToShow = (topicID) => {
  return route('user.elearning.subjects.show', { help_center: props.course.id, subject: topicID });
}
</script>
