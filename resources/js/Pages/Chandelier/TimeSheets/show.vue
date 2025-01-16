<template>

  <Head>
    <title>Hoja de tiempo</title>
  </Head>

  <main>
    <ChandelierPage>

      <AreaTitle class="my-8">Hoja de tiempo</AreaTitle>

      {{props.timeSheet.title}}
      
      <TimeSheetProjects :projects="props.timeSheet.projects" />

      <TimeSheet :timeSheet="props.timeSheet" :openModal="openModal" />

      <TimeSheetComments :comments="props.timeSheet.reviewerComments" />

      <div class="flex justify-end mt-8">
        <LinkBack :href="goToList()">Regresar</LinkBack>
      </div>
    </ChandelierPage>

    <div class="fixed flex inset-0 bg-opacity-50 min-h-screen bg-slate-700 z-50 items-center" v-show="modalIsOpen">
      <div class="flex flex-col bg-white mx-auto py-4 px-8 rounded-lg shadow-xl" ref="target">
        <div class="flex justify-between">
          <AreaTitle>{{currentProjectTime.date}}</AreaTitle>
          <button class="ml-5" @click="closeModal">
            <span class="mdi mdi-close"></span>
          </button>
        </div>


        <article class="mb-5">
          <p class="mr-2 text-sm">Proyecto:</p>
          <span class="text-slate-600 font-medium break-words"> {{ currentProjectTime.projectName }} </span>
        </article>

        <article class="mb-5">
          <p class="mr-2 text-sm">Horas:</p>
          <span class="text-slate-600 font-medium break-words"> {{ currentProjectTime.hours }} </span>
        </article>

        <article class="mb-5">
          <p class="mr-2 text-sm">Tipo de ausencia:</p>
          <span class="text-slate-600 font-medium break-words"> {{ currentProjectTime.absenceTypeName }} </span>
        </article>

        <article class="mb-5">
          <p class="mr-2 text-sm">Comentario:</p>
          <span class="text-slate-600 font-medium break-words"> {{ currentProjectTime.comment }} </span>
        </article>
      </div>
    </div>

  </main>
</template>

<script setup>
// main component and imports
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import Layout from '@/Layouts/MainLayout.vue'
import { onClickOutside } from '@vueuse/core'
import { useCountry } from '@/Composables/useCountry';

// Common Components
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue'
import TimeSheet from "@/Components/Chandelier/Common/TimeSheet.vue";
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import TimeSheetComments from '@/Components/Chandelier/Common/TimeSheetComments.vue'
import TimeSheetProjects from '@/Components/Chandelier/Common/TimeSheetProjects.vue'

const props = defineProps({
  timeSheet: Object
});

defineOptions({
  layout: Layout,
});

const { currentCountry } = useCountry();

let target = ref(null);
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

onClickOutside(target, () => closeModal())

const goToList = () => {
  return route('time-sheets.index', { country: currentCountry.value});
}
</script>
