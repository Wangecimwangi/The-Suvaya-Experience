<template>
  <v-form @submit.prevent="submitReservation" ref="formRef">
    <v-card class="reservation-form-card pa-4 pa-md-6" elevation="4">
      <v-card-title class="form-title text-center mb-3">
        <v-icon color="amber-darken-2" class="mr-2">mdi-account-edit</v-icon>
        Reservation Details
      </v-card-title>

      <v-card-subtitle class="form-desc text-center mb-5">
        Fill in your information to complete the booking
      </v-card-subtitle>

      <v-card-text class="px-2 px-md-4">
        <!-- Personal Information Section -->
        <div class="mb-4">
          <h4 class="section-label mb-3">
            <v-icon size="20" color="amber-darken-2" class="mr-1">mdi-account</v-icon>
            Personal Information
          </h4>

          <v-text-field
            v-model="name"
            label="Full Name"
            variant="outlined"
            color="amber-darken-2"
            prepend-inner-icon="mdi-account"
            :rules="[v => !!v || 'Name is required']"
            required
            class="mb-2"
          ></v-text-field>

          <v-text-field
            v-model="email"
            label="Email Address"
            type="email"
            variant="outlined"
            color="amber-darken-2"
            prepend-inner-icon="mdi-email"
            :rules="[v => !!v || 'Email is required', v => /.+@.+\..+/.test(v) || 'Email must be valid']"
            required
            class="mb-2"
          ></v-text-field>

          <v-text-field
            v-model="phone"
            label="Phone Number"
            variant="outlined"
            color="amber-darken-2"
            prepend-inner-icon="mdi-phone"
            :rules="[v => !!v || 'Phone is required']"
            required
            class="mb-2"
          ></v-text-field>
        </div>

        <v-divider class="my-5"></v-divider>

        <!-- Reservation Details Section -->
        <div class="mb-4">
          <h4 class="section-label mb-3">
            <v-icon size="20" color="amber-darken-2" class="mr-1">mdi-calendar-clock</v-icon>
            Date & Time
          </h4>

          <v-menu
            v-model="menu"
            :close-on-content-click="false"
            transition="scale-transition"
          >
            <template v-slot:activator="{ props }">
              <v-text-field
                v-model="formattedDate"
                label="Select Date"
                variant="outlined"
                color="amber-darken-2"
                prepend-inner-icon="mdi-calendar"
                readonly
                v-bind="props"
                :rules="[v => !!date || 'Date is required']"
                required
                class="mb-2"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="date"
              color="amber-darken-2"
              @update:model-value="menu = false"
            ></v-date-picker>
          </v-menu>

          <v-text-field
            v-model="time"
            label="Preferred Time"
            type="time"
            variant="outlined"
            color="amber-darken-2"
            prepend-inner-icon="mdi-clock-outline"
            :rules="[v => !!v || 'Time is required']"
            required
            class="mb-2"
          ></v-text-field>

          <v-text-field
            v-model="guests"
            label="Number of Guests"
            type="number"
            variant="outlined"
            color="amber-darken-2"
            prepend-inner-icon="mdi-account-group"
            min="1"
            :rules="[v => !!v || 'Number of guests is required', v => v > 0 || 'Must be at least 1']"
            required
            class="mb-2"
          ></v-text-field>
        </div>

        <v-divider class="my-5"></v-divider>

        <!-- Additional Information -->
        <div>
          <h4 class="section-label mb-3">
            <v-icon size="20" color="amber-darken-2" class="mr-1">mdi-note-text</v-icon>
            Additional Information
          </h4>

          <v-textarea
            v-model="notes"
            label="Special Requests or Notes (Optional)"
            variant="outlined"
            color="amber-darken-2"
            prepend-inner-icon="mdi-message-text"
            rows="3"
            placeholder="Dietary restrictions, special occasions, cake flavors, etc."
          ></v-textarea>
        </div>

        <!-- Deposit Notice -->
        <v-alert
          type="info"
          variant="tonal"
          icon="mdi-information"
          class="mt-4"
        >
          <strong>Payment Information:</strong> A 50% deposit is required to confirm your reservation. The remaining balance will be due on delivery or at the event.
        </v-alert>
      </v-card-text>

      <v-card-actions class="px-4 pb-4 pt-2">
        <v-btn
          color="amber-darken-2"
          type="submit"
          size="large"
          block
          :loading="loading"
        >
          <v-icon left class="mr-2">mdi-check-circle</v-icon>
          Submit Reservation
        </v-btn>
      </v-card-actions>
    </v-card>

    <v-snackbar v-model="showSnackbar" color="success" :timeout="4000">
      <v-icon class="mr-2">mdi-check-circle</v-icon>
      Reservation submitted successfully!
    </v-snackbar>
  </v-form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { isDateBooked, addBooking } from '@/api/bookings'

