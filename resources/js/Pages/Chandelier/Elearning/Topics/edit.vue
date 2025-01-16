<template>
  <Head>
    <title>Editar Clase</title>
  </Head>

  <main>
    <ChandelierPage class="mt-8">
      <AreaTitle>Editar clase</AreaTitle>

      <ul v-show="errors">
        <li v-for="(error, index) in errors" :key="index">
          {{ error }}
        </li>
      </ul>

      <Form class='gw-form' :validation-schema="schema" @submit="onSubmit" :initial-values="form" v-slot="{ values }">
        <div class="mt-3">
          <label for="priority" class="block text-sm font-medium leading-6 text-gray-900">No.</label>
          <div class='gw-form-input'>
            <Field
              name="priority"
              class="block w-full mt-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <ErrorMessage name="priority" />
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
          <label for="content" class="block text-sm font-medium leading-6 text-gray-900">Contenido</label>
          <ckeditor :editor="data.editor" :config="data.editorConfig"  v-model="data.editorData" />
        </div>

        <div class="mt-3">
          <div class='gw-form-input'>
            <label for="require_evaluation" class="block text-sm font-medium leading-6 text-gray-900">
              <Field name="require_evaluation"
                  type="checkbox"
                  value="true"
                  :checked-value="true"
                  :unchecked-value="false"
                  v-model="form.require_evaluation"
              />
              Completar la clase con un examen?
            </label>
            <ErrorMessage name="require_evaluation" />
          </div>
        </div>

        <div v-if="values.require_evaluation == 'true'">
          <Heading class="mt-12">
            Preguntas del examen
          </Heading>

          <ButtonLink @click="goToAddQuestion">
            <i class="mdi mdi-plus"></i>
            Agregar pregunta
          </ButtonLink>

          <fwb-accordion always-open flush>
            <fwb-accordion-panel v-for="(question) in form.questions" :key="question.id">
              <fwb-accordion-header>
                <small class='block'>Pregunta {{question.priority}}:</small>
                {{question.text}}
              </fwb-accordion-header>
              <fwb-accordion-content>
                <small>Respuestas:</small>
                <ul>
                  <li v-for="(option) in question.options" :key="option.id">
                    <i class="group-hover:text-slate-700 ms-3 text-xl mdi" :class="{ 'text-green-700 mdi-check-circle': option.is_correct == 'true', 'text-slate-700 mdi-circle-outline': option.is_correct != 'true' }" />
                    {{option.text}}
                    <small v-if="option.is_correct == 'true'" class='bg-green-400 px-1 rounded text-white'>Respuesta correcta</small>
                  </li>
                </ul>
                <ButtonPrimary @click="goToEditQuestion(question)" type='button'>
                  Editar
                </ButtonPrimary>
              </fwb-accordion-content>
            </fwb-accordion-panel>
          </fwb-accordion>
        </div>

        <div class="flex justify-end mt-8">
          <LinkBack :href="goToList()">Regresar</LinkBack>
          <ButtonSubmit>
            Guardar cambios
          </ButtonSubmit>
        </div>
      </Form>

      <EvaluationQuestion
        :question="data.question"
        :isOpen="isOpenModal"
        @close-modal="closeModal"
        @add-question="addQuestion" />

    </ChandelierPage>
  </main>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3'; // Firstly import the Link and Head components
import { Field, Form } from 'vee-validate';
import * as yup from 'yup';
import { v4 as uuidv4 } from 'uuid'
import {
  ClassicEditor,
  Bold,
  Essentials,
  Italic,
  Link,
  MediaEmbed,
  Alignment,
  List,
  Paragraph,
  Undo,
  Table,
  TableToolbar,
  Font,
  Heading as CkHeading,
  HtmlEmbed
} from 'ckeditor5';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import 'ckeditor5/ckeditor5.css';
import { useCountry } from '@/Composables/useCountry';

import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import AreaSubTitle from "@/Components/Chandelier/Common/AreaSubTitle.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
import TableHeaderItem from "@/Components/Chandelier/Common/TableHeaderItem.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import ErrorMessage from '@/Components/Chandelier/Common/ErrorMessage.vue';
import EvaluationQuestion from "@/Pages/Chandelier/Elearning/Topics/partials/EvaluationQuestion.vue";
import ButtonSubmit from '@/Components/Chandelier/Common/ButtonSubmit.vue';
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';
import Heading from '@/Components/Chandelier/Common/Heading.vue';
import ButtonLink from '@/Components/Chandelier/Common/ButtonLink.vue';
import LinkToPrimary from "@/Components/Chandelier/Common/LinkToPrimary.vue";
import ButtonPrimary from '@/Components/Chandelier/Common/ButtonPrimary.vue';

