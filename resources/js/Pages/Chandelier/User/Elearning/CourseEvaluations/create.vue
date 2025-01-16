<template>
  <Head>
    <title>CourseEvaluation</title>
  </Head>

  <ChandelierPage>
    <AreaTitle>
      Examen "{{course.name}} - {{topic.name}}"
    </AreaTitle>

    <fwb-alert type="danger" class='mt-8' v-if="data.evaluationIsNotStarted">
      Debe responder todas las preguntas del examen.
    </fwb-alert>

    <fwb-alert type="danger" class='mt-8' v-if="data.evaluationIsIncomplete">
      Hay preguntas pendientes de responder.
    </fwb-alert>

    <Form :validation-schema="schema" @submit="onSubmit" v-slot="{ values }">
      <div class="mt-3" v-for="question in questions" :key="question.id">
        <label for="question.id" class="block text-sm font-medium leading-6 text-gray-900">{{question.priority}}. {{question.text}}</label>
        <div class='gw-form-input'>
          <div v-for="option in question.options" :key="option.id">
            <Field
              :name="`${question.id}`"
              type="radio"
              :value="option.id"
              class=""
            >
            </Field>
            {{option.text}}
          </div>
        </div>
      </div>

      <div class="flex justify-end mt-8">
        <LinkBack :href="goToTopic()">Regresar a la clase</LinkBack>
        <ButtonSubmit>
          Enviar respuestas
        </ButtonSubmit>
      </div>

    </Form>
  </ChandelierPage>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3';

import { Field, Form } from 'vee-validate';
import {
  FwbAlert
} from 'flowbite-vue';

import MainLayout from "@/Layouts/MainLayout.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
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
  questions: {
    type: Object,
    required: true,
  }
});

const { currentCountry } = useCountry()

const data = reactive({
  evaluationIsNotStarted: false,
  evaluationIsIncomplete: false
})

const form = reactive({
  questions: {}
})

const onSubmit = (values) => {
  const filledQuestions = Object.keys(values).filter(key => {
    const response = values[key];
    return response !== '' && response !== null && response !== undefined;
  });

  data.evaluationIsNotStarted = false;
  data.evaluationIsIncomplete = false;

  if(filledQuestions.length == 0){
    data.evaluationIsNotStarted = true;
    return;
  }

  if(filledQuestions.length < props.questions.length){
    data.evaluationIsIncomplete = true;
    return
  }

  form.questions = values;

  submitEvaluation();
}

const submitEvaluation = () => {
  router.post(route('user.elearning.evaluations.create', { course: props.course.id, topic: props.topic.id, country: currentCountry.value }), {
    data: form,
  }, {
    preserveState: true,
  });
};

const goToTopic = () => {
  return route('user.elearning.topics.show', { course: props.course.id, topic: props.topic.id, country: currentCountry.value });
}
</script>
