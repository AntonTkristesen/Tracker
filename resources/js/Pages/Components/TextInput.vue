<template>
  <div class="position-relative">
    <i v-if="icon" :class="['pi', icon, 'position-absolute', 'text-muted']" style="top: 50%; left: 12px; transform: translateY(-50%); z-index: 2;"></i>
    <InputText
      :type="type"
      v-model="model"
      :placeholder="placeholder"
      :name="name"
      class="form-control ps-5"
      :class="{ 'is-invalid': message }"
    />
    <small v-if="message" class="text-danger">{{ message }}</small>
  </div>
</template>

<script setup>
const model = defineModel({ required: true });

defineProps({
  name: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
  },
  message: {
    type: String,
    default: '',
  },
  icon: {
    type: String,
    default: '', // e.g. 'pi-user'
  },
});
</script>

<style scoped>
/* Optional: fine-tune appearance */
</style>
