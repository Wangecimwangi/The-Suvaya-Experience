<template>
  <v-container class="py-8">
    <v-row>
      <v-col cols="12" class="text-center mb-6">
        <h2 class="calendar-title">Book Your Date</h2>
        <p class="calendar-desc">Select a date to see availability and make a reservation. Bookings require a 50% deposit.</p>
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col cols="12" md="6">
        <v-date-picker v-model="selectedDate" :allowed-dates="allowedDates" color="amber-darken-2" @input="onDateSelect" />
      </v-col>
    </v-row>
    <v-row v-if="selectedDate">
      <v-col cols="12" class="text-center">
        <v-alert :type="isBooked(selectedDate) ? 'error' : 'success'" border="left" colored-border>
          <span v-if="isBooked(selectedDate)">Sorry, this date is already booked.</span>
          <span v-else>This date is available! You can proceed to book.</span>
        </v-alert>
        <v-btn v-if="!isBooked(selectedDate)" color="amber darken-2" class="mt-4" @click="bookDate">Book This Date</v-btn>
      </v-col>
    </v-row>
    <v-dialog v-model="showDialog" max-width="400">
      <v-card>
        <v-card-title class="headline">Confirm Booking</v-card-title>
        <v-card-text>
          <div>You are booking <strong>{{ selectedDate }}</strong>.</div>
          <div class="mt-2">A 50% deposit is required to secure your booking.</div>
        </v-card-text>
        <v-card-actions>
          <v-btn color="amber darken-2" @click="payDeposit">Pay Deposit</v-btn>
          <v-btn text @click="showDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-snackbar v-model="showSnackbar" color="success">Booking successful! Deposit paid.</v-snackbar>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { isDateBooked, addBooking } from '@/api/bookings'

const selectedDate = ref('')
const showDialog = ref(false)
const showSnackbar = ref(false)

function allowedDates(date) {
  return !isDateBooked(date)
}
function isBooked(date) {
  return isDateBooked(date)
}
function onDateSelect(date) {}
function bookDate() {
  showDialog.value = true
}
function payDeposit() {
  addBooking(selectedDate.value, { source: 'calendar', deposit: true })
  showDialog.value = false
  showSnackbar.value = true
  selectedDate.value = ''
}
</script>

<style scoped>
.calendar-title {
  color: #b28704;
  font-size: 2rem;
  font-weight: 700;
}
.calendar-desc {
  color: #b28704;
  font-size: 1.1rem;
  margin-bottom: 1.5rem;
}
</style>
