<template>
  <v-card class="calendar-card pa-4 pa-md-6" elevation="4">
    <v-card-title class="calendar-title text-center mb-3">
      <v-icon color="amber-darken-2" class="mr-2">mdi-calendar-month</v-icon>
      Select Your Date
    </v-card-title>

    <v-card-subtitle class="calendar-desc text-center mb-4">
      Choose an available date for your reservation
    </v-card-subtitle>

    <v-row justify="center">
      <v-col cols="12" class="d-flex justify-center">
        <v-date-picker
          v-model="selectedDate"
          :allowed-dates="allowedDates"
          color="amber-darken-2"
          @update:model-value="onDateSelect"
          show-adjacent-months
          elevation="0"
          width="100%"
        />
      </v-col>
    </v-row>

    <v-row v-if="selectedDate" class="mt-4">
      <v-col cols="12">
        <v-alert
          :type="isBooked(selectedDate) ? 'error' : 'success'"
          :icon="isBooked(selectedDate) ? 'mdi-calendar-remove' : 'mdi-calendar-check'"
          variant="tonal"
          class="mb-4"
        >
          <span v-if="isBooked(selectedDate)">
            <strong>Date Unavailable</strong><br/>
            This date is already booked. Please select another date.
          </span>
          <span v-else>
            <strong>Date Available!</strong><br/>
            {{ formatDate(selectedDate) }} is available for booking.
          </span>
        </v-alert>

        <v-btn
          v-if="!isBooked(selectedDate)"
          color="amber-darken-2"
          size="large"
          block
          @click="bookDate"
        >
          <v-icon left class="mr-2">mdi-check-circle</v-icon>
          Continue with This Date
        </v-btn>
      </v-col>
    </v-row>

    <v-dialog v-model="showDialog" max-width="450">
      <v-card class="dialog-card">
        <v-card-title class="text-h6 bg-amber-lighten-4">
          <v-icon color="amber-darken-2" class="mr-2">mdi-calendar-check</v-icon>
          Confirm Your Booking
        </v-card-title>

        <v-card-text class="pt-6 pb-4">
          <div class="text-center mb-4">
            <v-icon color="amber-darken-2" size="64">mdi-calendar-heart</v-icon>
          </div>

          <p class="text-body-1 mb-3 text-center">
            You are booking: <strong class="text-amber-darken-2">{{ formatDate(selectedDate) }}</strong>
          </p>

          <v-divider class="my-4"></v-divider>

          <div class="deposit-info pa-4 rounded bg-amber-lighten-5">
            <div class="d-flex align-center mb-2">
              <v-icon color="amber-darken-2" class="mr-2">mdi-information</v-icon>
              <strong>Deposit Required</strong>
            </div>
            <p class="text-body-2 ma-0">
              A 50% deposit is required to secure your booking. The remaining balance will be due on delivery or at the event.
            </p>
          </div>
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showDialog = false">Cancel</v-btn>
          <v-btn color="amber-darken-2" variant="flat" @click="payDeposit">
            <v-icon left class="mr-2">mdi-cash</v-icon>
            Pay Deposit
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-snackbar v-model="showSnackbar" color="success" :timeout="3000">
      <v-icon class="mr-2">mdi-check-circle</v-icon>
      Booking successful! Deposit paid.
    </v-snackbar>
  </v-card>
</template>

<script setup>
import { ref } from 'vue'
import { isDateBooked, addBooking } from '@/api/bookings'

const selectedDate = ref('')
const showDialog = ref(false)
const showSnackbar = ref(false)

async function allowedDates(date) {
  try {
    const booked = await isDateBooked(date)
    return !booked
  } catch (error) {
    return true // Allow date if check fails
  }
}

async function isBooked(date) {
  try {
    return await isDateBooked(date)
  } catch (error) {
    return false
  }
}

function onDateSelect(date) {
  // Date selected
}

function bookDate() {
  showDialog.value = true
}

async function payDeposit() {
  try {
    await addBooking(selectedDate.value, { source: 'calendar', deposit: true })
    showDialog.value = false
    showSnackbar.value = true
    selectedDate.value = ''
  } catch (error) {
    console.error('Booking error:', error)
    alert('Failed to book date. Please try again.')
  }
}

function formatDate(dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>

<style scoped>
.calendar-card {
  background: #fffbe6;
  border-radius: 16px;
  height: 100%;
}

.calendar-title {
  color: #b28704;
  font-size: 1.3rem;
  font-weight: 700;
}

@media (min-width: 600px) {
  .calendar-title {
    font-size: 1.5rem;
  }
}

.calendar-desc {
  color: #7a7a7a;
  font-size: 0.9rem;
}

@media (min-width: 600px) {
  .calendar-desc {
    font-size: 1rem;
  }
}

.dialog-card {
  background: #fffbe6;
  border-radius: 16px;
}

.deposit-info {
  border-left: 4px solid #b28704;
}

:deep(.v-date-picker) {
  width: 100%;
}

:deep(.v-date-picker-header) {
  color: #b28704;
}
</style>
