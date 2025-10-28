<template>
  <v-form @submit.prevent="submitReservation" ref="form">
    <v-card class="mx-auto my-8" max-width="500" elevation="10">
      <v-card-title class="text-h5 text-center">Book a Reservation</v-card-title>
      <v-card-text>
        <v-text-field v-model="name" label="Full Name" :rules="[v => !!v || 'Name is required']" required></v-text-field>
        <v-text-field v-model="email" label="Email" type="email" :rules="[v => /.+@.+\..+/.test(v) || 'E-mail must be valid']" required></v-text-field>
        <v-text-field v-model="phone" label="Phone Number" :rules="[v => !!v || 'Phone is required']" required></v-text-field>
        <v-menu ref="menu" v-model="menu" :close-on-content-click="false" transition="scale-transition" offset-y min-width="auto">
          <template #activator="{ on, attrs }">
            <v-text-field v-model="date" label="Date" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
          </template>
          <v-date-picker v-model="date" @input="menu = false"></v-date-picker>
        </v-menu>
        <v-text-field v-model="time" label="Time" type="time" required></v-text-field>
        <v-text-field v-model="guests" label="Number of Guests" type="number" min="1" required></v-text-field>
        <v-textarea v-model="notes" label="Special Requests" rows="2"></v-textarea>
      </v-card-text>
      <v-card-actions class="justify-center">
        <v-btn color="amber darken-2" type="submit" large>Reserve</v-btn>
      </v-card-actions>
    </v-card>
  </v-form>
</template>

<script setup>
import { ref } from 'vue'
import { isDateBooked, addBooking } from '@/api/bookings'

const name = ref('')
const email = ref('')
const phone = ref('')
const date = ref('')
const time = ref('')
const guests = ref(1)
const notes = ref('')
const menu = ref(false)
const form = ref(null)
const showSnackbar = ref(false)

function submitReservation() {
  if (!form.value) return
  if (!form.value.validate()) return
  if (!date.value) {
    alert('Please pick a date for the reservation')
    return
  }
  if (isDateBooked(date.value)) {
    alert('Sorry, that date is already booked. Pick another date.')
    return
  }

  // persist booking (and record deposit placeholder)
  addBooking(date.value, {
    type: 'reservation',
    name: name.value,
    email: email.value,
    phone: phone.value,
    time: time.value,
    guests: guests.value,
    notes: notes.value,
    depositRequired: true,
  })

  showSnackbar.value = true

  // Reset form
  name.value = ''
  email.value = ''
  phone.value = ''
  date.value = ''
  time.value = ''
  guests.value = 1
  notes.value = ''
}
</script>

<style scoped>
.v-card {
  border-radius: 18px;
  background: #fffbe6;
}
.v-card-title {
  color: #b28704;
}
.v-btn {
  color: white;
}
</style>
