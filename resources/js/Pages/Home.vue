<script setup>
import Layout from '../Layouts/Layout.vue';
import { Head } from '@inertiajs/vue3';

defineOptions({ layout: Layout });
defineProps({
  users: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <Head :title="`Users | ${$page.component}`" />
  
  <div>
    <table class="table">
      <thead>
        <tr class="bg-slate-300">
          <th>Avatar</th>
          <th>Name</th>
          <th>Email</th>
          <th>Registration Date</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="(user, index) in users.data" :key="index">
          <td>
            <img :src="'/storage/' + $page.props.auth.user.avatar" alt="Avatar" class="avatar" />
          </td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.updated_at }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
}
</style>