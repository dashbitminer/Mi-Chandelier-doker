<template>
  <Head>
    <title>Nuevo Categoria</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Nuevo Categoria</AreaTitle>

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit" :initial-values="form" v-slot="{ values }">
        <div class="mt-3">
          <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nombre</label>
          <div class='gw-form-input'>
            <Field
              name="name"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="name" />
          </div>
        </div>

        <div class="mt-3">
          <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Descripcion</label>
          <div class='gw-form-input'>
            <Field
              name="description"
              as='textarea'
              rows='3'
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="description" />
          </div>
        </div>


        <div class="flex justify-end mt-8">
          <LinkBack :href="goToList()">Regresar</LinkBack>
          <ButtonSubmit>
            Crear categoria
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

const { currentCountry } = useCountry()

const form = reactive({
  name: null,
  description: null,
})

const schema = yup.object({
  name: yup.string().required().label('Nombre'),
});

const currentPageActive = ref(1);
const currentPageExpired = ref(1);



const onSubmit = (values) => {
  form.name = values.name;
  form.description = values.description;

  submitCategory();
}

const submitCategory = () => {
  router.post(route('elearning.categories.store', { country: currentCountry.value }), {
    data: form,
  }, {
    preserveState: true,
  });
};

const goToList = () => {
  return route('elearning.categories.index', { country: currentCountry.value });
}
</script>