const router = useRouter()
const authStore = useAuthStore()

// Check if user is logged in
if (!authStore.isLoggedIn) {
  if (confirm('You need to login to make a reservation. Login now?')) {
    router.push({
      path: '/login',
      query: { redirect: '/reservation' }
    })
  }
}

const name = ref('')
const email = ref('')
const phone = ref('')
const date = ref(null)
const time = ref('')
const guests = ref(1)
const notes = ref('')
const menu = ref(false)
const formRef = ref(null)
const showSnackbar = ref(false)
const loading = ref(false)

const formattedDate = computed(() => {
  if (!date.value) return ''
  const dateObj = new Date(date.value)
  return dateObj.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
})

async function submitReservation() {
  if (!formRef.value) return

  const { valid } = await formRef.value.validate()

  if (!valid) {
    return
  }

  if (!date.value) {
    alert('Please pick a date for the reservation')
    return
  }

  // Format date to YYYY-MM-DD
  const formattedDate = date.value instanceof Date
    ? date.value.toISOString().split('T')[0]
    : date.value

  // Check if date is already booked
  const dateBooked = await isDateBooked(formattedDate)
  if (dateBooked) {
    alert('Sorry, that date is already booked. Pick another date.')
    return
  }

  loading.value = true

  try {
    // Send to backend API
    const response = await addBooking(formattedDate, {
      type: 'reservation',
      name: name.value,
      email: email.value,
      phone: phone.value,
      time: time.value,
      guests: guests.value,
      notes: notes.value,
    })

    // Save reservation data to pass to success page
    const reservationData = {
      id: response.data.reservation_id,
      name: name.value,
      email: email.value,
      phone: phone.value,
      date: formattedDate,
      time: time.value,
      guests: guests.value,
      notes: notes.value,
      deposit_paid: false
    }

    localStorage.setItem('lastReservation', JSON.stringify(reservationData))

    // Redirect to success page
    router.push('/reservation-success')

  } catch (error) {
    console.error('Reservation error:', error)
    alert('Failed to create reservation. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.reservation-form-card {
  background: #fffbe6;
  border-radius: 16px;
  height: 100%;
}

.form-title {
  color: #b28704;
  font-size: 1.3rem;
  font-weight: 700;
}

@media (min-width: 600px) {
  .form-title {
    font-size: 1.5rem;
  }
}

.form-desc {
  color: #7a7a7a;
  font-size: 0.9rem;
}

@media (min-width: 600px) {
  .form-desc {
    font-size: 1rem;
  }
}

.section-label {
  color: #b28704;
  font-size: 1rem;
  font-weight: 600;
  display: flex;
  align-items: center;
}

@media (min-width: 600px) {
  .section-label {
    font-size: 1.1rem;
  }
}

:deep(.v-input) {
  font-size: 0.95rem;
}

:deep(.v-field) {
  border-radius: 12px;
}

:deep(.v-field--focused) {
  box-shadow: 0 0 0 2px rgba(178, 135, 4, 0.1);
}
</style>
