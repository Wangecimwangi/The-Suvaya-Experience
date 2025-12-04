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
    // Filter by current user
    reservations.value = (response.data || []).filter(r =>
      r.email === authStore.user?.email
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
            <span class="reservation-date">{{ formatDate(reservation.date) }}</span>
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
            >
              View Details
            </v-btn>

            <v-spacer></v-spacer>

            <v-btn
              v-if="!reservation.deposit_paid && reservation.status !== 'cancelled'"
              variant="tonal"
              color="green"
              size="small"
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

@media (max-width: 600px) {
  .page-title {
    font-size: 1.5rem;
  }

  .stat-number {
    font-size: 1.5rem;
  }
}
</style>
