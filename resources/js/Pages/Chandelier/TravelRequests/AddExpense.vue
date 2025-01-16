<script setup>
import { ref, reactive, watch } from 'vue'
import { onClickOutside } from '@vueuse/core'
import { Field, Form } from 'vee-validate';
import * as yup from 'yup';

import AreaTitle from '@/Components/Chandelier/Common/AreaTitle.vue'
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  expense_kinds: Object,
})

const schema = yup.object({
  expense_kind_id: yup.string().required().label('Tipo de gasto'),
  amount: yup
    .string()
    .required()
    .matches(/^\d+(\.\d{1,2})?$/, 'El monto debe de ser un número válido')
    .label('Monto'),
  comment: yup.string().required().label('Comentario')
});

const onSubmit = (values) => {
  emit('submit', { ...values })
  handleClose()
}

const emit = defineEmits(['modal-close', 'submit'])

const target = ref(null);
const formRef = ref(null);

const handleSubmit = () => formRef.value.submitForm()
const handleClose = () => emit('modal-close')

onClickOutside(target, () => handleClose())
</script>

<template>
  <div v-if="isOpen" v-on:click.self="dismissMe"
    class="fixed flex inset-0 bg-opacity-50 min-h-screen bg-slate-700 z-50 items-center">
    <div class="flex flex-col bg-white mx-auto py-4 px-8 rounded-lg shadow-xl" ref="target">

      <AreaTitle>Agregar gasto de viaje</AreaTitle>

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit">
        <div class="mt-3">
          <label for="expense_kind_id" class="block text-sm font-medium leading-6 text-gray-900">Tipo de gasto</label>
          <div class='gw-form-input'>
            <Field
              name="expense_kind_id"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="expense in expense_kinds" :value="expense.id">
                {{ expense.name }}
              </option>
            </Field>
            <ErrorMessage name="expense_kind_id" />
          </div>
        </div>

        <div class="mt-3">
          <label for="amount" class="block text-sm font-medium leading-6 text-gray-900">Monto</label>
          <div class='gw-form-input'>
            <Field
              name="amount"
              type="text"
              class="block flex-1 border border-gray-300 rounded-md bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"

            />
            <ErrorMessage class="text-red-600 text-sm" name="amount" />
          </div>
        </div>

        <div class="mt-3">
          <label for="comment" class="block text-sm font-medium leading-6 text-gray-900">Comentario</label>
          <div class='gw-form-input'>
            <Field
              name="comment"
              as='textarea'
              rows='3'
              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            />
            <ErrorMessage name="comment" />
          </div>
        </div>

        <div class="flex justify-end mt-8">
          <ButtonSubmit>
            Agregar gasto
          </ButtonSubmit>
        </div>
      </Form>

    </div>
  </div>
</template>
