<template>
  <Head title="Calendar" />
  <div class="container-fluid py-5 bg-light">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="card-header bg-gradient-primary text-white py-3 px-4">
            <h5 class="mb-0"><i class="pi pi-calendar me-2"></i>Calendar</h5>
          </div>
          <div class="card-body p-4">
            <Calendar v-model="selected" inline class="w-100" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Layout from '../Layouts/Layout.vue'

defineOptions({ layout: Layout })

const selected = ref(new Date())
</script>

<style scoped>
.bg-gradient-primary {
  background: linear-gradient(135deg, #4361ee, #3f37c9);
}
.rounded-4 {
  border-radius: 1rem !important;
}
</style>
