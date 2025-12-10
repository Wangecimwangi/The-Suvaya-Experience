<script setup>
import { ref, onMounted, computed } from 'vue'
import { reservationsAPI } from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const router = useRouter()
const authStore = useAuthStore()

const reservations = ref([])
const loading = ref(false)
const filter = ref('all') // all, upcoming, past, cancelled
const searchQuery = ref('')

// Dialogs
const detailsDialog = ref(false)
const paymentDialog = ref(false)
const editDialog = ref(false)
const cancelDialog = ref(false)
const selectedReservation = ref(null)

// Payment form
const showCardDialog = ref(false)
const cardNumber = ref('')
const cardName = ref('')
const cardExpiry = ref('')
const cardCVV = ref('')
const processingPayment = ref(false)

// Redirect if not logged in
if (!authStore.isLoggedIn) {
  router.push('/login')
}

const filteredReservations = computed(() => {
  let filtered = reservations.value

  // Filter by status
  if (filter.value === 'upcoming') {
    filtered = filtered.filter(r => {
      const resDate = new Date(r.date)
      return resDate >= new Date() && r.status !== 'cancelled' && r.status !== 'completed'
    })
  } else if (filter.value === 'past') {
    filtered = filtered.filter(r => {
      const resDate = new Date(r.date)
      return resDate < new Date() || r.status === 'completed'
    })
  } else if (filter.value === 'cancelled') {
    filtered = filtered.filter(r => r.status === 'cancelled')
  }

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(r =>
      r.id?.toString().includes(query) ||
      r.name?.toLowerCase().includes(query) ||
      r.date?.includes(query) ||
      r.notes?.toLowerCase().includes(query)
    )
  }

  // Sort by date (newest first)
  return filtered.sort((a, b) => new Date(b.date) - new Date(a.date))
})

const stats = computed(() => {
  return {
    total: reservations.value.length,
    upcoming: reservations.value.filter(r => {
      const resDate = new Date(r.date)
      return resDate >= new Date() && r.status !== 'cancelled'
    }).length,
    past: reservations.value.filter(r => {
      const resDate = new Date(r.date)
      return resDate < new Date() || r.status === 'completed'
    }).length,
    cancelled: reservations.value.filter(r => r.status === 'cancelled').length
  }
})

async function loadReservations() {
  loading.value = true
  try {
    const response = await reservationsAPI.getAll()
    const allReservations = response.data || []

    // Filter by current user email
    const userEmail = authStore.user?.email

    if (!userEmail) {
      reservations.value = []
      return
    }

    // Filter reservations by matching email (case-insensitive)
    reservations.value = allReservations.filter(r =>
      r.email?.toLowerCase() === userEmail.toLowerCase()
    )
  } catch (error) {
    console.error('Error loading reservations:', error)
  } finally {
    loading.value = false
  }
}

function getStatusColor(status) {
  switch(status?.toLowerCase()) {
    case 'pending': return 'orange'
    case 'confirmed': return 'green'
    case 'completed': return 'blue'
    case 'cancelled': return 'red'
    default: return 'grey'
  }
}

function formatDate(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

function goToNewReservation() {
  router.push('/reservation')
}

function viewDetails(reservation) {
  selectedReservation.value = reservation
  detailsDialog.value = true
}

function openPaymentDialog(reservation) {
  selectedReservation.value = reservation
  paymentDialog.value = true
}

async function processCardPayment() {
  processingPayment.value = true

  // Simulate payment processing
  await new Promise(resolve => setTimeout(resolve, 2000))

  try {
    // TODO: Add actual payment API call here
    // For now, we'll just update the reservation status
    const index = reservations.value.findIndex(r => r.id === selectedReservation.value.id)
    if (index !== -1) {
      reservations.value[index].deposit_paid = true
    }

    alert('Payment successful! Your deposit has been recorded.')
    showCardDialog.value = false
    paymentDialog.value = false

    // Reset form
    cardNumber.value = ''
    cardName.value = ''
    cardExpiry.value = ''
    cardCVV.value = ''
  } catch (error) {
    console.error('Payment error:', error)
    alert('Payment failed. Please try again.')
  } finally {
    processingPayment.value = false
  }
}

function payWithCard() {
  showCardDialog.value = true
}

function payWithMpesa() {
  alert('M-Pesa Payment:\n\n1. Go to M-Pesa menu\n2. Select "Lipa na M-Pesa"\n3. Select "Buy Goods and Services"\n4. Enter Till Number: 5858585\n5. Enter the deposit amount\n6. Enter your PIN\n\nYour deposit will be confirmed once payment is verified.')
  paymentDialog.value = false
}

function payWithCash() {
  alert('Cash payment: Please bring the deposit amount when you arrive for your reservation.')
  paymentDialog.value = false
}

function openEditDialog(reservation) {
  selectedReservation.value = { ...reservation }
  editDialog.value = true
}

async function saveReservation() {
  try {
    const response = await fetch('http://localhost:8000/api/reservations/update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(selectedReservation.value)
    })
    const result = await response.json()

    if (result.success) {
      alert('Reservation updated successfully!')
      editDialog.value = false
      await loadReservations()
    } else {
      alert(result.message || 'Failed to update reservation')
    }
  } catch (error) {
    console.error('Update error:', error)
    alert('Failed to update reservation. Please try again.')
  }
}

