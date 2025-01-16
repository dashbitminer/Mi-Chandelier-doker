<template>

  <div v-for="(week) in timeSheet.weeks" :key="week.weekOfYear">
    <fwb-heading tag='h6' class='mt-8 mb-4'>
      {{week.title}}
    </fwb-heading>
    <div>
      <div v-for="(project) in timeSheet.projects" :key="project.id" class="mb-4">
        <div class="flex">
          <!-- proyectos -->
          <div class="grow basis-0">
            <div class="h-24 mb-1"></div>
            <div>
              <div class="border bg-slate-200 mb-1 p-3">
                {{ project.name }}
              </div>
            </div>
          </div>

          <!-- dias de la semana -->
          <div v-for="(day) in week.days" :key="day.date" class="w-28">
            <div class="h-24 mb-1 ml-1 p-3 rounded bg-slate-200 text-center">
              {{ day.dayName }}
              <strong class="block">{{ day.day }}</strong>
            </div>
            <div v-if="findProjectTimes(project.project_id, day.times).length > 0" >
              <div class="border mb-1 ml-1 p-3 text-gray-600 text-center" :class="{ 'bg-yellow-200': time.customized }" v-for="(time) in findProjectTimes(project.project_id, day.times)" :key="time.id">
                {{ time.hours }}
                <button @click="handleOpenModal(time)" v-if="time.customized">
                  <span class="mdi mdi-information-outline"></span>
                </button>
              </div>
            </div>
            <div v-else>
              <div class="border mb-1 ml-1 p-3 text-gray-600 text-center">
                --
              </div>
            </div>
          </div>
        </div>
        <div v-if="week.totalWorkingDays > 0">
          <Heading>
            Descripcion de las tareas de las semana:
          </Heading>
          <div v-for="(comment) in findProjectWeeks(project.project_id, week.weekOfYear)" :key="comment.id">
            <em class="block text-gray-700 text-sm text-justify" v-html="comment.comment"></em>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>

import {
  FwbHeading
} from 'flowbite-vue';

import Heading from '@/Components/Chandelier/Common/Heading.vue';

const props = defineProps({
  timeSheet: {
    type: String,
    required: true,
  },
  openModal: {
    type: Function
  }
})

const handleOpenModal = (time) => {
  props.openModal(time);
}

const findProjectTimes = (projectID, times) => {
  if(!times)
    return []

  return times = times.filter((time) => time.project_id == projectID)
}

const findProjectWeeks = (projectID, weekOfYear) => {
  return props.timeSheet.comments.filter((comment) => comment.weekOfYear === weekOfYear && comment.project_id === projectID)
}
</script>
