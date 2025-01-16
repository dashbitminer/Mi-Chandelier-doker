<template>
  <Head title="Editar solicitud de viaje" />

  <main>
    <ChandelierPage>
      <AreaTitle>Editar tipo de pago</AreaTitle>

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit" :initial-values="form">
        <div class="mt-3">
          <label for="period" class="block text-sm font-medium leading-6 text-gray-900">Tipo de pago</label>
          <div class='gw-form-input'>
            <Field
              name="period"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="period in props.periods" :value="period.id">
                {{ period.name }}
              </option>
            </Field>
            <ErrorMessage name="period" />
          </div>
        </div>

        <div class="mt-3">
          <label for="comment" class="block text-sm font-medium leading-6 text-gray-900">Motivo del cambio</label>
          <div class='gw-form-input'>
            <Field
              name="comment"
              as='textarea'
              rows='3'
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="comment" />
          </div>
        </div>

        <div class="my-5">
          <div class='gw-form-input'>
            <label for="user_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
              <Field
                name="user_confirmation"
                type="checkbox"
                value="true"
                :checked-value="true"
                :unchecked-value="false"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              />
              Confirmo que deseo actualizar el tipo de pago
            </label>
            <ErrorMessage name="user_confirmation" />
          </div>
        </div>

        <div class="flex justify-end mt-8">
          <LinkBack :href="goToList()">Regresar</LinkBack>
          <ButtonSubmit>
            Guardar cambios
          </ButtonSubmit>
        </div>
      </Form>
    </ChandelierPage>

  </main>
</template>

<script setup>
import { reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Field, Form } from 'vee-validate';
import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';
import * as yup from 'yup';
import { useCountry } from '@/Composables/useCountry';

import Layout from '@/Layouts/MainLayout.vue';
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue';
import AreaTitle from '@/Components/Chandelier/Common/AreaTitle.vue';
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';

defineOptions({
  layout: Layout,
});

const props = defineProps({
  periods: Array,
  errors: Object
});

const { currentCountry } = useCountry()

const form = reactive({
  comment: null,
  period: null,
  user_confirmation: false
});

const schema = yup.object({
  period: yup.string().required().label('Tipo de pago'),
  comment: yup.string().required().label('Motivo del cambio'),
  user_confirmation: yup.boolean().oneOf([true])
});

const onSubmit = (values) => {
  form.period = values.period;
  form.comment = values.comment;
  form.user_confirmation = values.user_confirmation ? true : false;

  submitPaymentPeriod();
}

const submitPaymentPeriod = () => {
  router.post(route('accounting.payment-periods.store', { country: currentCountry.value }), {
    data: form,
  }, {
    preserveState: true,
  });
};

const goToList = () => {
  return route('accounting.payment-periods', { country: currentCountry.value });
}
</script>
