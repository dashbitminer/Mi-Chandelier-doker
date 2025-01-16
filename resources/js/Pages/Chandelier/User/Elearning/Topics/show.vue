<template>
  <Head>
    <title>Clases</title>
  </Head>

  <ChandelierPage>
    <AreaTitle>
      Formaciones - {{ course.name }}
    </AreaTitle>

    <AreaTitle>
      {{ topic.name }}
    </AreaTitle>

    <p class="mb-4">{{ topic.description }}</p>


    <section v-html="topic.content"></section>

    <fwb-alert type="warning" class='mt-8' v-if="showEvaluationLink()">
      <p>Para completar esta clase, debes realizar un examen que pondrá a prueba tu conocimiento del tema.</p>
      <div class="flex justify-start mt-2">
        <LinkTo :href="goToEvaluation()">
          Realizar evaluacion
        </LinkTo>
      </div>
    </fwb-alert>

    <div class="flex justify-end mt-8">
      <LinkBack :href="goToTopics()">Regresar al curso</LinkBack>
    </div>
  </ChandelierPage>
</template>

<script setup>
import { ref } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3';
import { useCountry } from '@/Composables/useCountry';

import {
  FwbAlert
} from 'flowbite-vue';

import MainLayout from "@/Layouts/MainLayout.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import LinkTo from '@/Components/Chandelier/Common/LinkTo.vue';
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  course: {
    type: Object,
    required: true
  },
  userCourseTopic: {
    type: Object,
    required: true
  },
  topic: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry()

const goToTopics = () => {
  return route('user.elearning.topics.index', { course: props.course.id, country: currentCountry.value });
}

const goToEvaluation = () => {
  return route('user.elearning.evaluations.store', { course: props.course.id, topic: props.topic.id, country: currentCountry.value });
}

const showEvaluationLink = () => {
  return props.userCourseTopic.status != 'completed' && props.topic.require_evaluation == 'true'
}
</script>
