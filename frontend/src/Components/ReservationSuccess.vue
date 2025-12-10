<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const reservation = ref(null)
const receiptDialog = ref(false)
const receiptData = ref(null)
const loading = ref(false)
const error = ref(null)
const successMessage = ref(null)

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

async function viewReceipt() {
  if (!reservation.value?.id) {
    error.value = 'Reservation ID not available'
    return
  }

  loading.value = true
  error.value = null
  successMessage.value = null

  try {
    const response = await fetch(`http://localhost:8000/api/reservations/get-receipt.php?id=${reservation.value.id}`)
    const result = await response.json()

    if (result.success) {
      receiptData.value = result.data.receipt
      // Directly download the receipt
      downloadReceipt()
      successMessage.value = 'Receipt downloaded successfully!'

      // Clear success message after 4 seconds
      setTimeout(() => {
        successMessage.value = null
      }, 4000)
    } else {
      error.value = result.message || 'Failed to load receipt'
    }
  } catch (err) {
    error.value = 'Unable to load receipt. Please try again.'
    console.error('Receipt fetch error:', err)
  } finally {
    loading.value = false
  }
}

function downloadReceipt() {
  if (!receiptData.value) {
    console.error('No receipt data available')
    return
  }

  console.log('Downloading receipt:', receiptData.value)

  const receiptContent = `
RESERVATION RECEIPT
The Suvaya Experience
====================

Confirmation Number: ${receiptData.value.confirmation_number}
Date Issued: ${new Date(receiptData.value.created_at).toLocaleString()}

RESERVATION DETAILS
-------------------
Name: ${receiptData.value.name}
Email: ${receiptData.value.email}
Phone: ${receiptData.value.phone}
Reservation Date: ${formatDate(receiptData.value.date)}
Time: ${receiptData.value.time}
Number of Guests: ${receiptData.value.guests}
Status: ${receiptData.value.status}
${receiptData.value.notes ? `\nSpecial Notes:\n${receiptData.value.notes}` : ''}

-------------------
Thank you for choosing The Suvaya Experience!
For inquiries, contact us at info@suvaya.com or +254 712 345 678
`

  try {
    const blob = new Blob([receiptContent], { type: 'text/plain' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `receipt-${receiptData.value.confirmation_number}.txt`
    document.body.appendChild(a)
    a.click()

    // Use setTimeout to ensure cleanup happens after download
    setTimeout(() => {
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    }, 100)

    console.log('Receipt download initiated')
  } catch (err) {
    console.error('Download failed:', err)
    error.value = 'Failed to download receipt'
  }
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

          <!-- Success Alert -->
          <v-alert
            v-if="successMessage"
            type="success"
            variant="tonal"
            class="mt-6"
            closable
            @click:close="successMessage = null"
          >
            <v-icon class="mr-2">mdi-check-circle</v-icon>
            {{ successMessage }}
          </v-alert>

          <!-- Error Alert -->
          <v-alert
            v-if="error"
            type="error"
            variant="tonal"
            class="mt-6"
            closable
            @click:close="error = null"
          >
            {{ error }}
          </v-alert>

          <!-- Action Buttons -->
          <v-row class="mt-6 action-buttons">
            <v-col cols="12" sm="4">
              <v-btn
                color="amber-darken-2"
                variant="flat"
                size="large"
                block
                @click="goToReservations"
                v-if="authStore.isLoggedIn"
              >
                <v-icon start size="small">mdi-calendar-multiple</v-icon>
                <span class="button-text">My Reservations</span>
              </v-btn>
              <v-btn
                v-else
                color="amber-darken-2"
                variant="flat"
                size="large"
                block
                @click="printReceipt"
              >
                <v-icon start size="small">mdi-printer</v-icon>
                <span class="button-text">Print</span>
              </v-btn>
            </v-col>

            <v-col cols="12" sm="4">
              <v-btn
                color="green-darken-1"
                variant="flat"
                size="large"
                block
                @click="viewReceipt"
                :loading="loading"
              >
                <v-icon start size="small">mdi-download</v-icon>
                <span class="button-text">Download Receipt</span>
              </v-btn>
            </v-col>

            <v-col cols="12" sm="4">
              <v-btn
                variant="outlined"
                color="amber-darken-2"
                size="large"
                block
                @click="goHome"
              >
                <v-icon start size="small">mdi-home</v-icon>
                <span class="button-text">Back to Home</span>
              </v-btn>
            </v-col>
          </v-row>
        </v-card>

        <!-- Package Recommendation CTA -->
        <v-card class="package-cta-card mt-4" elevation="3">
          <v-card-text class="pa-4 pa-md-6">
            <v-row align="center">
              <v-col cols="12" md="8">
                <div class="d-flex align-center mb-2">
                  <v-icon color="amber-darken-2" size="40" class="mr-3">mdi-lightbulb-on</v-icon>
                  <div>
                    <h3 class="cta-title">Need Items for Your Event?</h3>
                    <p class="cta-subtitle mb-0">Get personalized package recommendations</p>
                  </div>
                </div>
                <p class="cta-description mb-0">
                  Let our smart recommendation system suggest the perfect packages for your event.
                </p>
              </v-col>
              <v-col cols="12" md="4" class="text-center text-md-right">
                <v-btn
                  to="/package-recommendation"
                  color="amber-darken-2"
                  size="large"
                  prepend-icon="mdi-wizard-hat"
                  block
                >
                  Get Recommendations
                </v-btn>
              </v-col>
            </v-row>
          </v-card-text>
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

    <!-- Receipt Dialog -->
    <v-dialog v-model="receiptDialog" max-width="600px" scrollable>
      <v-card>
        <v-card-title class="d-flex align-center justify-space-between bg-amber-darken-2 text-white">
          <div class="d-flex align-center">
            <v-icon class="mr-2">mdi-receipt</v-icon>
            <span>Reservation Receipt</span>
          </div>
          <v-btn
            icon
            variant="text"
            size="small"
            @click="receiptDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text class="pa-6" v-if="receiptData">
          <div class="receipt-content">
            <div class="text-center mb-4">
              <h2 class="text-h5 font-weight-bold text-amber-darken-2">The Suvaya Experience</h2>
              <p class="text-caption text-grey-darken-1">Reservation Receipt</p>
            </div>

            <v-divider class="my-4"></v-divider>

            <div class="mb-4">
              <v-chip color="green" variant="flat" class="mb-2">
                {{ receiptData.confirmation_number }}
              </v-chip>
              <p class="text-caption text-grey-darken-1">
                Issued: {{ new Date(receiptData.created_at).toLocaleString() }}
              </p>
            </div>

            <v-divider class="my-4"></v-divider>

            <div class="receipt-details">
              <h3 class="text-subtitle-1 font-weight-bold mb-3 text-amber-darken-2">
                Reservation Details
              </h3>

              <v-row dense class="mb-2">
                <v-col cols="4" class="font-weight-medium">Name:</v-col>
                <v-col cols="8">{{ receiptData.name }}</v-col>
              </v-row>

              <v-row dense class="mb-2">
                <v-col cols="4" class="font-weight-medium">Email:</v-col>
                <v-col cols="8">{{ receiptData.email }}</v-col>
              </v-row>

              <v-row dense class="mb-2">
                <v-col cols="4" class="font-weight-medium">Phone:</v-col>
                <v-col cols="8">{{ receiptData.phone }}</v-col>
              </v-row>

              <v-row dense class="mb-2">
                <v-col cols="4" class="font-weight-medium">Date:</v-col>
                <v-col cols="8">{{ formatDate(receiptData.date) }}</v-col>
              </v-row>

              <v-row dense class="mb-2">
                <v-col cols="4" class="font-weight-medium">Time:</v-col>
                <v-col cols="8">{{ receiptData.time }}</v-col>
              </v-row>

              <v-row dense class="mb-2">
                <v-col cols="4" class="font-weight-medium">Guests:</v-col>
                <v-col cols="8">{{ receiptData.guests }}</v-col>
              </v-row>

              <v-row dense class="mb-2">
                <v-col cols="4" class="font-weight-medium">Status:</v-col>
                <v-col cols="8">
                  <v-chip
                    :color="receiptData.status === 'confirmed' ? 'green' : 'orange'"
                    size="small"
                    variant="flat"
                  >
                    {{ receiptData.status }}
                  </v-chip>
                </v-col>
              </v-row>

              <v-row v-if="receiptData.notes" dense class="mb-2">
                <v-col cols="4" class="font-weight-medium">Notes:</v-col>
                <v-col cols="8">{{ receiptData.notes }}</v-col>
              </v-row>
            </div>

            <v-divider class="my-4"></v-divider>

            <div class="text-center text-caption text-grey-darken-1">
              <p class="mb-1">Thank you for choosing The Suvaya Experience!</p>
              <p class="mb-0">For inquiries: info@suvaya.com | +254 712 345 678</p>
            </div>
          </div>
        </v-card-text>

        <v-card-actions class="px-6 pb-4">
          <v-btn
            color="amber-darken-2"
            variant="flat"
            prepend-icon="mdi-download"
            @click="downloadReceipt"
          >
            Download Receipt
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn
            variant="outlined"
            color="grey"
            @click="receiptDialog = false"
          >
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
  .v-timeline,
  .package-cta-card {
    display: none !important;
  }
}

.package-cta-card {
  background: linear-gradient(135deg, #fffbe6 0%, #fff8dc 100%);
  border-radius: 16px;
  border: 2px solid #b28704;
}

.cta-title {
  color: #b28704;
  font-size: 1.1rem;
  font-weight: 700;
}

@media (min-width: 960px) {
  .cta-title {
    font-size: 1.3rem;
  }
}

.cta-subtitle {
  color: #7a7a7a;
  font-size: 0.875rem;
}

@media (min-width: 960px) {
  .cta-subtitle {
    font-size: 0.95rem;
  }
}

.cta-description {
  color: #5a5a5a;
  font-size: 0.875rem;
  line-height: 1.6;
}

@media (min-width: 960px) {
  .cta-description {
    font-size: 0.95rem;
  }
}

.action-buttons .v-col {
  margin-bottom: 12px;
}

@media (min-width: 600px) {
  .action-buttons .v-col {
    margin-bottom: 0;
  }
}

.action-buttons .v-btn {
  min-height: 48px;
  text-transform: none;
  font-weight: 600;
  letter-spacing: 0.025em;
  white-space: normal;
  height: auto;
  padding: 12px 16px;
}

.action-buttons .button-text {
  display: inline-block;
  line-height: 1.2;
}

.action-buttons .v-btn :deep(.v-icon) {
  flex-shrink: 0;
}

@media (max-width: 959px) {
  .action-buttons .v-btn {
    font-size: 0.875rem;
  }
}
</style>