function openCancelDialog(reservation) {
  selectedReservation.value = reservation
  cancelDialog.value = true
}

async function confirmCancelReservation() {
  try {
    const response = await fetch('http://localhost:8000/api/reservations/cancel.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: selectedReservation.value.id })
    })
    const result = await response.json()

    if (result.success) {
      alert('Reservation cancelled successfully!')
      cancelDialog.value = false
      await loadReservations()
    } else {
      alert(result.message || 'Failed to cancel reservation')
    }
  } catch (error) {
    console.error('Cancel error:', error)
    alert('Failed to cancel reservation. Please try again.')
  }
}

onMounted(() => {
  loadReservations()
})
</script>

<template>
  <v-container class="my-reservations-container py-6 py-md-8">
    <!-- Page Header -->
    <v-row class="mb-6">
      <v-col cols="12">
        <h1 class="page-title">
          <v-icon color="amber-darken-2" size="36" class="mr-2">mdi-calendar-multiple</v-icon>
          My Reservations
        </h1>
        <p class="page-subtitle">View and manage all your reservations</p>
      </v-col>
    </v-row>

    <!-- Stats Cards -->
    <v-row class="mb-4">
      <v-col cols="6" sm="3">
        <v-card class="stat-card" elevation="2" :class="{ active: filter === 'all' }" @click="filter = 'all'">
          <v-card-text class="text-center">
            <div class="stat-number">{{ stats.total }}</div>
            <div class="stat-label">Total</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="6" sm="3">
        <v-card class="stat-card" elevation="2" :class="{ active: filter === 'upcoming' }" @click="filter = 'upcoming'">
          <v-card-text class="text-center">
            <div class="stat-number text-green">{{ stats.upcoming }}</div>
            <div class="stat-label">Upcoming</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="6" sm="3">
        <v-card class="stat-card" elevation="2" :class="{ active: filter === 'past' }" @click="filter = 'past'">
          <v-card-text class="text-center">
            <div class="stat-number text-blue">{{ stats.past }}</div>
            <div class="stat-label">Past</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="6" sm="3">
        <v-card class="stat-card" elevation="2" :class="{ active: filter === 'cancelled' }" @click="filter = 'cancelled'">
          <v-card-text class="text-center">
            <div class="stat-number text-red">{{ stats.cancelled }}</div>
            <div class="stat-label">Cancelled</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Search and New Reservation Button -->
    <v-row class="mb-4">
      <v-col cols="12" md="8">
        <v-text-field
          v-model="searchQuery"
          label="Search reservations..."
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          color="amber-darken-2"
          clearable
          hide-details
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <v-btn
          color="amber-darken-2"
          size="large"
          block
          @click="goToNewReservation"
        >
          <v-icon class="mr-2">mdi-plus</v-icon>
          New Reservation
        </v-btn>
      </v-col>
    </v-row>

    <!-- Loading State -->
    <v-row v-if="loading">
      <v-col cols="12" class="text-center py-12">
        <v-progress-circular
          indeterminate
          color="amber-darken-2"
          size="64"
        ></v-progress-circular>
        <p class="mt-4">Loading reservations...</p>
      </v-col>
    </v-row>

    <!-- Reservations List -->
    <v-row v-else-if="filteredReservations.length > 0">
      <v-col
        v-for="reservation in filteredReservations"
        :key="reservation.id"
        cols="12"
        md="6"
        lg="4"
      >
        <v-card class="reservation-card" elevation="3">
          <v-card-title class="d-flex justify-space-between align-center">
            <div>
              <div class="reservation-id">ID: #{{ reservation.id }}</div>
              <span class="reservation-date">{{ formatDate(reservation.date) }}</span>
            </div>
            <v-chip :color="getStatusColor(reservation.status)" size="small">
              {{ reservation.status }}
            </v-chip>
          </v-card-title>

          <v-card-text>
            <div class="reservation-info">
              <div class="info-row">
                <v-icon size="small" color="amber-darken-2">mdi-clock-outline</v-icon>
                <span>{{ reservation.time }}</span>
              </div>

              <div class="info-row">
                <v-icon size="small" color="amber-darken-2">mdi-account-group</v-icon>
                <span>{{ reservation.guests }} {{ reservation.guests > 1 ? 'guests' : 'guest' }}</span>
              </div>

              <div class="info-row">
                <v-icon size="small" color="amber-darken-2">mdi-phone</v-icon>
                <span>{{ reservation.phone }}</span>
              </div>

              <div v-if="reservation.notes" class="info-row">
                <v-icon size="small" color="amber-darken-2">mdi-note-text</v-icon>
                <span class="text-truncate">{{ reservation.notes }}</span>
              </div>

              <v-divider class="my-3"></v-divider>

              <div class="d-flex justify-space-between align-center">
                <div>
                  <div class="text-caption">Deposit</div>
                  <div class="font-weight-bold">
                    {{ reservation.deposit_paid ? 'Paid' : 'Pending' }}
                  </div>
                </div>

                <v-icon
                  :color="reservation.deposit_paid ? 'green' : 'orange'"
                  size="large"
                >
                  {{ reservation.deposit_paid ? 'mdi-check-circle' : 'mdi-clock-alert' }}
                </v-icon>
              </div>
            </div>
          </v-card-text>

          <v-card-actions>
            <v-btn
              variant="text"
              color="amber-darken-2"
              size="small"
              @click="viewDetails(reservation)"
            >
              View Details
            </v-btn>

            <v-spacer></v-spacer>

            <v-btn
              v-if="reservation.status !== 'cancelled' && reservation.status !== 'completed'"
              variant="text"
              color="blue"
              size="small"
              icon="mdi-pencil"
              @click="openEditDialog(reservation)"
            >
            </v-btn>

            <v-btn
              v-if="reservation.status !== 'cancelled' && reservation.status !== 'completed'"
              variant="text"
              color="red"
              size="small"
              icon="mdi-close-circle"
              @click="openCancelDialog(reservation)"
            >
            </v-btn>

            <v-btn
              v-if="!reservation.deposit_paid && reservation.status !== 'cancelled'"
              variant="tonal"
              color="green"
              size="small"
              @click="openPaymentDialog(reservation)"
            >
              Pay Deposit
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Empty State -->
    <v-row v-else>
      <v-col cols="12">
        <v-card class="empty-state pa-8" elevation="2">
          <div class="text-center">
            <v-icon color="grey" size="80" class="mb-4">
              mdi-calendar-blank
            </v-icon>
            <h3 class="mb-2">No Reservations Found</h3>
            <p class="text-grey mb-4">
              {{ searchQuery ? 'Try adjusting your search' : 'You haven\'t made any reservations yet' }}
            </p>
            <v-btn
              v-if="!searchQuery"
              color="amber-darken-2"
              size="large"
              @click="goToNewReservation"
            >
              <v-icon class="mr-2">mdi-plus</v-icon>
              Make Your First Reservation
            </v-btn>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- View Details Dialog -->
    <v-dialog v-model="detailsDialog" max-width="600">
      <v-card v-if="selectedReservation">
        <v-card-title class="d-flex align-center justify-space-between bg-amber-lighten-5">
          <div class="d-flex align-center">
            <v-icon color="amber-darken-2" class="mr-2">mdi-calendar-check</v-icon>
            <span>Reservation Details</span>
          </div>
          <v-btn icon size="small" variant="text" @click="detailsDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text class="pa-6">
          <v-row dense>
            <v-col cols="12">
              <div class="detail-item">
                <div class="detail-label">Reservation ID</div>
                <div class="detail-value">
                  <v-chip color="amber-darken-2" size="small">#{{ selectedReservation.id }}</v-chip>
                </div>
              </div>
            </v-col>

            <v-col cols="12">
              <div class="detail-item">
                <div class="detail-label">Date & Time</div>
                <div class="detail-value">
                  {{ formatDate(selectedReservation.date) }} at {{ selectedReservation.time }}
                </div>
              </div>
            </v-col>

            <v-col cols="12" sm="6">
              <div class="detail-item">
                <div class="detail-label">Name</div>
                <div class="detail-value">{{ selectedReservation.name }}</div>
              </div>
            </v-col>

            <v-col cols="12" sm="6">
              <div class="detail-item">
                <div class="detail-label">Number of Guests</div>
                <div class="detail-value">{{ selectedReservation.guests }}</div>
              </div>
            </v-col>

            <v-col cols="12" sm="6">
              <div class="detail-item">
                <div class="detail-label">Email</div>
                <div class="detail-value">{{ selectedReservation.email }}</div>
              </div>
            </v-col>

            <v-col cols="12" sm="6">
              <div class="detail-item">
                <div class="detail-label">Phone</div>
                <div class="detail-value">{{ selectedReservation.phone }}</div>
              </div>
            </v-col>

            <v-col cols="12">
              <div class="detail-item">
                <div class="detail-label">Status</div>
                <v-chip :color="getStatusColor(selectedReservation.status)" size="small">
                  {{ selectedReservation.status }}
                </v-chip>
              </div>
            </v-col>

            <v-col cols="12">
              <div class="detail-item">
                <div class="detail-label">Deposit Status</div>
                <v-chip :color="selectedReservation.deposit_paid ? 'green' : 'orange'" size="small">
                  {{ selectedReservation.deposit_paid ? 'Paid' : 'Pending' }}
                </v-chip>
              </div>
            </v-col>

            <v-col cols="12" v-if="selectedReservation.notes">
              <div class="detail-item">
                <div class="detail-label">Special Notes</div>
                <div class="detail-value">{{ selectedReservation.notes }}</div>
              </div>
            </v-col>
          </v-row>
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn color="amber-darken-2" @click="detailsDialog = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Payment Method Dialog -->
    <v-dialog v-model="paymentDialog" max-width="500">
      <v-card v-if="selectedReservation">
        <v-card-title class="bg-amber-lighten-5">
          <v-icon color="amber-darken-2" class="mr-2">mdi-cash-multiple</v-icon>
          Pay Deposit
        </v-card-title>

        <v-card-text class="pa-6">
          <p class="text-body-1 mb-4">
            Select a payment method for your reservation deposit:
          </p>

          <v-btn
            color="blue"
            variant="tonal"
            block
            size="large"
            class="mb-3"
            prepend-icon="mdi-credit-card"
            @click="payWithCard"
          >
            Pay with Card
          </v-btn>

          <v-btn
            color="green"
            variant="tonal"
            block
            size="large"
            class="mb-3"
            prepend-icon="mdi-cellphone"
            @click="payWithMpesa"
          >
            Pay with M-Pesa
          </v-btn>

          <v-btn
            color="amber-darken-2"
            variant="tonal"
            block
            size="large"
            prepend-icon="mdi-cash"
            @click="payWithCash"
          >
            Pay Cash Later
          </v-btn>
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="paymentDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Card Payment Dialog -->
    <v-dialog v-model="showCardDialog" max-width="500" persistent>
      <v-card>
        <v-card-title class="bg-blue-lighten-5">
          <v-icon color="blue" class="mr-2">mdi-credit-card</v-icon>
          Card Payment
        </v-card-title>

        <v-card-text class="pa-6">
          <v-form>
            <v-text-field
              v-model="cardNumber"
              label="Card Number"
              placeholder="1234 5678 9012 3456"
              variant="outlined"
              color="blue"
              prepend-inner-icon="mdi-credit-card-outline"
              :disabled="processingPayment"
            ></v-text-field>

            <v-text-field
              v-model="cardName"
              label="Cardholder Name"
              placeholder="John Doe"
              variant="outlined"
              color="blue"
              prepend-inner-icon="mdi-account"
              :disabled="processingPayment"
            ></v-text-field>

            <v-row>
              <v-col cols="6">
                <v-text-field
                  v-model="cardExpiry"
                  label="Expiry Date"
                  placeholder="MM/YY"
                  variant="outlined"
                  color="blue"
                  prepend-inner-icon="mdi-calendar"
                  :disabled="processingPayment"
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model="cardCVV"
                  label="CVV"
                  placeholder="123"
                  variant="outlined"
                  color="blue"
                  prepend-inner-icon="mdi-lock"
                  type="password"
                  :disabled="processingPayment"
                ></v-text-field>
              </v-col>
            </v-row>

            <v-alert type="info" variant="tonal" class="mt-2">
              This is a simulated payment. Your card will not be charged.
            </v-alert>
          </v-form>
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn
            variant="text"
            @click="showCardDialog = false; paymentDialog = true"
            :disabled="processingPayment"
          >
            Back
          </v-btn>
          <v-btn
            color="blue"
            @click="processCardPayment"
            :loading="processingPayment"
          >
            {{ processingPayment ? 'Processing...' : 'Pay Now' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Edit Reservation Dialog -->
    <v-dialog v-model="editDialog" max-width="600">
      <v-card v-if="selectedReservation">
        <v-card-title class="bg-blue-lighten-5">
          <v-icon color="blue" class="mr-2">mdi-pencil</v-icon>
          Edit Reservation
        </v-card-title>

        <v-card-text class="pa-6">
          <v-form>
            <v-text-field
              v-model="selectedReservation.name"
              label="Name"
              variant="outlined"
              color="blue"
              class="mb-3"
            ></v-text-field>

            <v-text-field
              v-model="selectedReservation.phone"
              label="Phone"
              variant="outlined"
              color="blue"
              class="mb-3"
            ></v-text-field>

            <v-text-field
              v-model="selectedReservation.date"
              label="Date"
              type="date"
              variant="outlined"
              color="blue"
              class="mb-3"
            ></v-text-field>

            <v-text-field
              v-model="selectedReservation.time"
              label="Time"
              type="time"
              variant="outlined"
              color="blue"
              class="mb-3"
            ></v-text-field>

            <v-text-field
              v-model.number="selectedReservation.guests"
              label="Number of Guests"
              type="number"
              variant="outlined"
              color="blue"
              class="mb-3"
            ></v-text-field>

            <v-textarea
              v-model="selectedReservation.notes"
              label="Notes"
              variant="outlined"
              color="blue"
              rows="3"
            ></v-textarea>
          </v-form>
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="editDialog = false">Cancel</v-btn>
          <v-btn color="blue" @click="saveReservation">Save Changes</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Cancel Reservation Dialog -->
    <v-dialog v-model="cancelDialog" max-width="400">
      <v-card>
        <v-card-title class="bg-red-lighten-5">
          <v-icon color="red" class="mr-2">mdi-alert-circle</v-icon>
          Cancel Reservation
        </v-card-title>

        <v-card-text class="pa-6">
          <p>Are you sure you want to cancel this reservation?</p>
          <p class="text-caption text-grey mt-2">This action cannot be undone.</p>
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="cancelDialog = false">No, Keep It</v-btn>
          <v-btn color="red" @click="confirmCancelReservation">Yes, Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<style scoped>
.my-reservations-container {
  background: #f5f5f5;
  min-height: calc(100vh - 64px);
}

.page-title {
  color: #b28704;
  font-size: 2rem;
  font-weight: 700;
  display: flex;
  align-items: center;
}

.page-subtitle {
  color: #7a7a7a;
  font-size: 1rem;
  margin-top: 0.5rem;
}

.stat-card {
  background: #fffbe6;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
}

.stat-card.active {
  border-color: #b28704;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #b28704;
}

.stat-label {
  font-size: 0.875rem;
  color: #7a7a7a;
  margin-top: 0.25rem;
}

.reservation-card {
  background: #fffbe6;
  border-radius: 12px;
  height: 100%;
}

.reservation-id {
  font-size: 0.75rem;
  color: #7a7a7a;
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.reservation-date {
  color: #b28704;
  font-size: 1.1rem;
  font-weight: 600;
}

.reservation-info {
  font-size: 0.9rem;
}

.info-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.empty-state {
  background: #fffbe6;
  border-radius: 16px;
}

.detail-item {
  margin-bottom: 16px;
}

.detail-label {
  font-size: 0.875rem;
  color: #7a7a7a;
  margin-bottom: 4px;
  font-weight: 600;
}

.detail-value {
  font-size: 1rem;
  color: #333;
}

@media (max-width: 600px) {
  .page-title {
    font-size: 1.5rem;
  }

  .stat-number {
    font-size: 1.5rem;
  }
}
</style>
