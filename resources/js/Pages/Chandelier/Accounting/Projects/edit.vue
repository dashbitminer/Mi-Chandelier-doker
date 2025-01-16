<template>
  <Head>
    <title>Proyectos</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Editar Proyecto</AreaTitle>

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit" :initial-values="form">
        <div class="mt-3">
          <label for="saturday_hours" class="block text-sm font-medium leading-6 text-gray-900">Horas laborales en sábado</label>
          <div class='gw-form-input'>
            <Field
              name="saturday_hours"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="hour in [0, 1, 2, 3, 4, 5, 6, 7, 8]" :value="hour">
                {{ hour }}
              </option>
            </Field>
            <ErrorMessage name="saturday_hours" />
          </div>
        </div>

        <div class="mt-3">
          <label for="sunday_hours" class="block text-sm font-medium leading-6 text-gray-900">Horas laborales en domingo</label>
          <div class='gw-form-input'>
            <Field
              name="sunday_hours"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="hour in [0, 1, 2, 3, 4, 5, 6, 7, 8]" :value="hour">
                {{ hour }}
              </option>
            </Field>
            <ErrorMessage name="sunday_hours" />
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
import { ref, reactive } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import { Field, Form } from 'vee-validate';
import * as yup from 'yup';
import { useCountry } from '@/Composables/useCountry';

import ButtonPositiveSmall from "@/Components/Chandelier/Common/ButtonPositiveSmall.vue";
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';


defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  project: {
    type: Object,
    required: true,
  },
});

const { currentCountry } = useCountry()

const form = reactive({
  saturday_hours: props.project.data.saturday_hours,
  sunday_hours: props.project.data.sunday_hours,
})

const schema = yup.object({
  saturday_hours: yup.string().required().label('Horas laborales en sábado'),
  sunday_hours: yup.string().required().label('Horas laborales en domingo'),
});


const onSubmit = (values) => {
  form.saturday_hours = values.saturday_hours;
  form.sunday_hours = values.sunday_hours;

  submitProject();
}

const submitProject = () => {
  router.put(route('accounting.projects.update', { project: props.project.data.id, country: currentCountry.value }), {
    data: form,
  }, {
    preserveState: true,
  });
};

const goToList = () => {
  return route('accounting.projects.index', { country: currentCountry.value });
}
</script>
