<template>
  <div>
    <header class="shadow-sm">
      <nav class="navbar navbar-expand-lg">
        <div class="container">
          <!-- Logo/Brand -->
          <Link :href="route('home')" class="navbar-brand d-flex align-items-center">
            <div class="brand-logo rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center me-2">
              <i class="pi pi-bolt text-white"></i>
            </div>
            <span class="brand-text fw-bold text-primary">TimeFlow</span>
          </Link>

          <!-- Mobile Toggle Button -->
          <button
            class="navbar-toggler border-0 shadow-none"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarContent"
            aria-controls="navbarContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <i class="pi pi-bars text-primary" style="font-size: 1.25rem"></i>
          </button>

          <!-- Navigation Menu -->
          <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-center">
              <template v-if="$page.props.auth && $page.props.auth.user">
                <!-- Nav Items for Authenticated Users -->
                <li class="nav-item mx-1">
                  <Link :href="route('dashboard')" class="nav-link px-3 py-2 rounded-pill d-flex align-items-center">
                    <i class="pi pi-chart-bar me-2 d-none d-sm-inline-block"></i>
                    <span>Dashboard</span>
                  </Link>
                </li>
                <li class="nav-item mx-1">
                  <Link :href="route('home')" class="nav-link px-3 py-2 rounded-pill d-flex align-items-center">
                    <i class="pi pi-home me-2 d-none d-sm-inline-block"></i>
                    <span>Home</span>
                  </Link>
                </li>
                <li class="nav-item mx-1">
                  <Link :href="route('tracking')" class="nav-link px-3 py-2 rounded-pill d-flex align-items-center">
                    <i class="pi pi-clock me-2 d-none d-sm-inline-block"></i>
                    <span>Tracking</span>
                  </Link>
                </li>
                <li class="nav-item mx-1">
                  <Link :href="route('calendar')" class="nav-link px-3 py-2 rounded-pill d-flex align-items-center">
                    <i class="pi pi-calendar me-2 d-none d-sm-inline-block"></i>
                    <span>Calendar</span>
                  </Link>
                </li>

                <!-- User Menu - Using Vue-controlled dropdown instead of Bootstrap -->
                <li class="nav-item ms-2 position-relative">
                  <button 
                    @click="toggleUserMenu" 
                    class="nav-link d-flex align-items-center user-menu-link border-0 bg-transparent"
                  >
                    <div class="avatar-container me-2 position-relative">
                      <template v-if="$page.props.auth.user.avatar">
                        <img
                          :src="'/storage/' + $page.props.auth.user.avatar"
                          alt="User Avatar"
                          class="avatar-img rounded-circle border border-2 border-white shadow-sm"
                        />
                      </template>
                      <template v-else>
                        <div class="avatar-placeholder rounded-circle bg-gradient-secondary d-flex align-items-center justify-content-center text-white">
                          {{ getUserInitials() }}
                        </div>
                      </template>
                      <span class="online-indicator"></span>
                    </div>
                    <span class="d-none d-md-block">{{ $page.props.auth.user.name || 'User' }}</span>
                    <i class="pi pi-chevron-down ms-2" style="font-size: 0.8rem"></i>
                  </button>
                  
                  <!-- Vue-controlled dropdown menu -->
                  <transition name="dropdown-fade">
                    <ul v-show="isUserMenuOpen" class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 show position-absolute end-0" style="z-index: 1000;">
                      <li class="dropdown-header px-3 py-2">
                        <div class="fw-bold text-primary">{{ $page.props.auth.user.name || 'User' }}</div>
                        <div class="small text-muted">{{ $page.props.auth.user.email }}</div>
                      </li>
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        <Link :href="route('profile')" class="nav-link px-3 py-2 rounded-pill d-flex align-items-center">
                          <i class="pi pi-clock me-2 d-none d-sm-inline-block"></i>
                          <span>Profile</span>
                        </Link>
                      </li>
                      <li>
                        <a class="dropdown-item d-flex align-items-center px-3 py-2" href="#">
                          <i class="pi pi-cog me-2"></i>
                          <span>Settings</span>
                        </a>
                      </li>
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        <Link 
                          :href="route('logout')" 
                          method="post" 
                          as="button" 
                          class="dropdown-item d-flex align-items-center px-3 py-2 text-danger w-100 text-start"
                        >
                          <i class="pi pi-sign-out me-2"></i>
                          <span>Logout</span>
                        </Link>
                      </li>
                    </ul>
                  </transition>
                </li>
              </template>

              <!-- Nav Items for Guests -->
              <template v-else>
                <li class="nav-item mx-1">
                  <Link :href="route('register')" class="nav-link btn btn-outline-primary rounded-pill px-4">
                    Register
                  </Link>
                </li>
                <li class="nav-item ms-2">
                  <Link :href="route('login')" class="nav-link btn btn-outline-primary rounded-pill px-4">
                    Login
                  </Link>
                </li>
              </template>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <main class="container py-4">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/vue3'

