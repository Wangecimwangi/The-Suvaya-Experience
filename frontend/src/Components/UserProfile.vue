<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { ordersAPI, reservationsAPI } from '@/services/api'

const authStore = useAuthStore()
const router = useRouter()

const loading = ref(false)
const editMode = ref(false)
const recentOrders = ref([])
const recentReservations = ref([])

// Editable user data
const userData = ref({
  name: '',
  email: '',
  phone: ''
})

const stats = computed(() => ({
  totalOrders: recentOrders.value.length,
  totalReservations: recentReservations.value.length,
  pendingOrders: recentOrders.value.filter(o => o.status === 'pending').length,
  upcomingReservations: recentReservations.value.filter(r => new Date(r.date) > new Date()).length
}))

onMounted(async () => {
  if (!authStore.isLoggedIn || !authStore.user) {
    router.push('/login')
    return
  }

  // Load user data
  userData.value = {
    name: authStore.user.name || '',
    email: authStore.user.email || '',
    phone: authStore.user.phone || ''
  }

  // Load recent orders and reservations
  await loadRecentData()
})

async function loadRecentData() {
  loading.value = true
  try {
    // Fetch recent orders (limit to 3 most recent)
    const ordersResponse = await ordersAPI.getAll(authStore.user?.email)
    recentOrders.value = (ordersResponse.data || []).slice(0, 3)

    // Fetch recent reservations
    const reservationsResponse = await reservationsAPI.getAll()
    // Filter by user email and get 3 most recent
    recentReservations.value = (reservationsResponse.data || [])
      .filter(r => r.email === authStore.user?.email)
      .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      .slice(0, 3)
  } catch (error) {
    console.error('Error loading data:', error)
  } finally {
    loading.value = false
  }
}

function toggleEditMode() {
  editMode.value = !editMode.value
}

