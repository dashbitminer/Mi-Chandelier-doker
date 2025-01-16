<script setup>
// main component and imports
import { Field, Form } from 'vee-validate';
import * as yup from 'yup';import { ref, reactive } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import Layout from '@/Layouts/MainLayout.vue'
import { useCountry } from '@/Composables/useCountry';

// Common Components
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue'
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import TravelRequest from "@/Components/Chandelier/Common/TravelRequest.vue";
import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';
import ButtonLink from '@/Components/Chandelier/Common/ButtonLink.vue';
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import TravelRequestComments from '@/Components/Chandelier/Common/TravelRequestComments.vue'

const props = defineProps({
  travelRequestReview: Object,
  travelRequestReviewStatuses: Object,
  errors: Object
});

defineOptions({
  layout: Layout,
});

const { currentCountry } = useCountry();

const form = reactive({
  comment: props.travelRequestReview.comment || '',
  status: props.travelRequestReview.status || ''
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

  submitTravelExpenseReview();
}
const submitTravelExpenseReview = () => {
  router.put(route('travel-request-reviews.update', {travel_request_review: props.travelRequestReview.id, country: currentCountry.value }), {
    data: form,
  }, {
    preserveState: true,
  });
}

const goToList = () => {
  return route('travel-requests.index', { country: currentCountry.value });
}

const isEnableComment = (status) => {
  return status === 'denied' || status === 'rejected';
}
</script>

<template>

  <Head>
    <title>Revisión de solicitud de viaje</title>
  </Head>

  <main>
    <ChandelierPage>
      <AreaTitle>Revisión de solicitud de viaje</AreaTitle>

      <ul v-show="errors">
        <li v-for="error in errors" :value="uuidv4()">
          {{ error }}
        </li>
      </ul>

      <TravelRequestComments :comments="props.travelRequestReview.travelRequest.reviewerComments" />

      <TravelRequest :travelRequest="props.travelRequestReview.travelRequest" />

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit" :initial-values="form" v-slot="{ values }">
        <div class="mt-3">
          <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Resolución</label>
          <div class='gw-form-input'>
            <Field
              name="status"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="status in travelRequestReviewStatuses" :value="status.id">
                {{ status.name }}
              </option>
            </Field>
            <ErrorMessage name="status" />
          </div>
        </div>
        <div class="mt-3" v-if="isEnableComment(values.status)">
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
            Finalizar revisión
          </ButtonSubmit>
        </div>
      </Form>

    </ChandelierPage>
  </main>
</template>
