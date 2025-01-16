<script setup>
// main component and imports
import { Field, Form } from 'vee-validate';
import * as yup from 'yup';
import { ref, reactive } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import Layout from '@/Layouts/MainLayout.vue'
import ErrorMessage from "@/Components/Chandelier/Common/ErrorMessage.vue";
import { useCountry } from '@/Composables/useCountry';

// Common Components
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue'
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import TimeSheet from "@/Components/Chandelier/Common/TimeSheet.vue";
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import TimeSheetProjects from '@/Components/Chandelier/Common/TimeSheetProjects.vue'

const props = defineProps({
  timeSheetReview: Object,
  timeSheetReviewStatuses: Object,
  errors: Object
});

defineOptions({
  layout: Layout,
});

const { currentCountry } = useCountry()

const form = reactive({
  comment: props.timeSheetReview.comment || '',
  status: props.timeSheetReview.status || ''
});

const schema = yup.object({
  comment: yup.string()
    .test('is-rejected', 'El comentario es obligatorio si el estado es "rechazado"', function(value) {
      const { status } = this.parent;
      return status !== 'rejected' || (status === 'rejected' && value);
    }),
  status: yup.string().required().label('Resolución'),
});

const onSubmit = (values) => {
  form.comment = values.comment
  form.status = values.status

  submitTravelExpense();
}
const submitTravelExpense = () => {
  router.put(route('time-sheet-reviews.update', { time_sheet_review: props.timeSheetReview.id, country: currentCountry.value }), {
    data: form,
  }, {
    preserveState: true,
  });
}

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
    <title>Revisión de hoja de tiempo</title>
  </Head>

  <main>
    <ChandelierPage>
      <AreaTitle>Revisión de hoja de tiempo</AreaTitle>

      {{props.timeSheetReview.timeSheet.title}}

      <ul v-show="errors">
        <li v-for="error in errors" :value="uuidv4()">
          {{ error }}
        </li>
      </ul>

      <TimeSheetProjects :projects="props.timeSheetReview.timeSheet.projects" />

      <TimeSheet :timeSheet="props.timeSheetReview.timeSheet" :openModal="openModal" />

      <h2 class="w-full text-lg font-semibold leading-7 text-gray-700 my-4">Comentario:</h2>
      {{ timeSheetReview.timeSheet.comment }}

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit" :initial-values="form" v-slot="{ values }">
        <div>
          <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Resolucion</label>
          <div class='gw-form-input'>
            <Field
              name="status"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="status in timeSheetReviewStatuses" :value="status.id">
                {{ status.name }}
              </option>
            </Field>
            <ErrorMessage name="status" />
          </div>
        </div>

        <div v-if="values.status == 'rejected'">
          <label for="comment" class="block text-sm font-medium leading-6 text-gray-900">Comentarios</label>
          <div class='gw-form-input'>
            <Field
              name="comment"
              as='textarea'
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="comment" />
          </div>
        </div>

        <div class="flex justify-end mt-8">
          <LinkBack :href="goToList()">Regresar</LinkBack>
          <ButtonSubmit>
            Finalizar revision
          </ButtonSubmit>
        </div>
      </Form>

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
