<script setup>
// main component and imports
import { Head } from '@inertiajs/vue3'
import Layout from '@/Layouts/MainLayout.vue'
import { useCountry } from '@/Composables/useCountry';

// Common Components
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue'
import AreaTitle from '@/Components/Chandelier/Common/AreaTitle.vue'
import TravelRequest from '@/Components/Chandelier/Common/TravelRequest.vue'
import TravelRequestComments from '@/Components/Chandelier/Common/TravelRequestComments.vue'
import LinkBack from '@/Components/Chandelier/Common/LinkBack.vue';

const props = defineProps({
  travelRequest: Object,
});

defineOptions({
  layout: Layout,
});

const { currentCountry } = useCountry()

const goToList = () => {
  return route('travel-requests.index', { country: currentCountry.value });
}
</script>

<template>

  <Head>
    <title>Solicitud de viaje</title>
  </Head>

  <main>
    <ChandelierPage>
      <AreaTitle class="mb-6">Solicitud de viaje</AreaTitle>

      <TravelRequest :travelRequest="props.travelRequest" />

      <TravelRequestComments :comments="props.travelRequest.reviewerComments" />

      <div class="flex justify-end mt-8">
        <LinkBack :href="goToList()">Regresar</LinkBack>
      </div>
    </ChandelierPage>
  </main>
</template>
