<template>
  <Head>
    <title>Editar Usuario</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Editar Usuario</AreaTitle>
      
      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit" :initial-values="form" v-slot="{ values }">
        <div class="mt-3">
          <label for="role" class="block text-sm font-medium leading-6 text-gray-900">Rol</label>
          <div class='gw-form-input'>
            <Field
              name="role"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="role in props.roles.data" :value="role.name">
                {{ role.name }}
              </option>
            </Field>
            <ErrorMessage name="role" />
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
  user: {
    type: Object,
    required: true,
  },
  roles: {
    type: Object,
    required: true,
  },
});

const { currentCountry } = useCountry()

const form = reactive({
  role: props.user.data.role,
})

const schema = yup.object({
  role: yup.string().required().label('Rol'),
});

const currentPageActive = ref(1);
const currentPageExpired = ref(1);


const onSubmit = (values) => {
  form.role = values.role;

  submitUser();
}

const submitUser = () => {
  router.put(route('backoffice.users.update', { user: props.user.data.id, country: currentCountry.value }), {
    data: form,
  }, {
    preserveState: true,
  });
};

const goToList = () => {
  return route('backoffice.users.index', { country: currentCountry.value });
}
</script>