function saveProfile() {
  // TODO: Implement profile update API call
  authStore.user.name = userData.value.name
  authStore.user.phone = userData.value.phone
  editMode.value = false
  alert('Profile updated successfully!')
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

function formatPrice(price) {
  return new Intl.NumberFormat('en-KE', {
    style: 'currency',
    currency: 'KES'
  }).format(price)
}

function getStatusColor(status) {
  const colors = {
    pending: 'orange',
    confirmed: 'blue',
    delivered: 'green',
    cancelled: 'red'
  }
  return colors[status] || 'grey'
}

function handleLogout() {
  authStore.logout()
  router.push('/login')
}
</script>

<template>
  <v-container class="profile-container py-6 py-md-8">
    <v-row justify="center">
      <v-col cols="12" lg="10">
        <!-- Page Header -->
        <v-row class="mb-6">
          <v-col cols="12">
            <h1 class="page-title">
              <v-icon color="amber-darken-2" size="40" class="mr-2">mdi-account-circle</v-icon>
              My Profile
            </h1>
            <p class="page-subtitle">Manage your account and view your activity</p>
          </v-col>
        </v-row>

        <!-- Profile Information Card -->
        <v-row class="mb-6">
          <v-col cols="12">
            <v-card class="profile-card" elevation="4">
              <v-card-title class="card-header d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <v-icon color="amber-darken-2" class="mr-2">mdi-account</v-icon>
                  <span>Account Information</span>
                </div>
                <v-btn
                  v-if="!editMode"
                  color="amber-darken-2"
                  variant="outlined"
                  size="small"
                  prepend-icon="mdi-pencil"
                  @click="toggleEditMode"
                >
                  Edit
                </v-btn>
              </v-card-title>

              <v-divider></v-divider>

              <v-card-text class="pa-6">
                <v-form v-if="editMode">
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="userData.name"
                        label="Full Name"
                        variant="outlined"
                        color="amber-darken-2"
                        prepend-inner-icon="mdi-account"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="userData.email"
                        label="Email Address"
                        variant="outlined"
                        color="amber-darken-2"
                        prepend-inner-icon="mdi-email"
                        disabled
                        hint="Email cannot be changed"
                        persistent-hint
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="userData.phone"
                        label="Phone Number"
                        variant="outlined"
                        color="amber-darken-2"
                        prepend-inner-icon="mdi-phone"
                      ></v-text-field>
                    </v-col>
                  </v-row>

                  <div class="mt-4">
                    <v-btn
                      color="amber-darken-2"
                      class="mr-2"
                      @click="saveProfile"
                    >
                      Save Changes
                    </v-btn>
                    <v-btn
                      variant="outlined"
                      @click="toggleEditMode"
                    >
                      Cancel
                    </v-btn>
                  </div>
                </v-form>

                <div v-else>
                  <v-row>
                    <v-col cols="12" sm="6" md="4">
                      <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ userData.name || 'Not provided' }}</div>
                      </div>
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                      <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ userData.email }}</div>
                      </div>
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                      <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ userData.phone || 'Not provided' }}</div>
                      </div>
                    </v-col>
                  </v-row>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Statistics Cards -->
        <v-row class="mb-6">
          <v-col cols="6" sm="3">
            <v-card class="stat-card" elevation="2">
              <v-card-text class="text-center pa-4">
                <v-icon color="amber-darken-2" size="36" class="mb-2">mdi-receipt-text</v-icon>
                <div class="stat-value">{{ stats.totalOrders }}</div>
                <div class="stat-label">Total Orders</div>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="6" sm="3">
            <v-card class="stat-card" elevation="2">
              <v-card-text class="text-center pa-4">
                <v-icon color="orange" size="36" class="mb-2">mdi-clock-outline</v-icon>
                <div class="stat-value text-orange">{{ stats.pendingOrders }}</div>
                <div class="stat-label">Pending</div>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="6" sm="3">
            <v-card class="stat-card" elevation="2">
              <v-card-text class="text-center pa-4">
                <v-icon color="amber-darken-2" size="36" class="mb-2">mdi-calendar-check</v-icon>
                <div class="stat-value">{{ stats.totalReservations }}</div>
                <div class="stat-label">Reservations</div>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="6" sm="3">
            <v-card class="stat-card" elevation="2">
              <v-card-text class="text-center pa-4">
                <v-icon color="blue" size="36" class="mb-2">mdi-calendar-clock</v-icon>
                <div class="stat-value text-blue">{{ stats.upcomingReservations }}</div>
                <div class="stat-label">Upcoming</div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Quick Actions -->
        <v-row class="mb-6">
          <v-col cols="12">
            <v-card class="actions-card" elevation="3">
              <v-card-title class="card-header">
                <v-icon color="amber-darken-2" class="mr-2">mdi-flash</v-icon>
                Quick Actions
              </v-card-title>
              <v-divider></v-divider>
              <v-card-text class="pa-4">
                <v-row>
                  <v-col cols="6" sm="4" md="3">
                    <v-btn
                      color="amber-darken-2"
                      block
                      prepend-icon="mdi-storefront"
                      to="/menu"
                    >
                      Browse Menu
                    </v-btn>
                  </v-col>
                  <v-col cols="6" sm="4" md="3">
                    <v-btn
                      color="amber-darken-2"
                      variant="outlined"
                      block
                      prepend-icon="mdi-cart"
                      to="/cart"
                    >
                      View Cart
                    </v-btn>
                  </v-col>
                  <v-col cols="6" sm="4" md="3">
                    <v-btn
                      color="amber-darken-2"
                      variant="outlined"
                      block
                      prepend-icon="mdi-receipt-text"
                      to="/my-orders"
                    >
                      My Orders
                    </v-btn>
                  </v-col>
                  <v-col cols="6" sm="4" md="3">
                    <v-btn
                      color="amber-darken-2"
                      variant="outlined"
                      block
                      prepend-icon="mdi-calendar"
                      to="/my-reservations"
                    >
                      My Reservations
                    </v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Recent Orders -->
        <v-row class="mb-6">
          <v-col cols="12" md="6">
            <v-card class="recent-card" elevation="3">
              <v-card-title class="card-header d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <v-icon color="amber-darken-2" class="mr-2">mdi-receipt-text</v-icon>
                  <span>Recent Orders</span>
                </div>
                <v-btn
                  variant="text"
                  size="small"
                  color="amber-darken-2"
                  to="/my-orders"
                >
                  View All
                </v-btn>
              </v-card-title>
              <v-divider></v-divider>
              <v-card-text class="pa-0">
                <v-list v-if="recentOrders.length > 0" density="compact">
                  <v-list-item
                    v-for="order in recentOrders"
                    :key="order.id"
                    class="px-4 py-3"
                  >
                    <template v-slot:prepend>
                      <v-icon :color="getStatusColor(order.status)">mdi-receipt</v-icon>
                    </template>
                    <v-list-item-title class="font-weight-medium">
                      Order #{{ order.order_id }}
                    </v-list-item-title>
                    <v-list-item-subtitle>
                      {{ formatDate(order.created_at) }} • {{ formatPrice(order.subtotal) }}
                    </v-list-item-subtitle>
                    <template v-slot:append>
                      <v-chip :color="getStatusColor(order.status)" size="small">
                        {{ order.status }}
                      </v-chip>
                    </template>
                  </v-list-item>
                </v-list>
                <div v-else class="pa-8 text-center text-grey">
                  <v-icon size="64" color="grey-lighten-1">mdi-receipt-text-outline</v-icon>
                  <p class="mt-2">No orders yet</p>
                  <v-btn color="amber-darken-2" size="small" to="/menu">Start Shopping</v-btn>
                </div>
              </v-card-text>
            </v-card>
          </v-col>

          <!-- Recent Reservations -->
          <v-col cols="12" md="6">
            <v-card class="recent-card" elevation="3">
              <v-card-title class="card-header d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <v-icon color="amber-darken-2" class="mr-2">mdi-calendar-check</v-icon>
                  <span>Recent Reservations</span>
                </div>
                <v-btn
                  variant="text"
                  size="small"
                  color="amber-darken-2"
                  to="/my-reservations"
                >
                  View All
                </v-btn>
              </v-card-title>
              <v-divider></v-divider>
              <v-card-text class="pa-0">
                <v-list v-if="recentReservations.length > 0" density="compact">
                  <v-list-item
                    v-for="reservation in recentReservations"
                    :key="reservation.id"
                    class="px-4 py-3"
                  >
                    <template v-slot:prepend>
                      <v-icon :color="new Date(reservation.date) > new Date() ? 'blue' : 'grey'">
                        mdi-calendar
                      </v-icon>
                    </template>
                    <v-list-item-title class="font-weight-medium">
                      {{ formatDate(reservation.date) }} at {{ reservation.time }}
                    </v-list-item-title>
                    <v-list-item-subtitle>
                      {{ reservation.guests }} guests
                    </v-list-item-subtitle>
                    <template v-slot:append>
                      <v-chip
                        :color="new Date(reservation.date) > new Date() ? 'blue' : 'grey'"
                        size="small"
                      >
                        {{ new Date(reservation.date) > new Date() ? 'Upcoming' : 'Past' }}
                      </v-chip>
                    </template>
                  </v-list-item>
                </v-list>
                <div v-else class="pa-8 text-center text-grey">
                  <v-icon size="64" color="grey-lighten-1">mdi-calendar-blank-outline</v-icon>
                  <p class="mt-2">No reservations yet</p>
                  <v-btn color="amber-darken-2" size="small" to="/reservation">Book Now</v-btn>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Logout Button -->
        <v-row>
          <v-col cols="12" class="text-center">
            <v-btn
              color="red"
              variant="outlined"
              size="large"
              prepend-icon="mdi-logout"
              @click="handleLogout"
            >
              Logout
            </v-btn>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.profile-container {
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

@media (min-width: 960px) {
  .page-title {
    font-size: 2.5rem;
  }
}

.page-subtitle {
  color: #7a7a7a;
  font-size: 1rem;
  margin-top: 0.5rem;
}

.profile-card,
.stat-card,
.actions-card,
.recent-card {
  background: #fffbe6;
  border-radius: 16px;
}

.card-header {
  color: #b28704;
  font-weight: 700;
  font-size: 1.1rem;
  padding: 16px 24px;
}

.info-item {
  margin-bottom: 16px;
}

.info-label {
  color: #7a7a7a;
  font-size: 0.875rem;
  margin-bottom: 4px;
}

.info-value {
  color: #333;
  font-size: 1rem;
  font-weight: 500;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #b28704;
  margin: 8px 0 4px;
}

.stat-label {
  color: #7a7a7a;
  font-size: 0.875rem;
}

@media (max-width: 600px) {
  .page-title {
    font-size: 1.5rem;
  }

  .stat-value {
    font-size: 1.5rem;
  }
}
</style>
