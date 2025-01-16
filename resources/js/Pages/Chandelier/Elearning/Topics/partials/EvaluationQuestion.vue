<template>
  <fwb-modal size="5xl" v-if="isOpen == true" @close="handleCloseModal">

    <template #header>
      <AreaTitle>Agregar pregunta</AreaTitle>
    </template>

    <template #body>
      <fwb-alert type="danger" class='mt-8' v-if="data.optionsIsEmpty">
        Debe seleccionar la respuesta correcta de la pregunta.
      </fwb-alert>

      <fwb-alert type="danger" class='mt-8' v-if="data.optionsIsTooMuch">
        Debe seleccionar solo 1 respuesta correcta a la pregunta.
      </fwb-alert>

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit" :initial-values="form" v-slot="{ values }">
        <div class="mt-3">
          <label for="priority" class="block text-sm font-medium leading-6 text-gray-900">No.</label>
          <div class='gw-form-input'>
            <Field
              name="priority"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="priority" />
          </div>
        </div>

        <div class="mt-3">
          <label for="text" class="block text-sm font-medium leading-6 text-gray-900">Pregunta</label>
          <div class='gw-form-input'>
            <Field
              name="text"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="text" />
          </div>
        </div>
        
        <div class="mt-3">
          <Heading class="mt-12">
            Opciones de respuestas
          </Heading>

          <FieldArray name="options" v-slot="{ fields, push, remove }">
            <ButtonLink @click="push({id: uuidv4(), text: '', priority: '', is_correct: 'false', is_new: 'true'})">
              <i class="mdi mdi-plus"></i>
              Agregar respuesta
            </ButtonLink>

            <fwb-table striped hoverable class="shadow-none">
              <thead>
                <tr>
                  <TableHeaderItem class='w-4'>No.</TableHeaderItem>
                  <TableHeaderItem>Respuesta</TableHeaderItem>
                  <TableHeaderItem class='w-4'>Correcta?</TableHeaderItem>
                  <TableHeaderItem class='w-4'></TableHeaderItem>
                </tr>
              </thead>
              <fwb-table-body class="m-3 divide-y-2 divide-slate-100">
                <fwb-table-row v-for="(entry, idx) in fields" :key="entry.key">
                  <fwb-table-cell class='w-24'>
                    <Field
                      :name="`options[${idx}].priority`"
                      class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    <ErrorMessage :name="`options[${idx}].priority`" />
                  </fwb-table-cell>
                  <fwb-table-cell>
                    <Field
                      :name="`options[${idx}].text`"
                      class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    <ErrorMessage :name="`options[${idx}].text`" />
                  </fwb-table-cell>
                  <fwb-table-cell class='w-4'>
                    <Field :name="`options[${idx}].is_correct`"
                        type="checkbox"
                        value="true"
                        :checked-value="true"
                        :unchecked-value="false"
                    />
                  </fwb-table-cell>
                  <fwb-table-cell class='w-4'>
                    <button type="button" @click="remove(idx)">
                      <i class="text-red-700 group-hover:text-slate-700 ms-3 text-xl mdi mdi-close-circle" />
                    </button>
                  </fwb-table-cell>
                </fwb-table-row>
              </fwb-table-body>
            </fwb-table>
          </FieldArray>
        </div>

        <div class="flex justify-end mt-8">
          <ButtonSubmit>
            Agregar pregunta
          </ButtonSubmit>
        </div>
      </Form>
    </template>
  </fwb-modal>

</template>

<script setup>
import { ref, reactive, watchEffect } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import { Field, Form, FieldArray } from 'vee-validate';
import * as yup from 'yup';
import { v4 as uuidv4 } from 'uuid'
import {
  FwbA,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
  FwbModal,
  FwbAlert
} from 'flowbite-vue';

import ButtonPositiveSmall from "@/Components/Chandelier/Common/ButtonPositiveSmall.vue";
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import TableHeaderItem from "@/Components/Chandelier/Common/TableHeaderItem.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';
import Modal from "@/Components/Chandelier/Common/Modal.vue";
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import Heading from '@/Components/Chandelier/Common/Heading.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  question: {
    type: Object,
    required: true,
  }
});

const emit = defineEmits(['close-modal', 'add-question'])

const form = reactive({
  id: null,
  text: null,
  priority: null,
  options: []
})

const data = reactive({
  optionsIsEmpty: false,
  optionsIsTooMuch: false
})

watchEffect(() => {
  form.id = props.question.id;
  form.text = props.question.text;
  form.priority = props.question.priority;
  form.is_new = props.question.is_new;
  form.options = props.question.options;
})

const schema = yup.object({
  priority: yup.number().required().label('No.'),
  text: yup.string().required().label('Pregunta'),
  options: yup
  .array()
  .of(
    yup.object().shape({
      //priority: yup.number().required().label('No.'),
      text: yup.string().required().label('Respuesta'),
    })
  )
  .strict(),
});

const onSubmit = (values) => {
  const correctOptions = values.options.filter(option => {
    return option.is_correct == 'true';
  });

  data.optionsIsEmpty = false;
  data.optionsIsTooMuch = false;

  if(correctOptions.length == 0){
    data.optionsIsEmpty = true;
    return;
  }

  if(correctOptions.length > 1){
    data.optionsIsTooMuch = true;
    return
  }

  handleAddQuestion(values);
  handleCloseModal();
}

const handleCloseModal = () => {
  emit('close-modal')
}

const handleAddQuestion = (values) => {
  emit('add-question', values)
}
</script>
