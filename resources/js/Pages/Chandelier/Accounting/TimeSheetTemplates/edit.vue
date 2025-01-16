<template>
  <Head>
    <title>Hojas de tiempo</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Edición hoja de tiempo</AreaTitle>

      <Heading>{{timeSheetTemplate.monthNameWithYear}}</Heading>

      <div class="flex mt-8">
        <div v-for="(dayName, i) in timeSheetTemplate.dayNames" :key="i" class="w-24">
          <div class="mb-1 ml-1 p-3 rounded bg-slate-200 text-center">
            <span class="block">{{ dayName }}</span>
          </div>
        </div>
      </div>

      <!-- semanas -->
      <div class="flex" v-for="(week, i) in timeSheetTemplate.weeks" :key="i">

        <!-- dias de la semana -->
        <div v-for="(day, j) in week" :key="j" class="w-24">
          <div
            class="h-24 mb-1 ml-1 p-3 border"
            v-if="day.date"
            @click="toogleHoliday(day)"
            :class="{ 'cursor-pointer': true, 'bg-green-300': !day.isWeekend, 'bg-sky-500': props.timeSheetTemplate.holidays.indexOf(day.date) !== -1, 'bg-red-200': day.isWeekend }">
            <strong class="block text-right">{{ day.day }}</strong>
          </div>

          <div class="h-24 mb-1 ml-1 p-3 border" v-if="!day.date"></div>
        </div>
      </div>

      <ul class="mt-8">
        <div class="flex">
          <span class="inline-block mb-1 mr-1 h-6 w-8 bg-green-300 text-center">
          </span>
          Dia laborado
        </div>
        <div class="flex">
          <span class="inline-block mb-1 mr-1 h-6 w-8 bg-sky-500 text-center">
          </span>
          Dia festivo (no laborado)
        </div>
        <div class="flex">
          <span class="inline-block mb-1 mr-1 h-6 w-8 bg-red-200 text-center">
          </span>
          Dia bloqueado (no laborado)
        </div>
      </ul>

      <div class="flex justify-end mt-8">
        <LinkBack :href="goToList()">Regresar</LinkBack>
        <ButtonSubmit
          type="button"
          @click="saveTimeSheetTemplate()"
          v-if="timeSheetTemplate.status === 'unpublish'">
          Guardar cambios
        </ButtonSubmit>
        <ButtonSubmit
          type="button"
          @click="publishTimeSheetTemplate()"
          v-if="timeSheetTemplate.status === 'unpublish'">
          Publicar
        </ButtonSubmit>
      </div>

    </ChandelierPage>
  </main>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import { useCountry } from '@/Composables/useCountry';

import ButtonPositiveSmall from "@/Components/Chandelier/Common/ButtonPositiveSmall.vue";
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import TableHeaderItem from "@/Components/Chandelier/Common/TableHeaderItem.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import Heading from '@/Components/Chandelier/Common/Heading.vue';

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
  timeSheetTemplate: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry();

const form = reactive({
  holidays: props.timeSheetTemplate.holidays
});

const toogleHoliday = (config) => {
  const indexToRemove = props.timeSheetTemplate.holidays.indexOf(config.date);

  if(indexToRemove !== -1){
    props.timeSheetTemplate.holidays.splice(indexToRemove, 1);
  } else{
    props.timeSheetTemplate.holidays.push(config.date)
  }
}

const saveTimeSheetTemplate = () => {
  router.put(route('accounting.time-sheet-templates.update', { time_sheet_template: props.timeSheetTemplate.id, country: currentCountry.value }), {
    data: {
      holidays: props.timeSheetTemplate.holidays
    },
  }, {
    preserveState: true,
  });
}

const publishTimeSheetTemplate = () => {
  router.post(route('accounting.time-sheet-templates.publish', { time_sheet_template: props.timeSheetTemplate.id, country: currentCountry.value }), {
  }, {
    preserveState: true,
  });
}

const goToList = () => {
  return route('accounting.time-sheet-templates.index', { country: currentCountry.value });
}

</script>