// User dropdown state
const isUserMenuOpen = ref(false)

// Toggle user dropdown menu
const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value
}

// Close menu when clicking outside
const handleClickOutside = (event) => {
  const dropdown = document.querySelector('.user-menu-link')
  if (dropdown && !dropdown.contains(event.target) && isUserMenuOpen.value) {
    isUserMenuOpen.value = false
  }
}

// Lifecycle hooks for click outside handling
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Function to get user initials for avatar placeholder
const getUserInitials = () => {
  const user = usePage().props.auth?.user
  if (!user || !user.name) return 'U'
  
  const nameParts = user.name.split(' ')
  if (nameParts.length >= 2) {
    return `${nameParts[0][0]}${nameParts[1][0]}`.toUpperCase()
  }
  return nameParts[0][0].toUpperCase()
}
</script>

<style scoped>
.navbar {
  background-color: #ffffff;
  padding: 0.75rem 0;
  transition: all 0.3s ease;
}

.brand-logo {
  width: 36px;
  height: 36px;
  transition: all 0.3s ease;
}

.brand-text {
  font-size: 1.25rem;
  letter-spacing: -0.5px;
}

.avatar-img, .avatar-placeholder {
  width: 40px;
  height: 40px;
  transition: all 0.2s ease;
}

.avatar-placeholder {
  font-size: 14px;
  font-weight: bold;
}

.nav-link {
  color: #4a5568;
  font-weight: 500;
  transition: all 0.2s ease;
}

.nav-link:hover {
  color: #4361ee;
  background-color: rgba(67, 97, 238, 0.05);
}

.online-indicator {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 10px;
  height: 10px;
  background-color: #2ec4b6;
  border-radius: 50%;
  border: 2px solid white;
}

.user-menu-link {
  border-radius: 30px;
  padding: 0.4rem 0.8rem;
  transition: all 0.2s ease;
  cursor: pointer;
}

.user-menu-link:hover {
  background-color: rgba(67, 97, 238, 0.05);
}

.dropdown-menu {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  border: none;
  min-width: 240px;
  margin-top: 0.5rem !important;
}

.dropdown-item {
  border-radius: 0.375rem;
  margin: 0.125rem 0.5rem;
  width: auto;
  transition: all 0.2s ease;
}

.dropdown-item:hover {
  background-color: rgba(67, 97, 238, 0.05);
}

/* Dropdown animation */
.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
  transition: opacity 0.2s, transform 0.2s;
}

.dropdown-fade-enter-from,
.dropdown-fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Gradient backgrounds */
.bg-gradient-primary {
  background: linear-gradient(135deg, #4361ee, #3f37c9);
}

.bg-gradient-secondary {
  background: linear-gradient(135deg, #4a4e69, #22223b);
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
  .navbar-collapse {
    background: white;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-top: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  }
  
  .nav-item {
    margin: 0.25rem 0;
  }
  
  .user-menu-link {
    width: 100%;
    justify-content: space-between;
  }
  
  .dropdown-menu {
    width: 100%;
    margin-top: 0.5rem;
    position: static !important;
    transform: none !important;
  }
}
</style>