import {
  FwbA,
  FwbTable,
  FwbTableBody,
  FwbTableCell,
  FwbTableHead,
  FwbTableRow,
  FwbPagination,
  FwbAccordion,
  FwbAccordionContent,
  FwbAccordionHeader,
  FwbAccordionPanel,
} from 'flowbite-vue';

defineOptions({
  layout: MainLayout,
});

const props = defineProps({
  course: {
    type: Object,
    required: true,
  },
  topic: {
    type: Object,
    required: true,
  },
  questions: {
    type: Object,
    required: false,
  },
  errors: Object
});

const { currentCountry } = useCountry();

const form = reactive({
  name: props.topic.name,
  priority: props.topic.priority,
  description: props.topic.description,
  require_evaluation: props.topic.require_evaluation,
  questions: props.questions
})

const data = reactive({
  question: {},
  editor: ClassicEditor,
  editorConfig: {
    plugins: [
      Bold,
      Essentials,
      Italic,
      Link,
      MediaEmbed,
      List,
      Alignment,
      Paragraph,
      Undo,
      Table,
      TableToolbar,
      Font,
      CkHeading,
      HtmlEmbed
    ],
    toolbar: [
      'undo',
      'redo',
      '|',
      'heading',
      'bold',
      'italic',
      'fontSize',
      'fontFamily',
      'fontColor',
      'fontBackgroundColor',
      '|',
      'alignment',
      'numberedList',
      'bulletedList',
      '|',
      'link',
      'mediaEmbed',
      'insertTable',
      '|',
      'htmlEmbed' ],
    table: {
        contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
    },
    mediaEmbed: {
      previewsInData: true
    },
  },
  editorData: props.topic.content
})

const schema = yup.object({
  priority: yup.number().required().label('No.'),
  name: yup.string().required().label('Nombre'),
  description: yup.string().required().label('Descripcion'),
});

const currentPageActive = ref(1);
const currentPageExpired = ref(1);

const onSubmit = (values) => {
  form.priority = values.priority;
  form.name = values.name;
  form.description = values.description;
  form.require_evaluation = values.require_evaluation == 'true' ? true : false;
  form.content = data.editorData;

  submitTopic();
}

const submitTopic = () => {
  const formData = {
    priority: form.priority,
    name: form.name,
    description: form.description,
    content: form.content,
    require_evaluation: form.require_evaluation,
    questions: form.questions.map(question => {
      return {
        id: question.id,
        text: question.text,
        priority: question.priority,
        is_new: question.is_new == 'true' ? true : false,
        options: question.options.map(option => {
          return {
            id: option.id,
            text: option.text,
            priority: option.priority,
            is_new: option.is_new == 'true' ? true : false,
            is_correct: option.is_correct == 'true' ? true : false,
          }
        })
      }
    })
  };

  router.put(route('elearning.courses.topics.update', { course: props.course.id, topic: props.topic.id, country: currentCountry.value }), {
    data: formData,
  }, {
    preserveState: true,
  });
};

const goToEditQuestion = (question) => {
  data.question = question;
  openModal();
}

const goToAddQuestion = () => {
  data.question = {
    id: uuidv4(),
    text: null,
    is_new: 'true',
    options: [{
      id: uuidv4(),
      text: '',
      priority: '',
      is_correct: 'false',
      is_new: 'true'
    },
    {
      id: uuidv4(),
      text: '',
      priority: '',
      is_correct: 'false',
      is_new: 'true'
    }]
  }

  openModal();
}

const isOpenModal = ref(false)

const openModal = () => {
  isOpenModal.value = true
}

const closeModal = () => {
  isOpenModal.value = false
}

const addQuestion = (values) => {
  const existingQuestion = form.questions.find(question => question.id == values.id);

  if(existingQuestion){
    form.questions = form.questions.map(question => {
      if(question.id == existingQuestion.id)
        return values

      return question
    });
  } else{
    form.questions.push(values)
  }
}

const goToList = () => {
  return route('elearning.courses.topics.index', { course: props.course.id, country: currentCountry.value });
}
</script>
