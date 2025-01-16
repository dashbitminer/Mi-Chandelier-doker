<script setup>
import {ref} from 'vue';
import {Field, Form} from 'vee-validate';
import * as yup from 'yup';

import {
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
} from 'flowbite-vue';

import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import TableHeaderItem from '@/Components/Chandelier/Common/TableHeaderItem.vue';

const props = defineProps({
  comment: {
    type: Object,
    required: true,
  },
  project: {
    type: Object,
    required: true,
  },
  week: {
    type: Object,
    required: true,
  },
});

const form = ref({...props.comment});

const schema = yup.object({
  comment: yup.string().required().label('Tareas')
});

const emit = defineEmits(['update-data', 'close']);
const onSubmit = (values) => {
  emit('update-data', values);
  emit('close');
};

</script>

<template>
  <div class="modal">
    <div class="modal-content">

      <div class="flex justify-between">
        <AreaTitle>Editar tareas</AreaTitle>
        <button type="button" @click="$emit('close')"><span class="mdi mdi-close"></span></button>
      </div>

      Proyecto:
      {{project.name}}
      <br />
      {{week.title}}

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit"  :initial-values="form">

        <div class="mt-3">
          <label for="comment" class="block text-sm font-medium leading-6 text-gray-900">Tareas</label>
          <div class='gw-form-input'>
            <Field
              name="comment"
              as="textarea"
              rows="10"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="comment" />
          </div>
        </div>

        <div class="flex justify-end mt-8">
          <ButtonSubmit>
            Guardar cambios
          </ButtonSubmit>
        </div>
      </Form>

    </div>
  </div>
</template>

<style scoped>
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 400px;
}
</style>
