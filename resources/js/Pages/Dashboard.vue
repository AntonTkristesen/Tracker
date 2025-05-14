<template>
    <div class="p-d-flex p-jc-center p-mt-6">
      <Card style="width: 360px">
        <template #title>
          <div class="p-d-flex p-ai-center p-flex-column">
            <Avatar
              :image="'/storage/' + $page.props.auth.user.avatar"
              size="xlarge"
              shape="circle"
            />
            <h3 class="p-mt-3">{{ $page.props.auth.user.name }}</h3>
          </div>
        </template>
  
        <template #content>
          <!-- View Mode -->
          <div v-if="!isEditing" class="p-mt-3">
            <p><strong>Email:</strong> {{ $page.props.auth.user.email }}</p>
            <p><strong>Location:</strong> {{ profile.location || 'Not specified' }}</p>
            <p><strong>About:</strong> {{ profile.about || 'No information provided' }}</p>
          </div>
          
          <!-- Edit Mode -->
          <div v-else class="p-mt-3">
            <div class="p-field p-mb-3">
              <label for="location">Location</label>
              <InputText 
                id="location" 
                v-model="profile.location" 
                class="p-inputtext w-full"
              />
            </div>
            <div class="p-field">
              <label for="about">About</label>
              <Textarea 
                id="about" 
                v-model="profile.about" 
                rows="3" 
                class="p-inputtext w-full"
              />
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
  import { useForm } from '@inertiajs/vue3';
  
  // Edit mode state
  const isEditing = ref(false);
  
  // Profile data (you can initialize with your user data)
  const profile = reactive({
    location: '',
    about: '',
  });
  
  // For Inertia form submission
  const form = useForm({
    location: '',
    about: '',
  });
  
  // Toggle edit mode
  const toggleEdit = () => {
    isEditing.value = true;
  };
  
  // Cancel edit mode
  const cancelEdit = () => {
    isEditing.value = false;
    // Reset any unsaved changes
    // You may want to reload from the server or restore from a backup
  };
  
  // Save profile changes
  const saveProfile = () => {
    // Copy data from profile to form
    form.location = profile.location;
    form.about = profile.about;
    
    // Submit to your backend route
    form.put(route('profile.update'), {
      onSuccess: () => {
        // Exit edit mode after successful update
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