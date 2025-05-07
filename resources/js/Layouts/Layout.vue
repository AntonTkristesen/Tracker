<template>
  <div>
    <header>
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
          <Link :href="route('home')" class="navbar-brand">MyApp</Link>
          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item" v-if="$page.props.auth.user">
                <Link :href="route('dashboard')" class="nav-link">Dashboard</Link>
              </li>
              <li class="nav-item" v-if="$page.props.auth.user">
                <Link :href="route('home')" class="nav-link">Home</Link>
              </li>
              <li class="nav-item" v-if="$page.props.auth.user">
                <Link :href="route('logout')" method="post" as="button" class="nav-link">Logout</Link>
              </li>
              <li class="nav-item" v-else>
                <Link :href="route('register')" class="nav-link">Register</Link>
              </li>
              <li class="nav-item" v-else>
                <Link :href="route('login')" class="nav-link">Login</Link>
              </li>
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
// No logic needed here unless you want to add script-side customization
</script>
