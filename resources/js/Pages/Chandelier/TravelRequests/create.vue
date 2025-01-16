<script setup>
// main component and imports
import { v4 as uuidv4 } from 'uuid'
import { Field, Form } from 'vee-validate';
import * as yup from 'yup';
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/MainLayout.vue';
import { FwbTable } from 'flowbite-vue';
import { useCountry } from '@/Composables/useCountry';

// Common Components
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue';
import Heading from '@/Components/Chandelier/Common/Heading.vue';
import ButtonLink from '@/Components/Chandelier/Common/ButtonLink.vue';
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import TableHeaderItem from '@/Components/Chandelier/Common/TableHeaderItem.vue';
import TableDataItem from '@/Components/Chandelier/Common/TableDataItem.vue';
import AreaTitle from '@/Components/Chandelier/Common/AreaTitle.vue';
import AreaSubTitle from '@/Components/Chandelier/Common/AreaSubTitle.vue';
import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';

// Modal Component
import AddExpense from '@/Pages/Chandelier/TravelRequests/AddExpense.vue'

const props = defineProps({
  projects: Object,
  expense_kinds: Object,
  errors: Object
});

defineOptions({
  layout: Layout,
});

const { currentCountry } = useCountry()

const form = reactive({
  project_id: null,
  description: null,
  departure_date: null,
  arrival_date: null,
  request_cash_advance: false,
  expenses: [],
});


const onSubmit = (values) => {
  form.project_id = values.project_id
  form.description = values.description
  form.departure_date = values.departure_date
  form.arrival_date = values.arrival_date
  form.request_cash_advance = values.request_cash_advance ? true : false

  submitTravelExpense();
}

const schema = yup.object({
  project_id: yup.string().required().label('Proyecto'),
  description: yup.string().required().label('Descripcion'),
  departure_date: yup.string().required().label('Inicio del viaje'),
  arrival_date: yup
    .string()
    .required()
    .label('Fin del viaje')
    .test('is-greater', 'La fecha de llegada debe ser igual o mayor a la fecha de inicio', function (value) {
      const { departure_date } = this.parent;
      return new Date(value) >= new Date(departure_date);
    }),
});

// AddExpense.vue actions
const expenseDialog = ref(false);

const openExpenseDialog = () => {
  expenseDialog.value = true;
}

const closeExpenseDialog = () => {
  expenseDialog.value = false;
}

const handleModalData = (data) => {
  let expenseKind = props.expense_kinds.find((expense_kind) => expense_kind.id === data.expense_kind_id);

  data.id = uuidv4();
  data.new_record = true
  if(expenseKind) data.expense_kind_name = expenseKind.name;

  form.expenses.push(data);
}

const removeTravelExpense = (id) => {
  form.expenses = form.expenses.filter((expense) => expense.id !== id);
}

const submitTravelExpense = () => {
  router.post(
    route('travel-requests.store', { country: currentCountry.value }),
    {
      data: form,
    }, {
      preserveState: true,
  });
}

const goToList = () => {
  return route('travel-requests.index', { country: currentCountry.value });
}
</script>

<template>
  <Head>
    <title>Nueva solicitud de viaje</title>
  </Head>

  <main>
    <ChandelierPage>
      <AreaTitle>Nueva solicitud de viaje</AreaTitle>

      <ul v-show="errors">
        <li v-for="error in errors" :value="uuidv4()">
          {{ error }}
        </li>
      </ul>
      
      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit">
        <div class="mt-3">
          <label for="project_id" class="block text-sm font-medium leading-6 text-gray-900">Proyecto</label>
          <div class='gw-form-input'>
            <Field
              name="project_id"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="project in projects" :value="project.id">
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
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="arrival_date" />
          </div>
        </div>

        <div class="mt-3">
          <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Descripción</label>
          <div class='gw-form-input'>
            <Field
              name="description"
              as='textarea'
              rows="3"
              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            />
            <ErrorMessage name="description" />
          </div>
        </div>

        <div class="mt-3">
          <div class='gw-form-input mt-2'>
            <label for="request_cash_advance" class="block text-sm font-medium leading-6 text-gray-900">
              <Field
                name="request_cash_advance"
                type="checkbox" value="true"
                :checked-value="true"
                :unchecked-value="false"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              />
              Solicitar adelanto
            </label>
            <ErrorMessage name="request_cash_advance" />
          </div>
        </div>



        <Heading class="mt-12">
          Gastos del viaje
        </Heading>

        <ButtonLink @click="openExpenseDialog">
          <i class="mdi mdi-plus"></i>
          Agregar Gasto
        </ButtonLink>

        <div v-if="form.expenses.length > 0">
          <fwb-table class="m-2 mt-0 table-auto shadow-none">
            <thead>
              <tr>
                <TableHeaderItem>Tipo</TableHeaderItem>
                <TableHeaderItem>Centro de Costo</TableHeaderItem>
                <TableHeaderItem>Monto</TableHeaderItem>
                <TableHeaderItem>Comentario</TableHeaderItem>
                <TableHeaderItem>Acciones</TableHeaderItem>
              </tr>
            </thead>
            <tbody class="m-3 divide-y-2 divide-slate-100">
              <tr v-for="expense in form.expenses" :key="expense.id"
                class="border-b border-gray-200 hover:bg-gray-100">
                <TableDataItem :value=expense.expense_kind_name />
                <TableDataItem :value=expense.amount />
                <TableDataItem :value=expense.comment />
                <td class="font-light text-right">
                  <button
                    @click="removeTravelExpense(expense.id)"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-4 rounded shadow-md m-2"
                  >
                    <span class="mdi mdi-trash-can-outline"></span>
                  </button>
                </td>

              </tr>
            </tbody>
          </fwb-table>
        </div>

        <div class="flex justify-end mt-8">
          <LinkBack :href="goToList()">Regresar</LinkBack>
          <ButtonSubmit>
            Crear solicitud
          </ButtonSubmit>
        </div>
      </Form>
    </ChandelierPage>

    <!-- Modal Add Expense -->
    <AddExpense :isOpen="expenseDialog" :expense_kinds="expense_kinds"
      @modal-close="closeExpenseDialog" @submit="handleModalData" />
  </main>
</template>
