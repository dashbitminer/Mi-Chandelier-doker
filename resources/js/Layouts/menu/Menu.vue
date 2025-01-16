<script setup>
import { router, usePage } from '@inertiajs/vue3';
import TopActions from "@/Components/Chandelier/Common/TopActions.vue"
defineOptions({
  name: "Menu",
})
// const props = defineProps({
//   navigationPosition: {
//     type: String,
//     required: true
//   }
// })

const page = usePage();
const position = page.component.split("Chandelier/")[1];
const path = position.split("/").join(" > ");
</script>

<template>
  <div class="flex flex-wrap m-4 mt-4">
    <!-- <span class="grow font-extra-light text-lg text-gray-400">{{ navigationPosition }}</span> -->
    <!-- <span class="grow mt-4 font-extra-light text-lg text-gray-400">Menu Principal > Página > Ubicación actual</span> -->
    <span class="grow mt-4 font-extra-light text-lg text-gray-400">Menu Principal > {{ path }}</span>
    <TopActions />
  </div>
</template>
<!-- <script>
export default {
    name: "Menu",
    props: {
        navigationPosition: {
            type: String,
            required: true
        }
    }
}
</script> -->
