<template>
  <Head>
    <title>Nuevo Curso</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Nuevo Curso</AreaTitle>

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit" :initial-values="form" v-slot="{ values }">
        <div class="mt-3">
          <label for="category_id" class="block text-sm font-medium leading-6 text-gray-900">Categoría</label>
          <div class='gw-form-input'>
            <Field
              name="category_id"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="category in props.categories" :value="category.id">
                {{ category.name }}
              </option>
            </Field>
            <ErrorMessage name="category_id" />
          </div>
        </div>

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

        <div class="mt-3">
          <label for="course_type" class="block text-sm font-medium leading-6 text-gray-900">Tipo</label>
          <div class='gw-form-input'>
            <Field
              name="course_type"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="courseType in props.courseTypes" :value="courseType.id">
                {{ courseType.name }}
              </option>
            </Field>
            <ErrorMessage name="course_type" />
          </div>
        </div>

        <div class="mt-3">
          <label for="scope" class="block text-sm font-medium leading-6 text-gray-900">Alcance</label>
          <div class='gw-form-input'>
            <Field
              name="scope"
              as="select"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option v-for="scope in props.scopes" :value="scope.id">
                {{ scope.name }}
              </option>
            </Field>
            <ErrorMessage name="scope" />
          </div>
        </div>

        <div v-if="values.scope == 'country'">
          <Heading class="mt-12">
            Seleccione los paises donde se mostrará el curso
          </Heading>

          <fwb-table class="shadow-none">
            <thead>
            <tr>
              <TableHeaderItem>Pais</TableHeaderItem>
            </tr>
            </thead>
            <tbody class="m-3 divide-y-2 divide-slate-100">
            <fwb-table-row v-for="country in countries.data" :key="country.id">
              <td class="px-6 py-4 first:font-medium first:text-gray-900 first:dark:text-white first:whitespace-nowrap">
                <input 
                  type="checkbox" 
                  :checked="isSelectedCountry(country)"
                  @click="toogleCountry(country)" />
                <span class="ms-2 text-sm text-gray-600">{{ country.name }}</span>
              </td>
            </fwb-table-row>
            </tbody>
          </fwb-table>
          <fwb-pagination v-model="currentPageExpired" :total-items="countries.total" :per-page="5"
                          show-icons :show-labels="false"/>

          <div class="mt-8" v-show="form.selectedCountries.length > 0">
            Paises seleccionados:

            <ul>
              <li v-for="country in form.selectedCountries" :key="country.id">
                {{country.name}}
              </li>
            </ul>
          </div>
        </div>

        <div v-if="values.scope == 'project'">
          <Heading class="mt-12">
            Seleccione los proyectos donde se mostrará el curso
          </Heading>

          <fwb-table class="shadow-none">
            <thead>
            <tr>
              <TableHeaderItem>Proyecto</TableHeaderItem>
            </tr>
            </thead>
            <tbody class="">
            <fwb-table-row v-for="countryProject in countryProjects.data" :key="countryProject.id">
              <td class="px-6 py-4 first:font-medium first:text-gray-900 first:dark:text-white first:whitespace-nowrap">
                <input 
                  type="checkbox" 
                  :checked="isSelectedCountryProject(countryProject)"
                  @click="toogleCountryProject(countryProject)" />
                <span class="ms-2 text-sm text-gray-600">{{ countryProject.name }}</span>
              </td>
            </fwb-table-row>
            </tbody>
          </fwb-table>
          <fwb-pagination v-model="currentPageExpired" :total-items="countryProjects.total" :per-page="5"
                          show-icons :show-labels="false"/>

          <div class="mt-8" v-show="form.selectedCountryProjects.length > 0">
            Proyectos seleccionados:

            <ul>
              <li v-for="countryProject in form.selectedCountryProjects" :key="countryProject.id">
                {{countryProject.name}}
              </li>
            </ul>
          </div>
        </div>

        <div class="flex justify-end mt-8">
          <LinkBack :href="goToList()">Regresar</LinkBack>
          <ButtonSubmit>
            Crear curso
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
import TableHeaderItem from "@/Components/Chandelier/Common/TableHeaderItem.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';
import Heading from '@/Components/Chandelier/Common/Heading.vue';
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';

import {
  FwbA,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
  FwbPagination,
} from 'flowbite-vue';

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  countries: {
    type: Object,
    required: true,
  },
  countryProjects: {
    type: Object,
    required: true,
  },
  statuses: {
    type: Object,
    required: true,
  },
  scopes: {
    type: Object,
    required: true,
  },
  courseTypes: {
    type: Object,
    required: true,
  },
  categories: {
    type: Object,
    required: true,
  },
});

const { currentCountry } = useCountry()

const form = reactive({
  name: null,
  category_id: null,
  description: null,
  scope: null,
  course_type: null,
  selectedCountries: [],
  selectedCountryProjects: []
})

const schema = yup.object({
  name: yup.string().required().label('Nombre'),
  category_id: yup.string().required().label('Categoria'),
  description: yup.string().required().label('Descripcion'),
  course_type: yup.string().required().label('Tipo'),
  scope: yup.string().required().label('Alacnce')
});

const currentPageActive = ref(1);
const currentPageExpired = ref(1);

const toogleCountry = (country) => {
  const selectedCountries = form.selectedCountries.find(item => item.id === country.id)


  if(selectedCountries){
    form.selectedCountries = form.selectedCountries.filter(item => item.id !== country.id)
  } else{
    form.selectedCountries.push({ id: country.id, name: country.name })
  }
}

const toogleCountryProject = (countryProject) => {
  const selectedCountryProjects = form.selectedCountryProjects.find(item => item.id === countryProject.id)


  if(selectedCountryProjects){
    form.selectedCountryProjects = form.selectedCountryProjects.filter(item => item.id !== countryProject.id)
  } else{
    form.selectedCountryProjects.push({ id: countryProject.id, name: countryProject.name })
  }
}

const isSelectedCountryProject = (countryProject) => {
  const indexToUpdate = form.selectedCountryProjects.findIndex(item => item.id === countryProject.id);
  
  return indexToUpdate !== -1 ? true : false;
}

const isSelectedCountry = (country) => {
  const indexToUpdate = form.selectedCountries.findIndex(item => item.id === country.id);
  
  return indexToUpdate !== -1 ? true : false;
}

const onSubmit = (values) => {
  form.name = values.name;
  form.category_id = values.category_id;
  form.description = values.description;
  form.scope = values.scope;
  form.course_type = values.course_type;

  form.country_ids = form.selectedCountries.map(country => country.id);
  form.country_project_ids = form.selectedCountryProjects.map(countryProject => countryProject.id);

  submitCourse();
}

const submitCourse = () => {
  router.post(route('elearning.courses.store', { country: currentCountry.value }), {
    data: form,
  }, {
    preserveState: true,
  });
};

const goToList = () => {
  return route('elearning.courses.index', { country: currentCountry.value });
}
</script>
