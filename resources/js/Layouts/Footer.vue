<template>
  <footer class="py-5">
    <div class="container text-slate-400 text-xs">
      <div class="row mx-auto mt-1 col-8">
        {{ new Date().getFullYear() }}, Chandelier v0.3
      </div>
      <div class="row mx-auto mt-1 col-8">
        One small act can achieve great things
      </div>
      <div class="row mx-auto mt-1 col-8">
        <a href="https://glasswing.org">
          Glasswing International
        </a>
      </div>
    </div>
  </footer>
</template>

<!-- <script>
export default {
  name: "AppFooter",
};
</script> -->
<script setup>
defineOptions({
  name: "AppFooter",
})
</script>
