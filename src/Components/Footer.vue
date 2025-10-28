<template>
  <v-footer class="text-center d-flex flex-column ga-2 py-4" color="amber-lighten-3">
    <div>
      <v-btn icon="mdi-facebook" density="comfortable" variant="text"></v-btn>
      <v-btn icon="mdi-twitter" density="comfortable" variant="text"></v-btn>
      <v-btn icon="mdi-linkedin" density="comfortable" variant="text"></v-btn>
      <v-btn icon="mdi-instagram" density="comfortable" variant="text"></v-btn>
    </div>
    <v-divider class="my-2" thickness="2" width="50"></v-divider>
    <div class="text-caption font-weight-regular opacity-60">
      Welcome to Suvaya, where flavor meets creativity. We craft delicious cakes, savory meals, and food experiences that turn simple moments into something special. Our goal is to make every order personal — whether you’re booking an event, planning a meal, or just treating yourself.
      <br />
      At Suvaya, everything is made with care, passion, and a touch of love — because great food should always feel like home.
    </div>
    <v-divider></v-divider>
    <div>
      {{ new Date().getFullYear() }} — <strong>Suvaya</strong>
    </div>
  </v-footer>
</template>

<script setup>
// No logic needed for static footer
</script>

<style scoped>
.v-footer {
  background: #fffbe6;
  color: #b28704;
  font-size: 1rem;
}
</style>
