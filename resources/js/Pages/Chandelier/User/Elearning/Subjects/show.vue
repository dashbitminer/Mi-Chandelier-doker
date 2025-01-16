<template>
  <Head>
    <title>Temas</title>
  </Head>

  <ChandelierPage>
    <AreaTitle>
      Centro de ayuda - {{ course.name }}
    </AreaTitle>

    <AreaTitle>
      {{ topic.name }}
    </AreaTitle>

    <p class="mb-4">{{ topic.description }}</p>

    <section v-html="topic.content"></section>

    <div class="flex justify-end mt-8">
      <LinkBack :href="goToTopics()">Regresar al temario</LinkBack>
    </div>
  </ChandelierPage>
</template>

<script setup>
import { Link, Head, router } from '@inertiajs/vue3';

import MainLayout from "@/Layouts/MainLayout.vue";
import ChandelierPage from "@/Components/Chandelier/Common/Page.vue";
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
  topic: {
    type: Object,
    required: true,
  }
});

const goToTopics = () => {
  return route('user.elearning.subjects.index', { help_center: props.course.id });
}
</script>
