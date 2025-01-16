<template>
  {{formatDate(date)}}
</template>

<script setup>
import { format, parseISO } from 'date-fns';

const props = defineProps({
  date: {
    type: String,
    required: true,
  }
})

const formatDate = (date) => {

    if(!isValidDate(date)) return '--'

    try {
      const parsedDate = parseISO(date);
      
      return format(parsedDate, 'dd/MMM/yyyy');
    } catch{
      return '--'
    }
}


const isValidDate = (date) => {
    const strictDate = new Date(date);
    return strictDate instanceof Date && !isNaN(strictDate);
}
</script>