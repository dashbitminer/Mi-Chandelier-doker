<script setup>
// main component and imports
import { Field, Form } from 'vee-validate';
import * as yup from 'yup';import { ref, reactive } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import axios from 'axios';
import Layout from '@/Layouts/MainLayout.vue'
import { useCountry } from '@/Composables/useCountry';
import {
  FwbHeading,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
} from 'flowbite-vue';

// Common Components
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue'
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import EditTimeModal from "@/Pages/Chandelier/TimeSheets/EditTimeModal.vue";
import EditCommentModal from "@/Pages/Chandelier/TimeSheets/EditCommentModal.vue";
import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import Heading from '@/Components/Chandelier/Common/Heading.vue';
import TableHeaderItem from '@/Components/Chandelier/Common/TableHeaderItem.vue';

const props = defineProps({
  timeSheet: Object,
  absenceTypes: Object,
});

defineOptions({
  layout: Layout,
});

const { currentCountry } = useCountry()

const form = reactive({
  comments: props.timeSheet.comments,
  user_acceptance: props.timeSheet.user_acceptance,
  times: [],
  errors: []
});

let projectTimeForm = {}

const schema = yup.object({
  user_acceptance: yup.boolean().oneOf([true])
});

const onSubmit = (values) => {
  form.user_acceptance = values.user_acceptance ? true : false

  submitTimeSheet();
}
const submitTimeSheet = () => {
  const requiredComments = form.comments.filter(item => isCommentRequired(item)).length;
  if(requiredComments > 0){
    alert('Debe definir las tareas por semana de cada proyecto');
    return;
  }
  
  axios.put(route('time-sheets.update', { time_sheet: props.timeSheet.id, country: currentCountry.value }), { data: form })
    .then((response) => {
      router.visit(route('time-sheets.index', { country: currentCountry.value }));
    })
    .catch((error) => {
      console.log('error!!!')
      if(error.response){
        let errorBag = [];
        
        Object.values(error.response.data.errors).forEach(msg => {
          if(Array.isArray(msg))
            errorBag.push(msg[0]);
          else
            errorBag.push(msg);
        });

        form.errors = errorBag;
      } else if (error.request){
        form.errors = ['Ha ocurrido un error, intentelo nuevamente.'];
      } else{
        form.errors = ['Ha ocurrido un error, intentelo nuevamente.'];
      }
      
      errorsRef.value?.scrollIntoView({ behavior: 'smooth' })
    });
}

const selectedTime = ref({});
const selectedProject = ref({});
const selectedComment = ref({});
const selectedWeek = ref({});
const isTimeModalOpen = ref(false);
const isCommentModalOpen = ref(false);
const errorsRef = ref(null);
const openTimeModal = (key) => {
  const time = props.timeSheet.times.find((item) => item.id === key);

  time.accumulatedHours = props.timeSheet.times.filter((item) => item.date === time.date && item.id != time.id)
                                                .map((item) => item.hours)
                                                .reduce((total, hours) => total + hours, 0);

  const project =  props.timeSheet.projects.find((item) => { return item.project_id == time.project_id });

  project.remainingHours = remainingProjectHours(project);
  project.totalHours = totalProjectHours(project);

  selectedProject.value = project;
  selectedTime.value = time;
  isTimeModalOpen.value = true;
};

const openCommentModal = (comment, week) => {
  const project =  props.timeSheet.projects.find((item) => { return item.project_id == comment.project_id });

  selectedProject.value = project;
  selectedComment.value = comment;
  selectedWeek.value = week;
  isCommentModalOpen.value = true;
};

const updateTimes = (data) => {
  const time = props.timeSheet.times.find((item) => item.id === data.id);
  const project = props.timeSheet.projects.find((item) => item.project_id === data.project_id);
  const indexToUpdate = form.times.findIndex(time => time.id === data.id);
  
  time.hours = data.hours
  time.absence_type_id = data.absence_type_id

  if(indexToUpdate !== -1){
    form.times[indexToUpdate] = data;
  } else{
    form.times.push(data);
  }
  
  const totalHours = form.times.filter((item) => item.project_id === data.project_id).map((item) => parseFloat(item.hours)).reduce((sum, hours) => sum + hours, 0);

  project.hours = totalHours;
};

const updateComments = (data) => {
  const comment = form.comments.find((item) => item.id === data.id);
  comment.comment = data.comment;
}

const goToList = () => {
  return route('time-sheets.index', { country: currentCountry.value });
}

const findProjectTimes = (projectID, times) => {
  if(!times)
    return []

  return times = props.timeSheet.times.filter((time) => times.includes(time.id) && time.project_id === projectID)
}

const findProjectWeeks = (projectID, weekOfYear) => {
  return props.timeSheet.comments.filter((comment) => comment.weekOfYear === weekOfYear && comment.project_id === projectID)
}

const remainingProjectHours = (project) => {
  const hours = parseFloat(project.budgetHours) - (parseFloat(project.accumulatedHours) + parseFloat(project.hours));
  
  return hours > 0 ? hours : 0;
}

const totalProjectHours = (project) => {
  let label = parseFloat(project.accumulatedHours) + parseFloat(project.hours)
  
  if(isExceedHours(project))
    label += ' / ' + project.budgetHours
  
  return label;
}

