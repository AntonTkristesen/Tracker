<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import TextInput from '../Components/TextInput.vue';
import { ref } from 'vue';

const form = useForm({
    avatar: null,
    name: null, 
    email: null,
    password: null,
    password_confirmation: null,
});

const avatarPreview = ref(null);

const change = (e) => {
    const file = e.target.files[0];
    form.avatar = file;
    
    // Create preview for the selected image
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post('/register', {
        onError: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head :title="`TimeFlow | ${$page.component}`" />
    
    <div class="container-fluid py-5 bg-light">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <h2 class="mb-4 text-primary fw-bold">
                    <i class="pi pi-user-plus me-2"></i>Create Your Account
                </h2>
                
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-gradient-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="pi pi-user-plus me-2"></i>Register to TimeFlow</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <form @submit.prevent="submit" class="row g-3">
                            <!-- Avatar Upload -->
                            <div class="col-12">
                                <label for="avatar" class="form-label fw-bold text-primary">
                                    <i class="pi pi-image me-2"></i>Profile Picture
                                </label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-preview me-3">
                                        <div v-if="avatarPreview" class="avatar-container">
                                            <img :src="avatarPreview" alt="Avatar Preview" class="avatar-img rounded-circle border border-2 border-primary shadow-sm" style="width: 80px; height: 80px; object-fit: cover;" />
                                        </div>
                                        <div v-else class="avatar-placeholder rounded-circle bg-gradient-secondary d-flex align-items-center justify-content-center text-white" style="width: 80px; height: 80px;">
                                            <i class="pi pi-user" style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="input-group">
                                            <input 
                                                type="file" 
                                                id="avatar" 
                                                name="avatar" 
                                                @input="change" 
                                                class="form-control" 
                                                accept="image/*"
                                            />
                                            <label class="input-group-text bg-primary text-white" for="avatar">
                                                <i class="pi pi-upload me-1"></i> Choose
                                            </label>
                                        </div>

                                        <p v-if="form.errors.avatar" class="text-danger small mt-1">{{ form.errors.avatar }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Name Input -->
                            <div class="col-12">
                                <label for="name" class="form-label fw-bold text-primary">
                                    <i class="pi pi-id-card me-2"></i>Full Name
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="pi pi-user"></i></span>
                                    <TextInput
                                    id="name"
                                    name="name"
                                    v-model="form.name"
                                    :message="form.errors.name"
                                    icon="pi-user"
                                    placeholder="Enter your full name"
                                    />
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-12">
                                <label for="email" class="form-label fw-bold text-primary">
                                    <i class="pi pi-envelope me-2"></i>Email Address
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="pi pi-at"></i></span>
                                    <TextInput
                                        id="email"
                                        name="email"
                                        type="email"
                                        v-model="form.email"
                                        :message="form.errors.email"
                                        icon="pi-at"
                                        placeholder="Enter your email address"
                                        />

                                </div>
                            </div>

                            <!-- Password Input -->
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold text-primary">
                                    <i class="pi pi-lock me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="pi pi-lock"></i></span>
                                    <TextInput
                                        id="password"
                                        name="password"
                                        type="password"
                                        v-model="form.password"
                                        :message="form.errors.password"
                                        icon="pi-lock"
                                        placeholder="Create a password"
                                        />

                                </div>
                            </div>

                            <!-- Confirm Password Input -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-bold text-primary">
                                    <i class="pi pi-lock me-2"></i>Confirm Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="pi pi-lock"></i></span>
                                    <TextInput
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        v-model="form.password_confirmation"
                                        :message="form.errors.password"
                                        icon="pi-lock"
                                        placeholder="Repeat password"
                                    />
                                </div>
                            </div>

                            <!-- Submit Button and Login Link -->
                            <div class="col-12 mt-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                                    <p class="mb-3 mb-md-0">
                                        Already have an account? 
                                        <Link :href="route('login')" class="text-primary fw-bold text-decoration-none">
                                            <i class="pi pi-sign-in me-1"></i>Log In
                                        </Link>
                                    </p>
                                    <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-bold shadow-sm">
                                        <i class="pi pi-user-plus me-2"></i>Create Account
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Additional Info Card -->
                <div class="card border-0 shadow-sm rounded-4 mt-4 bg-white">
                    <div class="card-body p-4">
                        <h5 class="mb-3 text-primary">
                            <i class="pi pi-info-circle me-2"></i>Why Join TimeFlow?
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <div class="feature-icon text-primary me-3">
                                        <i class="pi pi-clock" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Track Time</h6>
                                        <p class="text-muted small mb-0">Easily track time for all your projects</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <div class="feature-icon text-primary me-3">
                                        <i class="pi pi-chart-bar" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Get Insights</h6>
                                        <p class="text-muted small mb-0">Detailed reports and analytics</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <div class="feature-icon text-primary me-3">
                                        <i class="pi pi-mobile" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Work Anywhere</h6>
                                        <p class="text-muted small mb-0">Accessible on all devices</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Additional styles for enhanced design */
.bg-gradient-primary {
    background: linear-gradient(135deg, #4361ee, #3f37c9);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #4a4e69, #22223b);
}

.rounded-4 {
    border-radius: 1rem !important;
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-3px);
}

.avatar-container {
    transition: all 0.3s ease;
}

.avatar-container:hover {
    transform: scale(1.05);
}

.form-control:focus {
    border-color: #4361ee;
    box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
}

/* Fix TextInput compatibility */
:deep(.form-control) {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Fix button hover effect */
.btn-primary {
    background: #4361ee;
    border-color: #4361ee;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #3f37c9;
    border-color: #3f37c9;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(67, 97, 238, 0.25);
}
</style>