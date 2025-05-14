<template>
  <div class="p-d-flex p-jc-center p-mt-6">
    <Card style="width: 360px">
      <template #title>
        <div class="p-d-flex p-ai-center p-flex-column">
          <Avatar
            :image="'/storage/' + page.props.auth.user.avatar"
            size="xlarge"
            shape="circle"
          />
          <h3 class="p-mt-3">{{ page.props.auth.user.name }}</h3>
        </div>
      </template>

      <template #content>
        <!-- View Mode -->
        <div v-if="!isEditing" class="p-mt-3">
          <p><strong>Email:</strong> {{ page.props.auth.user.email }}</p>
        </div>

        <!-- Edit Mode -->
        <div v-else class="p-mt-3">
          <div class="p-field p-mb-3">
            <TextInput name="name" v-model="form.name" :message="form.errors.name"/>  

            <TextInput name="email" v-model="form.email" type="email" :message="form.errors.email"/>

            <div class="p-field">
              <label for="avatar" class="p-text-secondary">Change Avatar</label>
              <input
                type="file"
                id="avatar"
                name="avatar"
                @change="(e) => form.avatar = e.target.files[0]"
              />
              <p v-if="form.errors.avatar" class="p-text-danger">{{ form.errors.avatar }}</p>
            </div>
          </div>
        </div>
      </template>

      <template #footer>
        <div class="p-d-flex p-jc-end">
          <!-- View Mode Button -->
          <Button 
            v-if="!isEditing"
            label="Edit Profile" 
            icon="pi pi-user-edit" 
            class="p-button-sm p-button-primary"
            @click="toggleEdit"
          />

          <!-- Edit Mode Buttons -->
          <div v-else class="p-d-flex p-jc-between w-full">
            <Button 
              label="Cancel" 
              icon="pi pi-times" 
              class="p-button-sm p-button-secondary"
              @click="cancelEdit"
            />
            <Button 
              label="Save" 
              icon="pi pi-check" 
              class="p-button-sm p-button-success"
              @click="saveProfile"
            />
          </div>
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import TextInput from './Components/TextInput.vue';

const page = usePage();


const isEditing = ref(false);


const profile = reactive({
  name: page.props.auth.user.name,
  email: page.props.auth.user.email,
});

const form = useForm({
  name: profile.name,
  email: profile.email,
  avatar: null,
});

const toggleEdit = () => {
  isEditing.value = true;
};

const cancelEdit = () => {
  isEditing.value = false;
};

// const changeAvatar = (e) => {
//   form.avatar = e.target.files[0];
// };

// Save profile changes
const saveProfile = () => {
  console.log(form.name);
  form.post(route('dashboard.update'), {
  onSuccess: () => {
    isEditing.value = false;
  },
});
};
</script>
  
  <style scoped>
  .text-secondary {
    color: #6c757d;
  }
  
  .w-full {
    width: 100%;
  }
  
  .p-field {
    margin-bottom: 1rem;
  }
  
  .p-field label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
  }
  
  .p-mb-3 {
    margin-bottom: 1rem;
  }
  
  .p-d-flex {
    display: flex;
  }
  
  .p-jc-between {
    justify-content: space-between;
  }
  </style>