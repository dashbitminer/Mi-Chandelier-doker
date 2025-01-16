<template>
  <Head>
    <title>Clases</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Clases</AreaTitle>

      <AreaSubTitle>{{course.name}}</AreaSubTitle>

      <LinkToPrimary :href="goToNew()">Crear clase</LinkToPrimary>

      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>No.</TableHeaderItem>
          <TableHeaderItem>Clase</TableHeaderItem>
          <TableHeaderItem>Require evaluacion</TableHeaderItem>
          <TableHeaderItem></TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="topic in topics.data" :key="topic.id">
          <fwb-table-cell>{{ topic.priority }}</fwb-table-cell>
          <fwb-table-cell>{{ topic.name }}</fwb-table-cell>
          <fwb-table-cell>{{ topic.requireEvaluationLabel }}</fwb-table-cell>
          <fwb-table-cell>
            <LinkToPrimary :href="goToEdit(topic.id)">
              Editar
            </LinkToPrimary>
            <ButtonPrimary @click="goToDelete(topic)">
              Eliminar
            </ButtonPrimary>
          </fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination
        class="mt-4"
        :model-value="currentPage"
        :total-items="topics.total"
        :per-page="topics.per_page"
        show-icons
        :show-labels="false"
        @update:model-value="handlePageChange($event)"
      />

      <TopicDeleteForm
        :isOpen="topicDeleteFormIsOpen"
        :course="course"
        :topic="data.topic"
        @close-modal="topicDeleteFormClose"
        @delete-topic="submitDeleteTopic" />

    </ChandelierPage>
  </main>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import { useCountry } from '@/Composables/useCountry';

import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import AreaSubTitle from "@/Components/Chandelier/Common/AreaSubTitle.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import TableHeaderItem from "@/Components/Chandelier/Common/TableHeaderItem.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import TopicDeleteForm from "@/Pages/Chandelier/Elearning/Topics/partials/TopicDeleteForm.vue";
import LinkToPrimary from "@/Components/Chandelier/Common/LinkToPrimary.vue";
import ButtonPrimary from '@/Components/Chandelier/Common/ButtonPrimary.vue';

import {
  FwbA,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
  FwbPagination,
  FwbAlert
} from 'flowbite-vue';

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  course: {
    type: Object,
    required: true,
  },
  topic: {
    type: Object,
    required: false,
  },
  topics: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry();

const data = reactive({
  topic: null
});

const currentPage = ref(props.topics.current_page || 1);

const handlePageChange = (newPage) => {
  router.get(route('elearning.courses.topics.index', { course: props.course.id, page: newPage, country: currentCountry.value }), {}, { preserveScroll: true });
};

const goToNew = () => {
  return route('elearning.courses.topics.create', { course: props.course.id, country: currentCountry.value });
}

const goToEdit = (topicID) => {
  return route('elearning.courses.topics.edit', { course: props.course.id, topic: topicID, country: currentCountry.value });
}

const goToDelete = (topic) => {
  data.topic = topic;
  topicDeleteFormOpen()
}

const topicDeleteFormIsOpen = ref(false)

const topicDeleteFormOpen = () => {
  topicDeleteFormIsOpen.value = true
}

const topicDeleteFormClose = () => {
  topicDeleteFormIsOpen.value = false
}

const submitDeleteTopic = (topicID, userAcceptance) => {
  if(userAcceptance)
    router.delete(route('elearning.courses.topics.destroy', { course: props.course.id, topic: topicID, country: currentCountry.value }))
}
</script>
