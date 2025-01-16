<template>
  <fwb-modal size="5xl" v-if="isOpen == true" @close="handleCloseModal">

    <template #header>
      <AreaTitle>Eliminar clase</AreaTitle>
    </template>
   
    <template #body>
    
      <p>¿Está seguro que desea eliminar la clase <strong>{{topic.name}}</strong> del curso {{props.course.name}}?</p>
      
      <p>Recuerde que una vez eliminada la clase, la clase y su configuración se perderán por completo".</p>
      
      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit" :initial-values="form">
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
              Estoy de acuerdo
            </label>
            <ErrorMessage name="user_confirmation" />
          </div>
        </div>

        <div class="flex justify-end mt-8">
          <ButtonSubmit>
            Eliminar
          </ButtonSubmit>
        </div>
      </Form>
    </template>
  </fwb-modal>

</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import { Field, Form } from 'vee-validate';
import * as yup from 'yup';

import {
  FwbModal,
} from 'flowbite-vue';

import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';

const props = defineProps({
  course: {
    type: Object,
    required: true,
  },
  topic: {
    type: Object,
    required: true,
  },
  isOpen: {
    type: Boolean,
    required: true,
  }
});

const emit = defineEmits(['close-modal', 'delete-topic'])

const form = reactive({
  user_confirmation: false,
})

const schema = yup.object({
  user_confirmation: yup.boolean().oneOf([true])
});

const onSubmit = (values) => {
  let userConfirmation = form.user_confirmation = values.user_confirmation ? true : false;
  
  handleDeleteTopic(userConfirmation);
  handleCloseModal();
}

const handleCloseModal = () => {
  emit('close-modal')
}

const handleDeleteTopic = (userConfirmation) => {
  emit('delete-topic', props.topic.id, userConfirmation)
}
</script>
