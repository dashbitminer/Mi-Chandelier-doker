<template>
  <Head>
    <title>CourseEvaluation</title>
  </Head>

  <ChandelierPage>
    <AreaTitle>
      Examen "{{course.name}} - {{topic.name}}"
    </AreaTitle>

    <div v-if="userCourseTopic.evaluationStatus == 'approved'">
      {{userCourseTopic.evaluationStatusLabel}}
      <p>
        ¡Felicidades por aprobar el examen!
        <br />
        Tu esfuerzo y dedicación han dado sus frutos.
        <br />
        ¡Sigue asi!
      </p>

      <div class="flex justify-end mt-8">
        <LinkBack :href="goToTopics()">Continuar con el curso</LinkBack>
      </div>
    </div>

    <div v-if="userCourseTopic.evaluationStatus == 'failed'">
      {{userCourseTopic.evaluationStatusLabel}}
      <p>
        Lamentablemente no has aprobado el examen, pero no te desanimes.
        <br />
        Cada intento es una oportunidad de aprendizaje.
        <br />
        ¡Te animo a que lo intentes de nuevo!
      </p>
      <p>
        Estoy seguro que lo lograrás.
        <br />
        ¡Animo!
      </p>

      <div class="flex justify-end mt-8">
        <LinkBack :href="goToTopic()">Regresar a la clase</LinkBack>
      </div>
    </div>

  </ChandelierPage>
</template>

<script setup>
import { ref } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3';

import MainLayout from "@/Layouts/MainLayout.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import { useCountry } from '@/Composables/useCountry';

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  course: {
    type: Object,
    required: true
  },
  topic: {
    type: Object,
    required: true,
  },
  userCourseTopic: {
    type: Object,
    required: true,
  },
});

const { currentCountry } = useCountry()

const goToTopic = () => {
  return route('user.elearning.topics.show', { course: props.course.id, topic: props.topic.id, country: currentCountry.value });
}

const goToTopics = () => {
  return route('user.elearning.topics.index', { course: props.course.id,  country: currentCountry.value });
}
</script>
