<script setup>
// main component and imports
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import Layout from '@/Layouts/MainLayout.vue'
import { useCountry } from '@/Composables/useCountry';

// Common Components
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue'
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import TimeSheet from "@/Components/Chandelier/Common/TimeSheet.vue";
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import TimeSheetComments from '@/Components/Chandelier/Common/TimeSheetComments.vue'
import TimeSheetProjects from '@/Components/Chandelier/Common/TimeSheetProjects.vue'

const props = defineProps({
  timeSheetReview: Object
});

defineOptions({
  layout: Layout,
});

const { currentCountry } = useCountry()

let modalIsOpen = ref(false);
let currentProjectTime = ref(false);

const openModal = (projectTime) => {
  modalIsOpen.value = true;
  currentProjectTime.value = projectTime;
};

const closeModal = () => {
  modalIsOpen.value = false;
  currentProjectTime.value = false;
}

const goToList = () => {
  return route('time-sheets.index', { country: currentCountry.value });
}
</script>

<template>

  <Head>
    <title>Hoja de tiempo</title>
  </Head>

  <main>
    <ChandelierPage>
      <AreaTitle>Hoja de tiempo</AreaTitle>

      {{props.timeSheetReview.timeSheet.title}}
      
      <TimeSheetProjects :projects="props.timeSheetReview.timeSheet.projects" />

      <TimeSheet :timeSheet="props.timeSheetReview.timeSheet" :openModal="openModal" />


      <h2 class="w-full text-lg font-semibold leading-7 text-gray-700 my-4">Comentarios:</h2>

      <div v-show="timeSheetReview.timeSheet.comment" class="border rounded-md p-3 my-3">
        <p class="text-gray-600 mt-2">
          {{timeSheetReview.timeSheet.comment}}
        </p>
      </div>

      <TimeSheetComments :comments="props.timeSheetReview.timeSheet.reviewerComments" />

      <div class="flex justify-end mt-8">
        <LinkBack :href="goToList()">Regresar</LinkBack>
      </div>

    </ChandelierPage>

    <div class="fixed flex inset-0 bg-opacity-50 min-h-screen bg-slate-700 z-50 items-center" v-show="modalIsOpen">
      <div class="flex flex-col bg-white mx-auto py-4 px-8 rounded-lg shadow-xl" ref="target">
        {{currentProjectTime.date}}
        <p>
          Proyecto: {{ currentProjectTime.projectName }}
        </p>
        <p>
          Horas: {{ currentProjectTime.hours }}
        </p>
        <p v-if="currentProjectTime.absence_type_id">
          Tipo de ausencia: {{ currentProjectTime.absenceTypeName }}
        </p>
        <p v-if="currentProjectTime.comment">
          Comentario: {{ currentProjectTime.comment }}
        </p>


        <button @click="closeModal">Cerrar</button>
      </div>
    </div>
  </main>
</template>

