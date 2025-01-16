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
  time: {
    type: Object,
    required: true,
  },
  project: {
    type: Object,
    required: true,
  },
  absenceTypes: Object
});

const form = ref({...props.time});

const schema = yup.object({
  project_id: yup.string().required().label('Proyecto'),
  hours: yup
    .string()
    .required()
    .label('Horas')
    .matches(/^\d+(\.\d{1,2})?$/, 'El total de horas debe de ser un número válido')
    .test('is-greater', 'Al ingresar esta cantidad supera el máximo de 8 horas por día', function (value) {
      const { accumulatedHours } = this.parent;
      return parseFloat(accumulatedHours) + parseFloat(value) <= 8;
    }),
  absence_type_id: yup.string().nullable().label('Tipo de ausencia'),
  comment: yup.string().nullable().label('Comentario')
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
        <AreaTitle>Editar tiempo</AreaTitle>
        <button type="button" @click="$emit('close')"><span class="mdi mdi-close"></span></button>
      </div>

      Proyecto:
      {{project.name}}
      <br />
      {{time.dateFormatted}}

      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Horas pendientes para asignar</TableHeaderItem>
          <TableHeaderItem>Horas asignadas</TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row>
          <fwb-table-cell>{{ project.remainingHours }}</fwb-table-cell>
          <fwb-table-cell>{{ project.totalHours }}</fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit"  :initial-values="form">

        <div class="mt-3">
          <label for="hours" class="block text-sm font-medium leading-6 text-gray-900">Horas</label>
          <div class='gw-form-input'>
            <Field
              name="hours"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="hour in [0, 1, 2, 3, 4, 5, 6, 7, 8]" :value="hour">
                {{ hour }}
              </option>
            </Field>
            <ErrorMessage name="hours" />
          </div>
        </div>

        <div class="mt-3">
          <label for="absence_type_id" class="block text-sm font-medium leading-6 text-gray-900">Tipo de ausencias</label>
          <div class='gw-form-input'>
            <Field
              name="absence_type_id"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="absenceType in absenceTypes" :value="absenceType.id">
                {{ absenceType.name }}
              </option>
            </Field>
            <ErrorMessage name="absence_type_id" />
          </div>
        </div>

        <div class="mt-3">
          <label for="comment" class="block text-sm font-medium leading-6 text-gray-900">Comentario</label>
          <div class='gw-form-input'>
            <Field
              name="comment"
              as="textarea"
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
