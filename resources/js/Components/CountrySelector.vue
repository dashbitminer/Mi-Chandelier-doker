<script setup>
import { onMounted } from 'vue'
import { useCountry } from '@/Composables/useCountry'

const { currentCountry, countries, fetchCountries, changeCountry, isLoading} = useCountry()

onMounted(() => {
  fetchCountries()
})
</script>

<template>
    {{}}
    <select
        v-if="Object.keys(countries).length > 0"
        :value="currentCountry"
        @change="changeCountry($event.target.value)"
        class="m-3 me-4 bg-slate-100 rounded-xl"
    >
        <option
            v-for="(name, code) in countries"
            :key="code"
            :value="code"
        >
            {{ name }}
        </option>
    </select>
</template>
