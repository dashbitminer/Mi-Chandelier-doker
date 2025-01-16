<template>
  <main>
    {{data}}
  </main>
</template>

<script setup>
import { ref } from 'vue';
import { Link, Head } from '@inertiajs/vue3'; // Firstly import the Link and Head components

import ButtonPositiveSmall from "@/Components/Chandelier/Common/ButtonPositiveSmall.vue";
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import TableHeaderItem from "@/Components/Chandelier/Common/TableHeaderItem.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import { useCountry } from '@/Composables/useCountry';

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
  data: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry();

const currentPageActive = ref(1);
const currentPageExpired = ref(1);

const goTo = (id) => {
  return route('accounting.time-sheet-templates.edit', {time_sheet_template: id, country: currentCountry.value});
}
</script>
