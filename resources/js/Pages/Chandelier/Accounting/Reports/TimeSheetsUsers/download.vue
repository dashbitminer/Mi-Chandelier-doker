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
</script>
