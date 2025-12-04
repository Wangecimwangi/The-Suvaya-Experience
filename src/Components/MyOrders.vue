<script setup>
import { ref, onMounted, computed } from 'vue'
import { orderAPI } from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const orders = ref([])
const loading = ref(false)
const filter = ref('all') // all, pending, confirmed, delivered, cancelled
const searchQuery = ref('')

const stats = computed(() => {
  return {
    total: orders.value.length,
    pending: orders.value.filter(o => o.status === 'pending').length,
    confirmed: orders.value.filter(o => o.status === 'confirmed').length,
    delivered: orders.value.filter(o => o.status === 'delivered').length,
    cancelled: orders.value.filter(o => o.status === 'cancelled').length
  }
})

const filteredOrders = computed(() => {
  let filtered = orders.value

  // Filter by status
  if (filter.value !== 'all') {
    filtered = filtered.filter(o => o.status === filter.value)
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(o =>
      o.order_id?.toLowerCase().includes(query) ||
      o.customer_name?.toLowerCase().includes(query) ||
      o.items?.some(item => item.name?.toLowerCase().includes(query))
    )
  }

  // Sort by date (newest first)
  return filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

async function loadOrders() {
  loading.value = true
  try {
    const response = await orderAPI.getAll()
    orders.value = response.data || []
  } catch (error) {
    console.error('Error loading orders:', error)
    // Fallback to sample data if API fails
    orders.value = getSampleOrders()
  } finally {
    loading.value = false
  }
}

function getSampleOrders() {
  return [
    {
      id: 1,
      order_id: 'ORD-2025-001',
      customer_name: authStore.user?.name || 'John Doe',
      customer_email: authStore.user?.email || 'john@example.com',
      customer_phone: '254712345678',
      delivery_method: 'delivery',
      delivery_address: '123 Main St, Nairobi',
      delivery_date: '2025-01-15',
      delivery_time: '14:00',
      payment_method: 'mpesa',
      subtotal: 5000,
      deposit_amount: 2500,
      balance_amount: 2500,
      status: 'confirmed',
      created_at: '2025-01-01T10:30:00',
      items: [
        { id: 1, name: 'Chocolate Cake', quantity: 1, price: 3500 },
        { id: 2, name: 'Vanilla Cupcakes', quantity: 2, price: 750 }
      ]
    },
    {
      id: 2,
      order_id: 'ORD-2025-002',
      customer_name: authStore.user?.name || 'John Doe',
      customer_email: authStore.user?.email || 'john@example.com',
      customer_phone: '254712345678',
      delivery_method: 'pickup',
      delivery_address: 'Pickup at store',
      delivery_date: '2025-01-10',
      delivery_time: '10:00',
      payment_method: 'cash',
      subtotal: 1600,
      deposit_amount: 800,
      balance_amount: 800,
      status: 'pending',
      created_at: '2025-01-05T14:20:00',
      items: [
        { id: 3, name: 'Croissants', quantity: 2, price: 800 }
      ]
    }
  ]
}

function formatPrice(price) {
  return new Intl.NumberFormat('en-KE', {
    style: 'currency',
    currency: 'KES'
  }).format(price)
}

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

function formatDateTime(dateTimeString) {
  const date = new Date(dateTimeString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function getStatusColor(status) {
  const colors = {
    pending: 'orange',
    confirmed: 'blue',
    preparing: 'purple',
    ready: 'cyan',
    delivered: 'green',
    cancelled: 'red'
  }
  return colors[status] || 'grey'
}

function getStatusIcon(status) {
  const icons = {
    pending: 'mdi-clock-outline',
    confirmed: 'mdi-check-circle',
    preparing: 'mdi-chef-hat',
    ready: 'mdi-package-variant-closed',
    delivered: 'mdi-truck-check',
    cancelled: 'mdi-cancel'
  }
  return icons[status] || 'mdi-help-circle'
}

onMounted(() => {
  loadOrders()
})
</script>

<template>
  <v-container class="orders-container py-6 py-md-8">
    <!-- Page Header -->
    <v-row class="mb-6">
      <v-col cols="12">
        <h1 class="page-title">
          <v-icon color="amber-darken-2" size="40" class="mr-2">mdi-receipt-text</v-icon>
          My Orders
        </h1>
        <p class="page-subtitle">View and track all your orders</p>
      </v-col>
    </v-row>

    <!-- Statistics Cards -->
    <v-row class="mb-6">
      <v-col cols="6" sm="3" md="2">
        <v-card class="stat-card" elevation="2">
          <v-card-text class="text-center pa-3">
            <div class="stat-value">{{ stats.total }}</div>
            <div class="stat-label">Total</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="6" sm="3" md="2">
        <v-card class="stat-card" elevation="2">
          <v-card-text class="text-center pa-3">
            <div class="stat-value text-orange">{{ stats.pending }}</div>
            <div class="stat-label">Pending</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="6" sm="3" md="2">
        <v-card class="stat-card" elevation="2">
          <v-card-text class="text-center pa-3">
            <div class="stat-value text-blue">{{ stats.confirmed }}</div>
            <div class="stat-label">Confirmed</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="6" sm="3" md="2">
        <v-card class="stat-card" elevation="2">
          <v-card-text class="text-center pa-3">
            <div class="stat-value text-green">{{ stats.delivered }}</div>
            <div class="stat-label">Delivered</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Search and Filter -->
    <v-row class="mb-4">
      <v-col cols="12" md="6">
        <v-text-field
          v-model="searchQuery"
          label="Search orders..."
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          color="amber-darken-2"
          clearable
          hide-details
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-select
          v-model="filter"
          :items="[
            { title: 'All Orders', value: 'all' },
            { title: 'Pending', value: 'pending' },
            { title: 'Confirmed', value: 'confirmed' },
            { title: 'Delivered', value: 'delivered' },
            { title: 'Cancelled', value: 'cancelled' }
          ]"
          label="Filter by status"
          variant="outlined"
          color="amber-darken-2"
          hide-details
        ></v-select>
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
        <p class="mt-4">Loading orders...</p>
      </v-col>
    </v-row>

    <!-- Orders List -->
    <v-row v-else-if="filteredOrders.length > 0">
      <v-col cols="12">
        <v-card
          v-for="order in filteredOrders"
          :key="order.id"
          class="order-card mb-4"
          elevation="3"
        >
          <!-- Order Header -->
          <v-card-title class="order-header d-flex align-center flex-wrap">
            <div class="flex-grow-1 mb-2 mb-md-0">
              <div class="order-id">Order #{{ order.order_id }}</div>
              <div class="order-date">Placed on {{ formatDateTime(order.created_at) }}</div>
            </div>
            <v-chip
              :color="getStatusColor(order.status)"
              size="large"
              class="status-chip"
            >
              <v-icon start>{{ getStatusIcon(order.status) }}</v-icon>
              {{ order.status }}
            </v-chip>
          </v-card-title>

          <v-divider></v-divider>

          <v-card-text class="px-4 px-md-6 py-4">
            <v-row>
              <!-- Order Items -->
              <v-col cols="12" md="6">
                <h4 class="section-title mb-3">Items</h4>
                <v-list density="compact" class="items-list">
                  <v-list-item
                    v-for="item in order.items"
                    :key="item.id"
                    class="px-0 py-1"
                  >
                    <v-list-item-title class="text-body-2">
                      {{ item.name }} x{{ item.quantity }}
                    </v-list-item-title>
                    <template v-slot:append>
                      <span class="text-body-2">{{ formatPrice(item.price * item.quantity) }}</span>
                    </template>
                  </v-list-item>
                </v-list>
              </v-col>

              <!-- Delivery & Payment Info -->
              <v-col cols="12" md="6">
                <h4 class="section-title mb-3">Delivery & Payment</h4>
                <div class="info-item mb-2">
                  <v-icon size="small" color="amber-darken-2" class="mr-2">
                    {{ order.delivery_method === 'delivery' ? 'mdi-truck' : 'mdi-store' }}
                  </v-icon>
                  <span class="text-capitalize">{{ order.delivery_method }}</span>
                </div>

                <div class="info-item mb-2" v-if="order.delivery_method === 'delivery'">
                  <v-icon size="small" color="amber-darken-2" class="mr-2">mdi-map-marker</v-icon>
                  <span>{{ order.delivery_address }}</span>
                </div>

                <div class="info-item mb-2">
                  <v-icon size="small" color="amber-darken-2" class="mr-2">mdi-calendar</v-icon>
                  <span>{{ formatDate(order.delivery_date) }} at {{ order.delivery_time }}</span>
                </div>

                <div class="info-item mb-3">
                  <v-icon size="small" color="amber-darken-2" class="mr-2">mdi-credit-card</v-icon>
                  <span class="text-capitalize">{{ order.payment_method === 'mpesa' ? 'M-Pesa' : 'Cash' }}</span>
                </div>

                <v-divider class="my-3"></v-divider>

                <div class="price-summary">
                  <div class="d-flex justify-space-between mb-1">
                    <span class="text-grey">Subtotal:</span>
                    <span>{{ formatPrice(order.subtotal) }}</span>
                  </div>
                  <div class="d-flex justify-space-between mb-1">
                    <span class="text-green">Deposit Paid:</span>
                    <span class="text-green font-weight-bold">{{ formatPrice(order.deposit_amount) }}</span>
                  </div>
                  <div class="d-flex justify-space-between">
                    <span class="text-amber-darken-2">Balance Due:</span>
                    <span class="text-amber-darken-2 font-weight-bold">{{ formatPrice(order.balance_amount) }}</span>
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-card-text>

          <v-divider></v-divider>

          <!-- Order Actions -->
          <v-card-actions class="px-4 px-md-6 py-3">
            <v-btn
              variant="text"
              color="amber-darken-2"
              prepend-icon="mdi-phone"
              size="small"
            >
              Contact Support
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn
              variant="outlined"
              color="amber-darken-2"
              prepend-icon="mdi-refresh"
              size="small"
              v-if="order.status === 'delivered'"
            >
              Reorder
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Empty State -->
    <v-row v-else>
      <v-col cols="12">
        <v-card class="empty-state pa-8 pa-md-12" elevation="2">
          <div class="text-center">
            <v-icon color="grey-lighten-1" size="120" class="mb-6">
              mdi-receipt-text-outline
            </v-icon>
            <h2 class="empty-title mb-3">No Orders Found</h2>
            <p class="empty-subtitle mb-6">
              {{ searchQuery || filter !== 'all'
                ? 'Try adjusting your search or filter'
                : "You haven't placed any orders yet. Start browsing our delicious menu!" }}
            </p>
            <v-btn
              color="amber-darken-2"
              size="large"
              prepend-icon="mdi-storefront"
              to="/menu"
            >
              Browse Menu
            </v-btn>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.orders-container {
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

.stat-card {
  background: #fffbe6;
  border-radius: 12px;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: #b28704;
}

.stat-label {
  font-size: 0.875rem;
  color: #7a7a7a;
  margin-top: 4px;
}

.order-card {
  background: #fffbe6;
  border-radius: 16px;
}

.order-header {
  padding: 16px 24px;
}

.order-id {
  color: #b28704;
  font-size: 1.2rem;
  font-weight: 700;
}

.order-date {
  color: #7a7a7a;
  font-size: 0.875rem;
  margin-top: 2px;
}

.status-chip {
  text-transform: capitalize;
  font-weight: 600;
}

.section-title {
  color: #b28704;
  font-size: 1rem;
  font-weight: 600;
}

.items-list {
  background: transparent;
}

.info-item {
  color: #333;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
}

.price-summary {
  font-size: 0.95rem;
}

.empty-state {
  background: #fffbe6;
  border-radius: 16px;
}

.empty-title {
  color: #b28704;
  font-size: 2rem;
  font-weight: 700;
}

.empty-subtitle {
  color: #7a7a7a;
  font-size: 1.1rem;
}

@media (max-width: 600px) {
  .page-title {
    font-size: 1.5rem;
  }

  .order-id {
    font-size: 1rem;
  }

  .empty-title {
    font-size: 1.5rem;
  }

  .stat-value {
    font-size: 1.5rem;
  }
}
</style>