const isExceedHours = (project) => {
  return (parseFloat(project.accumulatedHours) + parseFloat(project.hours)) > parseFloat(project.budgetHours);
}

const isCommentRequired = (comment) => {
  return comment.comment == null;
}
</script>

<template>

  <Head>
    <title>Editar hoja de tiempo</title>
  </Head>

  <main>
    <ChandelierPage>
      <AreaTitle>Editar hoja de tiempo</AreaTitle>
      <h2 class="w-full text-lg font-semibold leading-7 text-gray-700 my-4">{{timeSheet.title}}</h2>

      <ul ref="errorsRef" v-show="form.errors">
        <li v-for="(error, index) in form.errors" :key="index">
          {{ error }}
        </li>
      </ul>
      
      <fwb-table class="shadow-none">
        <thead>
        <tr>
          <TableHeaderItem>Proyecto</TableHeaderItem>
          <TableHeaderItem>Horas pendientes para asignar</TableHeaderItem>
          <TableHeaderItem>Horas asignadas</TableHeaderItem>
        </tr>
        </thead>
        <tbody class="m-3 divide-y-2 divide-slate-100">
        <fwb-table-row v-for="project in timeSheet.projects" :key="project.id">
          <fwb-table-cell>{{ project.name }}</fwb-table-cell>
          <fwb-table-cell>{{ remainingProjectHours(project) }}</fwb-table-cell>
          <fwb-table-cell>{{ totalProjectHours(project) }}</fwb-table-cell>
        </fwb-table-row>
        </tbody>
      </fwb-table>
      
      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit" :initial-values="form">
        <!-- semanas -->
        <div v-for="(week) in timeSheet.weeks" :key="week.weekOfYear">
          <fwb-heading tag='h6' class='mt-8 mb-4'>
            {{week.title}}
          </fwb-heading>
          <div>
            <div class="mb-4">
              <div class="flex">
                <!-- proyectos -->
                <div class="grow basis-0">
                  <div class="h-24 mb-1"></div>
                  <div>
                    <div v-for="(project) in timeSheet.projects" :key="project.id" class="border bg-slate-200 mb-1 p-3">
                      {{ project.name }}
                    </div>
                  </div>
                </div>

                <!-- dias de la semana -->
                <div v-for="(day) in week.days" :key="day.date" class="w-24">
                  <div class="h-24 mb-1 ml-1 p-3 rounded bg-slate-200 text-center">
                    {{ day.dayName }}
                    <strong class="block">{{ day.day }}</strong>
                  </div>
                  <div v-for="(project) in timeSheet.projects" :key="project.id">
                    <div v-if="findProjectTimes(project.project_id, day.times).length > 0">
                      <div class="border mb-1 ml-1 p-3 text-gray-600 text-center cursor-pointer" :class="{ 'bg-yellow-200': time.customized }" v-for="(time) in findProjectTimes(project.project_id, day.times)" :key="time.id" @click="openTimeModal(time.id)">
                        {{ time.hours }}
                      </div>
                    </div>
                    <div v-else>
                      <div class="border mb-1 ml-1 p-3">
                        --
                      </div>
                    </div>
                  </div>
                </div>
                <!-- dias de la semana (fin) -->
                
                <!-- tareas -->
                <div class="w-24">
                  <div class="h-24 mb-1 ml-1 p-3 rounded bg-slate-200 text-center">
                    Tareas
                  </div>
                  <div v-for="(project) in timeSheet.projects" :key="project.id">
                    <div v-if="week.totalWorkingDays > 0">
                      <div v-for="(comment) in findProjectWeeks(project.project_id, week.weekOfYear)" :key="comment.id" class="border mb-1 ml-1 p-3 text-gray-600 text-center cursor-pointer" :class="{ 'border-red-400': isCommentRequired(comment) }" @click="openCommentModal(comment, week)">
                        <i class="mdi mdi-check-bold text-green-500" v-if="!isCommentRequired(comment)"></i>
                        <i class="mdi mdi-alert-circle-outline text-red-500" v-if="isCommentRequired(comment)"></i>
                      </div>
                    </div>
                    <div v-else>
                      <div class="border mb-1 ml-1 p-3">
                        --
                      </div>
                    </div>
                  </div>
                </div>
                <!-- tareas (fin) -->
              </div>
            </div>
          </div>
        </div>

        <div class="my-5">
          <div class='gw-form-input'>
            <label for="user_acceptance" class="block text-sm font-medium leading-6 text-gray-900">
              <Field
                name="user_acceptance"
                type="checkbox"
                value="true"
                :checked-value="true"
                :unchecked-value="false"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
              />
              Certifico que la información en este formulario representa las tareas realizadas
            </label>
            <ErrorMessage name="user_acceptance" />
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

  <EditTimeModal
    v-if="isTimeModalOpen"
    :time="selectedTime"
    :project="selectedProject"
    :absenceTypes="absenceTypes"
    @close="isTimeModalOpen = false"
    @update-data="updateTimes"
  />

  <EditCommentModal
    v-if="isCommentModalOpen"
    :comment="selectedComment"
    :project="selectedProject"
    :week="selectedWeek"
    @close="isCommentModalOpen = false"
    @update-data="updateComments"
  />
</template>
