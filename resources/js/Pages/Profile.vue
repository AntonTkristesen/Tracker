<template>
    <div class="container-fluid py-5 bg-light">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 col-xl-4">
          <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-header bg-gradient-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
              <h5 class="mb-0"><i class="pi pi-user me-2"></i>My Profile</h5>
              <span class="badge bg-light text-primary">{{ page.props.auth.user.name }}</span>
            </div>
            
            <div class="card-body p-4">
              <div class="text-center mb-4">
                <div class="position-relative d-inline-block">
                  <Avatar
                    :image="'/storage/' + page.props.auth.user.avatar"
                    size="xlarge"
                    shape="circle"
                    class="shadow avatar-xl border-3 border-white"
                  />
                  <div v-if="isEditing" class="avatar-overlay">
                    <label for="avatar" class="avatar-edit-btn">
                      <i class="pi pi-camera"></i>
                    </label>
                    <input
                      type="file"
                      id="avatar"
                      name="avatar"
                      class="d-none"
                      @change="(e) => form.avatar = e.target.files[0]"
                    />
                  </div>
                </div>
                <h3 class="fw-bold text-primary mt-3">{{ page.props.auth.user.name }}</h3>
                <p class="text-muted mb-0" v-if="!isEditing">{{ page.props.auth.user.email }}</p>
              </div>
  
              <!-- Profile Details -->
              <div v-if="!isEditing" class="profile-info p-3 bg-light rounded-3 mb-4">
                <div class="d-flex align-items-center mb-3">
                  <div class="profile-icon me-3">
                    <i class="pi pi-envelope text-primary fs-4"></i>
                  </div>
                  <div>
                    <div class="text-muted small">Email Address</div>
                    <div class="fw-medium">{{ page.props.auth.user.email }}</div>
                  </div>
                </div>
                
                <!-- You can add more profile details here as needed -->
                <div class="d-flex align-items-center">
                  <div class="profile-icon me-3">
                    <i class="pi pi-calendar text-primary fs-4"></i>
                  </div>
                  <div>
                    <div class="text-muted small">Member Since</div>
                    <div class="fw-medium">{{ formatDate(page.props.auth.user.created_at || new Date()) }}</div>
                  </div>
                </div>
              </div>
  
              <!-- Edit Form -->
              <div v-if="isEditing" class="mt-4">
                <div class="mb-3">
                  <label class="form-label fw-bold text-primary">
                    <i class="pi pi-user me-2"></i>Name
                  </label>
                  <div class="input-group mb-3">
                    <span class="input-group-text bg-light"><i class="pi pi-id-card"></i></span>
                    <input 
                      type="text"
                      class="form-control form-control-lg border-start-0" 
                      v-model="form.name"
                      placeholder="Your name"
                    />
                  </div>
                  <div v-if="form.errors.name" class="text-danger small mt-1">{{ form.errors.name }}</div>
                </div>
                
                <div class="mb-3">
                  <label class="form-label fw-bold text-primary">
                    <i class="pi pi-envelope me-2"></i>Email
                  </label>
                  <div class="input-group mb-3">
                    <span class="input-group-text bg-light"><i class="pi pi-at"></i></span>
                    <input 
                      type="email" 
                      class="form-control form-control-lg border-start-0" 
                      v-model="form.email"
                      placeholder="Your email"
                    />
                  </div>
                  <div v-if="form.errors.email" class="text-danger small mt-1">{{ form.errors.email }}</div>
                </div>
                
                <div v-if="form.errors.avatar" class="alert alert-danger py-2" role="alert">
                  <i class="pi pi-exclamation-circle me-2"></i>{{ form.errors.avatar }}
                </div>
              </div>
  
              <!-- Action Buttons -->
              <div class="mt-4">
                <div v-if="!isEditing" class="d-grid">
                  <button @click="toggleEdit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm">
                    <i class="pi pi-user-edit me-2"></i>Edit Profile
                  </button>
                </div>
                
                <div v-else class="d-flex gap-3">
                  <button @click="cancelEdit" class="btn btn-light flex-fill py-3 fw-bold">
                    <i class="pi pi-times me-2"></i>Cancel
                  </button>
                  <button @click="saveProfile" class="btn btn-success flex-fill py-3 fw-bold shadow-sm">
                    <i class="pi pi-check-circle me-2"></i>Save Changes
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import { useForm, usePage } from '@inertiajs/vue3';
  
  const page = usePage();
  const isEditing = ref(false);
  
  const form = useForm({
    name: page.props.auth.user.name,
    email: page.props.auth.user.email,
    avatar: null,
  });
  
  const toggleEdit = () => {
    isEditing.value = true;
  };
  
  const cancelEdit = () => {
    isEditing.value = false;
    // Reset form values to original values
    form.name = page.props.auth.user.name;
    form.email = page.props.auth.user.email;
    form.avatar = null;
    form.clearErrors();
  };
  
  const saveProfile = () => {
    form.post(route('dashboard.update'), {
      onSuccess: () => {
        isEditing.value = false;
      },
    });
  };
  
  // Format date display
  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString(undefined, { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  };
  </script>
  
  <style>
  /* Additional styles for enhanced design */
  .bg-gradient-primary {
    background: linear-gradient(135deg, #4361ee, #3f37c9);
  }
  
  .rounded-4 {
    border-radius: 1rem !important;
  }
  
  .avatar-xl {
    width: 100px !important;
    height: 100px !important;
  }
  
  .position-relative {
    position: relative;
  }
  
  .avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  
  .position-relative:hover .avatar-overlay {
    opacity: 1;
  }
  
  .avatar-edit-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #4361ee;
    transition: all 0.2s ease;
  }
  
  .avatar-edit-btn:hover {
    transform: scale(1.1);
    background-color: #f8f9fa;
  }
  
  .profile-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: rgba(67, 97, 238, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .card {
    transition: transform 0.2s ease;
  }
  
  .card:hover {
    transform: translateY(-3px);
  }
  </style>