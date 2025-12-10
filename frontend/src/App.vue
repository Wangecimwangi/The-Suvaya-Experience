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

<style>
/* Global Button Enhancements */
:deep(.v-btn) {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  letter-spacing: 0.5px;
  font-weight: 600 !important;
}

/* Hover effects for all buttons */
:deep(.v-btn:hover) {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

/* Active/Click effect */
:deep(.v-btn:active) {
  transform: translateY(0px);
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
}

/* Primary amber buttons (main CTA) */
:deep(.v-btn.bg-amber-darken-2),
:deep(.v-btn[color="amber-darken-2"]) {
  background: linear-gradient(135deg, #b28704 0%, #d4a017 100%) !important;
  box-shadow: 0 4px 12px rgba(178, 135, 4, 0.3);
}

:deep(.v-btn.bg-amber-darken-2:hover),
:deep(.v-btn[color="amber-darken-2"]:hover) {
  background: linear-gradient(135deg, #c09705 0%, #e5b018 100%) !important;
  box-shadow: 0 6px 20px rgba(178, 135, 4, 0.4);
  transform: translateY(-2px) scale(1.02);
}

/* Outlined buttons */
:deep(.v-btn--variant-outlined) {
  border-width: 2px !important;
  background: rgba(255, 255, 255, 0.95) !important;
}

:deep(.v-btn--variant-outlined:hover) {
  background: rgba(178, 135, 4, 0.05) !important;
  border-width: 2px !important;
}

/* Success/Green buttons */
:deep(.v-btn.bg-green),
:deep(.v-btn[color="green"]),
:deep(.v-btn.bg-success) {
  background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%) !important;
  box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
}

:deep(.v-btn.bg-green:hover),
:deep(.v-btn[color="green"]:hover),
:deep(.v-btn.bg-success:hover) {
  background: linear-gradient(135deg, #388e3c 0%, #66bb6a 100%) !important;
  box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
}

/* Error/Red buttons */
:deep(.v-btn.bg-error),
:deep(.v-btn[color="error"]) {
  background: linear-gradient(135deg, #c62828 0%, #e53935 100%) !important;
  box-shadow: 0 4px 12px rgba(198, 40, 40, 0.3);
}

:deep(.v-btn.bg-error:hover),
:deep(.v-btn[color="error"]:hover) {
  background: linear-gradient(135deg, #d32f2f 0%, #ef5350 100%) !important;
  box-shadow: 0 6px 20px rgba(198, 40, 40, 0.4);
}

/* Icon buttons */
:deep(.v-btn--icon) {
  border-radius: 50% !important;
}

:deep(.v-btn--icon:hover) {
  transform: rotate(5deg) scale(1.1);
}

/* Loading state */
:deep(.v-btn--loading) {
  opacity: 0.8;
  pointer-events: none;
}

/* Disabled state */
:deep(.v-btn--disabled) {
  opacity: 0.5 !important;
  box-shadow: none !important;
  transform: none !important;
}

/* Text buttons */
:deep(.v-btn--variant-text) {
  box-shadow: none !important;
}

:deep(.v-btn--variant-text:hover) {
  background: rgba(178, 135, 4, 0.08) !important;
  transform: translateY(0px);
  box-shadow: none !important;
}

/* Large buttons */
:deep(.v-btn--size-large) {
  font-size: 1rem !important;
  padding: 12px 32px !important;
  border-radius: 12px !important;
}

/* Small buttons */
:deep(.v-btn--size-small) {
  font-size: 0.875rem !important;
  border-radius: 8px !important;
}

/* Block buttons (full width) */
:deep(.v-btn--block) {
  border-radius: 12px !important;
}

/* Smooth icon transitions */
:deep(.v-btn .v-icon) {
  transition: transform 0.3s ease;
}

:deep(.v-btn:hover .v-icon) {
  transform: scale(1.1);
}

/* Ripple effect enhancement */
:deep(.v-btn .v-ripple__container) {
  opacity: 0.3;
}
</style>

<style scoped></style>
  

