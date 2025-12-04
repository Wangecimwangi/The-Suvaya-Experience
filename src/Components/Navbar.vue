<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()
const drawer = ref(false)

function handleLogout() {
  authStore.logout()
  router.push('/login')
}
</script>

<template>
  <v-app-bar color="amber-accent-4" elevate-on-scroll app class="navbar">
    <v-app-bar-title class="font-weight-bold d-flex align-center">
      <v-icon color="amber-darken-4" class="mr-2">mdi-cake-variant</v-icon>
      <span class="d-none d-sm-inline">The Suvaya Experience</span>
      <span class="d-inline d-sm-none">Suvaya</span>
    </v-app-bar-title>

    <v-spacer />

    <!-- Desktop Navigation -->
    <div class="d-none d-lg-flex align-center">
      <v-btn to="/" variant="text" class="mx-1">Home</v-btn>
      <v-btn to="/menu" variant="text" class="mx-1">Menu</v-btn>
      <v-btn to="/reservation" variant="text" class="mx-1">Reservation</v-btn>
      <v-btn to="/events" variant="text" class="mx-1">Events</v-btn>
      <v-btn to="/aboutus" variant="text" class="mx-1">About</v-btn>
      <v-btn to="/contactus" variant="text" class="mx-1">Contact</v-btn>

      <!-- Shopping Cart -->
      <v-btn icon variant="text" class="mx-1" to="/cart">
        <v-badge
          :content="cartStore.itemCount"
          :model-value="cartStore.itemCount > 0"
          color="red"
          overlap
        >
          <v-icon>mdi-cart</v-icon>
        </v-badge>
      </v-btn>

      <!-- Logged In User Menu -->
      <template v-if="authStore.isLoggedIn">
        <v-btn to="/admin" variant="text" class="mx-1" v-if="authStore.isAdmin">Admin</v-btn>

        <v-menu>
          <template v-slot:activator="{ props }">
            <v-btn icon variant="text" class="mx-1" v-bind="props">
              <v-icon>mdi-account-circle</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item>
              <v-list-item-title class="font-weight-bold">{{ authStore.user?.name }}</v-list-item-title>
              <v-list-item-subtitle>{{ authStore.user?.email }}</v-list-item-subtitle>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item to="/userprofile" prepend-icon="mdi-account">
              <v-list-item-title>Profile</v-list-item-title>
            </v-list-item>
            <v-list-item to="/my-reservations" prepend-icon="mdi-calendar-multiple">
              <v-list-item-title>My Reservations</v-list-item-title>
            </v-list-item>
            <v-list-item to="/orders" prepend-icon="mdi-receipt">
              <v-list-item-title>My Orders</v-list-item-title>
            </v-list-item>
            <v-list-item @click="handleLogout" prepend-icon="mdi-logout">
              <v-list-item-title>Logout</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>

      <!-- Not Logged In -->
      <template v-else>
        <v-btn to="/login" variant="text" class="mx-1">Login</v-btn>
        <v-btn to="/signup" variant="outlined" class="mx-1">Sign Up</v-btn>
      </template>
    </div>

    <!-- Mobile Menu Button -->
    <v-app-bar-nav-icon
      class="d-lg-none"
      @click="drawer = !drawer"
      color="amber-darken-4"
    ></v-app-bar-nav-icon>
  </v-app-bar>

  <!-- Mobile Navigation Drawer -->
  <v-navigation-drawer
    v-model="drawer"
    location="right"
    temporary
    class="mobile-drawer"
  >
    <v-list density="compact" nav>
      <!-- User Info (if logged in) -->
      <template v-if="authStore.isLoggedIn">
        <v-list-item class="py-3">
          <v-list-item-title class="font-weight-bold">{{ authStore.user?.name }}</v-list-item-title>
          <v-list-item-subtitle class="text-caption">{{ authStore.user?.email }}</v-list-item-subtitle>
        </v-list-item>
        <v-divider class="mb-2"></v-divider>
      </template>

      <!-- Main Navigation -->
      <v-list-item prepend-icon="mdi-home" title="Home" to="/" @click="drawer = false"></v-list-item>
      <v-list-item prepend-icon="mdi-silverware-fork-knife" title="Menu" to="/menu" @click="drawer = false"></v-list-item>
      <v-list-item prepend-icon="mdi-calendar-check" title="Reservation" to="/reservation" @click="drawer = false"></v-list-item>
      <v-list-item prepend-icon="mdi-party-popper" title="Events" to="/events" @click="drawer = false"></v-list-item>
      <v-list-item prepend-icon="mdi-information" title="About Us" to="/aboutus" @click="drawer = false"></v-list-item>
      <v-list-item prepend-icon="mdi-email" title="Contact Us" to="/contactus" @click="drawer = false"></v-list-item>

      <v-divider class="my-2"></v-divider>

      <!-- Shopping Cart -->
      <v-list-item to="/cart" @click="drawer = false">
        <template v-slot:prepend>
          <v-badge
            :content="cartStore.itemCount"
            :model-value="cartStore.itemCount > 0"
            color="red"
            overlap
          >
            <v-icon>mdi-cart</v-icon>
          </v-badge>
        </template>
        <v-list-item-title>Shopping Cart</v-list-item-title>
      </v-list-item>

      <!-- Logged In Options -->
      <template v-if="authStore.isLoggedIn">
        <v-divider class="my-2"></v-divider>
        <v-list-item prepend-icon="mdi-calendar-multiple" title="My Reservations" to="/my-reservations" @click="drawer = false"></v-list-item>
        <v-list-item prepend-icon="mdi-receipt" title="My Orders" to="/orders" @click="drawer = false"></v-list-item>
        <v-list-item prepend-icon="mdi-shield-account" title="Admin" to="/admin" @click="drawer = false" v-if="authStore.isAdmin"></v-list-item>
        <v-list-item prepend-icon="mdi-account-circle" title="Profile" to="/userprofile" @click="drawer = false"></v-list-item>
        <v-list-item prepend-icon="mdi-logout" title="Logout" @click="handleLogout(); drawer = false"></v-list-item>
      </template>

      <!-- Not Logged In -->
      <template v-else>
        <v-divider class="my-2"></v-divider>
        <v-list-item prepend-icon="mdi-login" title="Login" to="/login" @click="drawer = false"></v-list-item>
        <v-list-item prepend-icon="mdi-account-plus" title="Sign Up" to="/signup" @click="drawer = false"></v-list-item>
      </template>
    </v-list>
  </v-navigation-drawer>
</template>

<style scoped>
.navbar {
  border-bottom: 2px solid #b28704;
  background: #fffbe6 !important;
}
.v-app-bar-title {
  color: #b28704;
  font-size: 1.4rem;
  letter-spacing: 1px;
}

.v-app-bar-title span {
  font-size: 1.2rem;
}

@media (min-width: 600px) {
  .v-app-bar-title span {
    font-size: 1.4rem;
  }
}

.v-btn {
  color: #b28704 !important;
  font-weight: 500;
  text-transform: none;
}

.v-btn[icon] {
  color: #b28704 !important;
}

.mobile-drawer {
  background: #fffbe6;
}

.mobile-drawer .v-list-item {
  color: #b28704;
}

.mobile-drawer .v-list-item:hover {
  background: #fff8dc;
}
</style>