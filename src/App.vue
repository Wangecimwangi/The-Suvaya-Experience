<script setup>
import { RouterView } from 'vue-router'

import NavBar from './Components/Navbar.vue'
import Footer from './Components/Footer.vue'

</script>



<template>
  <v-app>
    <NavBar/>
    <v-main>
      <RouterView></RouterView>
    </v-main>
    <Footer />
  </v-app>
</template>

<style scoped></style>
  

