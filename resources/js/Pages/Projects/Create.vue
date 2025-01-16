<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Form from './Partials/Form.vue';


defineProps({
    team: Object,
    availableRoles: Array,
    permissions: Object,
    projects: Object,
});
</script>
<template>
  <AppLayout :team="team">
    <div class="container mx-auto">
      <Form :team="team" :availableRoles="availableRoles" :permissions="permissions" />
    </div>
  </AppLayout>
</template>