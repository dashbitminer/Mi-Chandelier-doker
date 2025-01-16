<script setup>
import { v4 as uuidv4, validate as isUuid } from 'uuid'
import { ref, onMounted, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Field, Form } from 'vee-validate';
import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';
import * as yup from 'yup';
import { useCountry } from '@/Composables/useCountry';

import Layout from '@/Layouts/MainLayout.vue';
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue';
import AreaTitle from '@/Components/Chandelier/Common/AreaTitle.vue';
import Heading from '@/Components/Chandelier/Common/Heading.vue';
import ButtonLink from '@/Components/Chandelier/Common/ButtonLink.vue';
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import TableHeaderItem from '@/Components/Chandelier/Common/TableHeaderItem.vue'
import TableDataItem from '@/Components/Chandelier/Common/TableDataItem.vue'
import AddExpense from '@/Pages/Chandelier/TravelRequests/AddExpense.vue';
import TravelRequestComments from '@/Components/Chandelier/Common/TravelRequestComments.vue'

import {
  FwbTable,
} from 'flowbite-vue'

defineOptions({
  layout: Layout,
});

const props = defineProps({
  travelRequest: Object,
  projects: Array,
  expenseKinds: Array,
  currentProject: Object,
  errors: Object
});

const { currentCountry } = useCountry()

const form = reactive({
  project_id: null,
  description: null,
  departure_date: null,
  arrival_date: null,
  request_cash_advance: false,
  expenses: [],
  expensesToDelete: []
});

const schema = yup.object({
  project_id: yup.string().required().label('Proyecto'),
  description: yup.string().required().label('Descripcion'),
  departure_date: yup
    .date()
    .required()
    .label('Inicio del viaje')
    .typeError('La fecha de inicio no es válida'),
  arrival_date: yup
    .date()
    .required()
    .label('Fin del viaje')
    .min(
      yup.ref('departure_date'),
      'La fecha de llegada debe ser igual o mayor a la fecha de inicio'
    )
    .typeError('La fecha de llegada no es válida'),
});

const onSubmit = (values) => {
  form.project_id = values.project_id;
  form.description = values.description;
  form.departure_date = values.departure_date;
  form.arrival_date = values.arrival_date;
  form.request_cash_advance = values.request_cash_advance;

  submitTravelRequest();
}

const submitTravelRequest = () => {
  form.expenses = form.expenses.map(expense => {
    return {
      ...(isUuid(expense.id) ? {} : { id: expense.id }),
      expense_kind_id: expense.expense_kind_id,
      amount: expense.amount,
      comment: expense.comment,
    };
  });

  router.put(route('travel-requests.update', { country: currentCountry.value, travel_request: props.travelRequest.id }), {
    data: form,
  }, {
    preserveState: true,
  });
};

onMounted(() => {
  if (props.travelRequest) {
    form.project_id = props.currentProject.id;
    form.description = props.travelRequest.description;
    form.departure_date = props.travelRequest.departure_date;
    form.arrival_date = props.travelRequest.arrival_date;
    form.request_cash_advance = Boolean(props.travelRequest.request_cash_advance);
    form.expenses = props.travelRequest.expenses;
  }
});

const expenseModal = ref(false);

const openExpenseModal = () => {
  expenseModal.value = true;
}

const closeExpenseModal = () => {
  expenseModal.value = false;
}

const addExpense = (data) => {
  let expenseKind = props.expenseKinds.find((expense_kind) => expense_kind.id === data.expense_kind_id)

  form.expenses.push({
    id: data.id || uuidv4(),
    expense_kind_id: data.expense_kind_id,
    expenseKindName: expenseKind ? expenseKind.name : '',
    amount: data.amount,
    comment: data.comment,
  });
};

const removeTravelExpense = (id) => {
  form.expenses = form.expenses.filter((expense) => expense.id !== id);

  if (!isUuid(id)) {
    form.expensesToDelete.push(id);
  }
}

