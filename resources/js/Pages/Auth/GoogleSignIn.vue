<template>
  <button type="button" @click="handleClick">
    <div class="bg-blue-600 rounded-md flex flex-row p-1">
      <div class="bg-white p-1 me-1 flex rounded-sm">
        <img class="items-center justify-center" src="/images/google-logo-small.svg"/>
      </div>
      <span class="text-white m-1">
        Inicio con Google
        </span>
    </div>
  </button>
</template>

<script>
export default {
  methods: {
    handleClick() {
      this.$emit('click')
    }
  },
}
</script>
