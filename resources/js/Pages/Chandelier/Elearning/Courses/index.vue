<template>
  <Head>
    <title>Cursos</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Cursos</AreaTitle>

      <LinkToPrimary :href="goToNew()">
        Crear curso
      </LinkToPrimary>

      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Curso</TableHeaderItem>
          <TableHeaderItem>Categoria</TableHeaderItem>
          <TableHeaderItem>Tipo</TableHeaderItem>
          <TableHeaderItem>Alcance</TableHeaderItem>
          <TableHeaderItem>Estado</TableHeaderItem>
          <TableHeaderItem></TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="course in courses.data" :key="course.id">
          <fwb-table-cell>{{ course.name }}</fwb-table-cell>
          <fwb-table-cell>{{ course.categoryName }}</fwb-table-cell>
          <fwb-table-cell>{{ course.courseTypeName }}</fwb-table-cell>
          <fwb-table-cell>{{ course.scopeName }}</fwb-table-cell>
          <fwb-table-cell>{{ course.statusName }}</fwb-table-cell>
          <fwb-table-cell>
            <span class="mr-5">{{ course.active == 'true' ? 'Deshabilitar' : 'Habilitar' }}</span>
            <fwb-toggle
              v-model="course.active"
              @change="toggleCourse(course.id)"
            />
            <LinkToPrimary :href="goToTopics(course.id)">
              Clases del curso
            </LinkToPrimary>
            <LinkToPrimary :href="goToEdit(course.id)">
              Editar curso
            </LinkToPrimary>
            <ButtonPrimary @click="goToDelete(course)">
              Eliminar curso
            </ButtonPrimary>
          </fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      <fwb-pagination
        class="mt-4"
        :model-value="currentPage"
        :total-items="courses.total"
        :per-page="courses.per_page"
        show-icons
        :show-labels="false"
        @update:model-value="handlePageChange($event)"
      />

      <TopicDeleteForm
        :isOpen="courseDeleteFormIsOpen"
        :course="data.course"
        @close-modal="courseDeleteFormClose"
        @delete-course="submitDeleteCourse" />
    </ChandelierPage>
  </main>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import { useCountry } from '@/Composables/useCountry';

import ButtonPrimary from '@/Components/Chandelier/Common/ButtonPrimary.vue';
import LinkToPrimary from '@/Components/Chandelier/Common/LinkToPrimary.vue';
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import TableHeaderItem from "@/Components/Chandelier/Common/TableHeaderItem.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import TopicDeleteForm from "@/Pages/Chandelier/Elearning/Courses/partials/CourseDeleteForm.vue";


import {
  FwbA,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
  FwbPagination,
  FwbToggle
} from 'flowbite-vue';

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  courses: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry();

const data = reactive({
  course: null
});

const currentPage = ref(props.courses.current_page || 1);

const handlePageChange = (newPage) => {
  router.get(route('elearning.courses.index', { page: newPage, country: currentCountry.value }), {}, { preserveScroll: true });
};

const goToNew = () => {
  return route('elearning.courses.create', { country: currentCountry.value });
}

const goToEdit = (id) => {
  return route('elearning.courses.edit', { course: id, country: currentCountry.value });
}

const goToTopics = (id) => {
  return route('elearning.courses.topics.index', { course: id, country: currentCountry.value });
}

const goToDelete = (course) => {
  data.course = course;
  courseDeleteFormOpen()
}
const courseDeleteFormIsOpen = ref(false)
const courseDeleteFormOpen = () => {
  courseDeleteFormIsOpen.value = true
}
const courseDeleteFormClose = () => {
  courseDeleteFormIsOpen.value = false
}
const submitDeleteCourse = (courseID, userAcceptance) => {
  if(userAcceptance)
    router.delete(route('elearning.courses.destroy', { course: courseID, country: currentCountry.value }))
}

const toggleCourse = (id) => {
  axios.put(route('elearning.courses.toggle', { course: id, country: currentCountry.value }))
    .then((response) => {
      if(response.status === 200){

        const index = props.courses.data.findIndex(course => course.id === response.data.id);
        if (index !== -1)
          props.courses.data[index] = response.data;

      }
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}
</script>