const goToList = () => {
  return route('travel-requests.index', { country: currentCountry.value });
}
</script>

<template>
  <Head title="Edición solicitud de viaje" />

  <main>
    <ChandelierPage>
      <AreaTitle>Edición solicitud de viaje</AreaTitle>

      <TravelRequestComments :comments="props.travelRequest.reviewerComments" />

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit">
        <div class="mt-3">
          <label for="project_id" class="block text-sm font-medium leading-6 text-gray-900">Proyecto</label>
          <div class='gw-form-input'>
            <Field
              name="project_id"
              as="select"
              v-model="form.project_id"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="project in props.projects" :value="project.id">
                {{ project.name }}
              </option>
            </Field>
            <ErrorMessage name="project_id" />
          </div>
        </div>

        <div class="mt-3">
          <label for="departure_date" class="block text-sm font-medium leading-6 text-gray-900">Fecha inicio del viaje</label>
          <div class='gw-form-input'>
            <Field
              name="departure_date"
              type="date"
              v-model="form.departure_date"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="departure_date" />
          </div>
        </div>

        <div class="mt-3">
          <label for="arrival_date" class="block text-sm font-medium leading-6 text-gray-900">Fecha fin del viaje</label>
          <div class='gw-form-input'>
            <Field
              name="arrival_date"
              type="date"
              v-model="form.arrival_date"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="arrival_date" />
          </div>
        </div>

        <div class="mt-3">
          <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Descripcion</label>
          <div class='gw-form-input'>
            <Field
              name="description"
              as='textarea'
              rows='3'
              v-model="form.description"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="description" />
          </div>
        </div>

        <div class="mt-3">
          <div class='gw-form-input'>
            <label for="request_cash_advance" class="block text-sm font-medium leading-6 text-gray-900">
              <Field
                name="request_cash_advance"
                v-slot="{ field }"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              >
                <input
                  type="checkbox"
                  v-bind="field"
                  v-model="form.request_cash_advance"
                />
              </Field>
              Solicitar adelanto
            </label>
            <ErrorMessage name="request_cash_advance" />
          </div>
        </div>

        <Heading class="mt-12">
          Gastos del viaje
        </Heading>

        <ButtonLink @click="openExpenseModal">
          <i class="mdi mdi-plus"></i>
          Agregar Gasto
        </ButtonLink>

        <fwb-table class="m-2 mt-0 table-auto shadow-none">
          <thead>
            <tr>
              <TableHeaderItem>Tipo</TableHeaderItem>
              <TableHeaderItem>Monto</TableHeaderItem>
              <TableHeaderItem>Comentario</TableHeaderItem>
              <TableHeaderItem>Acciones</TableHeaderItem>
            </tr>
          </thead>
          <tbody class="m-3 divide-y-2 divide-slate-100">
            <tr v-for="expense in form.expenses" :key="expense.id"
              class="border-b border-gray-200 hover:bg-gray-100">
              <TableDataItem :value=expense.expenseKindName />
              <TableDataItem :value=expense.amount />
              <TableDataItem :value=expense.comment />
              <td class="font-light text-right">
                <button @click.prevent="removeTravelExpense(expense.id)"
                  class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-4 rounded shadow-md m-2">
                  <span class="mdi mdi-trash-can-outline"></span>
                </button>
              </td>


            </tr>
          </tbody>
        </fwb-table>

        <div class="flex justify-end mt-8">
          <LinkBack :href="goToList()">Regresar</LinkBack>
          <ButtonSubmit>
            Guardar cambios
          </ButtonSubmit>
        </div>
      </Form>
    </ChandelierPage>

    <!-- Modal Add Expense -->
    <AddExpense :isOpen="expenseModal" :expense_kinds="expenseKinds"
      @modal-close="closeExpenseModal" @submit="addExpense" />
  </main>
</template>
