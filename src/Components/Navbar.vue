<script setup>
import { ref } from 'vue'
const isLoggedIn = JSON.parse(localStorage.getItem('isLoggedIn'))
const isAdmin = true
const drawer = ref(false)
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
      <v-btn to="/orders" variant="text" class="mx-1" v-if="isLoggedIn">Orders</v-btn>
      <v-btn to="/admin" variant="text" class="mx-1" v-if="isAdmin">Admin</v-btn>
      <v-btn to="/userprofile" icon variant="text" class="mx-1" v-if="isLoggedIn">
        <v-icon>mdi-account-circle</v-icon>
      </v-btn>
      <v-btn to="/login" variant="text" class="mx-1" v-if="!isLoggedIn">Login</v-btn>
      <v-btn to="/signup" variant="outlined" class="mx-1" v-if="!isLoggedIn">Sign Up</v-btn>
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
      <v-list-item prepend-icon="mdi-home" title="Home" to="/" @click="drawer = false"></v-list-item>
      <v-list-item prepend-icon="mdi-silverware-fork-knife" title="Menu" to="/menu" @click="drawer = false"></v-list-item>
      <v-list-item prepend-icon="mdi-calendar-check" title="Reservation" to="/reservation" @click="drawer = false"></v-list-item>
      <v-list-item prepend-icon="mdi-party-popper" title="Events" to="/events" @click="drawer = false"></v-list-item>
      <v-list-item prepend-icon="mdi-information" title="About Us" to="/aboutus" @click="drawer = false"></v-list-item>
      <v-list-item prepend-icon="mdi-email" title="Contact Us" to="/contactus" @click="drawer = false"></v-list-item>
      <v-divider class="my-2" v-if="isLoggedIn || isAdmin"></v-divider>
      <v-list-item prepend-icon="mdi-receipt" title="My Orders" to="/orders" @click="drawer = false" v-if="isLoggedIn"></v-list-item>
      <v-list-item prepend-icon="mdi-shield-account" title="Admin" to="/admin" @click="drawer = false" v-if="isAdmin"></v-list-item>
      <v-list-item prepend-icon="mdi-account-circle" title="Profile" to="/userprofile" @click="drawer = false" v-if="isLoggedIn"></v-list-item>
      <v-divider class="my-2"></v-divider>
      <v-list-item prepend-icon="mdi-login" title="Login" to="/login" @click="drawer = false" v-if="!isLoggedIn"></v-list-item>
      <v-list-item prepend-icon="mdi-account-plus" title="Sign Up" to="/signup" @click="drawer = false" v-if="!isLoggedIn"></v-list-item>
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