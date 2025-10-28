<script setup>
const isLoggedIn = JSON.parse(localStorage.getItem('isLoggedIn'))
const isAdmin = true
</script>

<template>
  <v-app-bar color="amber-accent-4" elevate-on-scroll app class="navbar">
    <v-app-bar-title class="font-weight-bold">
      <v-icon left color="amber-darken-4">mdi-cake-variant</v-icon>
      The Suvaya Experience
    </v-app-bar-title>
    <v-spacer />
    <v-btn to="/" icon class="mx-1" title="Home"><v-icon>mdi-home</v-icon></v-btn>
    <v-btn to="/menu" class="mx-1" title="Menu">Menu</v-btn>
    <v-btn to="/signup" class="mx-1" title="Sign Up">Sign Up</v-btn>
    <v-btn to="/admin" class="mx-1" title="Admin" v-if="isAdmin">Admin</v-btn>
    <v-btn to="/aboutus" class="mx-1" title="About Us">About Us</v-btn>
    <v-btn to="/events" class="mx-1" title="Events">Events</v-btn>
    <v-btn to="/contactus" class="mx-1" title="Contact Us">Contact Us</v-btn>
    <v-btn to="/orders" class="mx-1" title="Orders">Orders</v-btn>
    <v-btn to="/reservation" class="mx-1" title="Reservation">Reservation</v-btn>
    <v-btn to="/userprofile" class="mx-1" title="User Profile">User</v-btn>
    <v-btn to="/login" class="mx-1" title="Login">Login</v-btn>
  </v-app-bar>
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
.v-btn {
  color: #b28704 !important;
  font-weight: 500;
  text-transform: none;
}
.v-btn[icon] {
  color: #b28704 !important;
}
</style>