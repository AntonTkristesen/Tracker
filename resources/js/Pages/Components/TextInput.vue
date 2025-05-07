<template>
    <div class="col-3">
      <label>{{ name }}</label>
      <input :type="type" v-model="model" />
      <small>{{ message }}</small> <!-- Use passed message prop -->
    </div>
  </template>
  
  <script setup>
  const model = defineModel({ required: true })
  
  defineProps({
    name: {
      type: String,
      required: true,
    },
    type: {
      type: String,
      default: 'text',
    },
    message: {
      type: String,
      default: '', // Default to empty string if no error
    },
  })
  </script>
  