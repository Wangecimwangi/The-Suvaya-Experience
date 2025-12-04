<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const reservation = ref(null)

onMounted(() => {
  // Get reservation data from route params or localStorage
  const reservationData = route.params.reservation || JSON.parse(localStorage.getItem('lastReservation') || '{}')

  if (!reservationData || !reservationData.date) {
    // No reservation data, redirect to reservation page
    router.push('/reservation')
    return
  }

  reservation.value = reservationData

  // Clear temporary storage
  localStorage.removeItem('lastReservation')
})

function formatDate(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function goToReservations() {
  router.push('/my-reservations')
}

function goHome() {
  router.push('/')
}

function printReceipt() {
  window.print()
}
</script>

<template>
  <v-container class="success-container py-8 py-md-12">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <!-- Success Icon -->
        <div class="text-center mb-6">
          <v-icon color="green" size="120" class="success-icon">
            mdi-check-circle
          </v-icon>
        </div>

        <!-- Success Message -->
        <v-card class="success-card pa-6 pa-md-8" elevation="4">
          <v-card-title class="text-center success-title mb-4">
            Reservation Confirmed!
          </v-card-title>

          <v-card-subtitle class="text-center mb-6">
            Your reservation has been successfully submitted
          </v-card-subtitle>

          <v-divider class="my-4"></v-divider>

          <!-- Reservation Details -->
          <div v-if="reservation" class="reservation-details">
            <h3 class="details-heading mb-4">
              <v-icon color="amber-darken-2" class="mr-2">mdi-calendar-check</v-icon>
              Reservation Details
            </h3>

            <v-row class="mb-2">
              <v-col cols="5" sm="4" class="font-weight-bold">Name:</v-col>
              <v-col cols="7" sm="8">{{ reservation.name }}</v-col>
            </v-row>

            <v-row class="mb-2">
              <v-col cols="5" sm="4" class="font-weight-bold">Email:</v-col>
              <v-col cols="7" sm="8">{{ reservation.email }}</v-col>
            </v-row>

            <v-row class="mb-2">
              <v-col cols="5" sm="4" class="font-weight-bold">Phone:</v-col>
              <v-col cols="7" sm="8">{{ reservation.phone }}</v-col>
            </v-row>

            <v-row class="mb-2">
              <v-col cols="5" sm="4" class="font-weight-bold">Date:</v-col>
              <v-col cols="7" sm="8">{{ formatDate(reservation.date) }}</v-col>
            </v-row>

            <v-row class="mb-2">
              <v-col cols="5" sm="4" class="font-weight-bold">Time:</v-col>
              <v-col cols="7" sm="8">{{ reservation.time }}</v-col>
            </v-row>

            <v-row class="mb-2">
              <v-col cols="5" sm="4" class="font-weight-bold">Guests:</v-col>
              <v-col cols="7" sm="8">{{ reservation.guests }} {{ reservation.guests > 1 ? 'people' : 'person' }}</v-col>
            </v-row>

            <v-row v-if="reservation.notes" class="mb-2">
              <v-col cols="5" sm="4" class="font-weight-bold">Notes:</v-col>
              <v-col cols="7" sm="8">{{ reservation.notes }}</v-col>
            </v-row>

            <v-divider class="my-4"></v-divider>

            <!-- Payment Info -->
            <v-alert type="info" variant="tonal" class="mb-4">
              <div class="d-flex align-center mb-2">
                <v-icon class="mr-2">mdi-information</v-icon>
                <strong>Payment Information</strong>
              </div>
              <p class="mb-1">A 50% deposit is required to confirm your reservation.</p>
              <p class="mb-0">The remaining balance will be due on the day of your event.</p>
            </v-alert>

            <v-alert
              v-if="reservation.deposit_paid"
              type="success"
              variant="tonal"
              class="mb-4"
            >
              <v-icon class="mr-2">mdi-check-circle</v-icon>
              Deposit payment confirmed!
            </v-alert>
          </div>

          <v-divider class="my-4"></v-divider>

          <!-- What's Next -->
          <div class="whats-next mb-4">
            <h3 class="details-heading mb-4">
              <v-icon color="amber-darken-2" class="mr-2">mdi-information-outline</v-icon>
              What's Next?
            </h3>

            <v-timeline side="end" density="compact">
              <v-timeline-item
                dot-color="green"
                size="small"
                icon="mdi-check"
              >
                <div class="mb-2">
                  <div class="font-weight-bold">Confirmation Email</div>
                  <div class="text-caption">You'll receive a confirmation email shortly</div>
                </div>
              </v-timeline-item>

              <v-timeline-item
                dot-color="amber-darken-2"
                size="small"
                icon="mdi-phone"
              >
                <div class="mb-2">
                  <div class="font-weight-bold">We'll Contact You</div>
                  <div class="text-caption">Our team will call to confirm final details</div>
                </div>
              </v-timeline-item>

              <v-timeline-item
                dot-color="amber-darken-2"
                size="small"
                icon="mdi-calendar-star"
              >
                <div class="mb-2">
                  <div class="font-weight-bold">Enjoy Your Event!</div>
                  <div class="text-caption">We'll make it special for you</div>
                </div>
              </v-timeline-item>
            </v-timeline>
          </div>

          <!-- Action Buttons -->
          <v-row class="mt-6">
            <v-col cols="12" sm="6" class="mb-2 mb-sm-0">
              <v-btn
                color="amber-darken-2"
                size="large"
                block
                @click="goToReservations"
                v-if="authStore.isLoggedIn"
              >
                <v-icon class="mr-2">mdi-calendar-multiple</v-icon>
                View My Reservations
              </v-btn>
              <v-btn
                v-else
                color="amber-darken-2"
                size="large"
                block
                @click="printReceipt"
              >
                <v-icon class="mr-2">mdi-printer</v-icon>
                Print Receipt
              </v-btn>
            </v-col>

            <v-col cols="12" sm="6">
              <v-btn
                variant="outlined"
                color="amber-darken-2"
                size="large"
                block
                @click="goHome"
              >
                <v-icon class="mr-2">mdi-home</v-icon>
                Back to Home
              </v-btn>
            </v-col>
          </v-row>
        </v-card>

        <!-- Contact Support -->
        <v-card class="mt-4 pa-4" elevation="2">
          <div class="text-center">
            <p class="mb-2">
              <strong>Need Help?</strong>
            </p>
            <p class="text-body-2 mb-3">
              Contact us at <a href="tel:+254712345678">+254 712 345 678</a> or
              <a href="mailto:info@suvaya.com">info@suvaya.com</a>
            </p>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.success-container {
  background: #f5f5f5;
  min-height: calc(100vh - 64px);
}

.success-icon {
  animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
  from {
    transform: scale(0);
  }
  to {
    transform: scale(1);
  }
}

.success-card {
  background: #fffbe6;
  border-radius: 16px;
}

.success-title {
  color: #2e7d32;
  font-size: 1.8rem;
  font-weight: 700;
}

.details-heading {
  color: #b28704;
  font-size: 1.1rem;
  font-weight: 600;
  display: flex;
  align-items: center;
}

.reservation-details .v-row {
  border-bottom: 1px solid #e0e0e0;
  padding: 8px 0;
}

.reservation-details .v-row:last-child {
  border-bottom: none;
}

@media (max-width: 600px) {
  .success-title {
    font-size: 1.5rem;
  }
}

@media print {
  .v-btn,
  .whats-next,
  .v-timeline {
    display: none !important;
  }
}
</style>
