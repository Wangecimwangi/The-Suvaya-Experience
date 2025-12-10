<script setup>
import { ref, onMounted, computed } from 'vue'
import { ordersAPI } from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const authStore = useAuthStore()
const cartStore = useCartStore()
const orders = ref([])
const loading = ref(false)
const filter = ref('all') // all, pending, confirmed, delivered, cancelled
const searchQuery = ref('')
const editDialog = ref(false)
const cancelDialog = ref(false)
const deleteDialog = ref(false)
const selectedOrder = ref(null)

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
    // Get orders for the logged-in user
    const userEmail = authStore.user?.email
    const response = userEmail
      ? await ordersAPI.getAll(userEmail)
      : await ordersAPI.getAll()

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

function openEditDialog(order) {
  selectedOrder.value = { ...order }
  editDialog.value = true
}

async function saveOrder() {
  try {
    const response = await fetch('http://localhost:8000/api/orders/update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(selectedOrder.value)
    })
    const result = await response.json()

    if (result.success) {
      alert('Order updated successfully!')
      editDialog.value = false
      await loadOrders()
    } else {
      alert(result.message || 'Failed to update order')
    }
  } catch (error) {
    console.error('Update error:', error)
    alert('Failed to update order. Please try again.')
  }
}

function openCancelDialog(order) {
  selectedOrder.value = order
  cancelDialog.value = true
}

async function confirmCancelOrder() {
  try {
    const response = await fetch('http://localhost:8000/api/orders/cancel.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: selectedOrder.value.id })
    })
    const result = await response.json()

    if (result.success) {
      alert('Order cancelled successfully!')
      cancelDialog.value = false
      await loadOrders()
    } else {
      alert(result.message || 'Failed to cancel order')
    }
  } catch (error) {
    console.error('Cancel error:', error)
    alert('Failed to cancel order. Please try again.')
  }
}

function openDeleteDialog(order) {
  selectedOrder.value = order
  deleteDialog.value = true
}

async function confirmDeleteOrder() {
  try {
    const response = await fetch('http://localhost:8000/api/orders/delete.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: selectedOrder.value.id })
    })
    const result = await response.json()

    if (result.success) {
      alert('Order deleted successfully!')
      deleteDialog.value = false
      await loadOrders()
    } else {
      alert(result.message || 'Failed to delete order')
    }
  } catch (error) {
    console.error('Delete error:', error)
    alert('Failed to delete order. Please try again.')
  }
}

function reorderItems(order) {
  // Clear current cart with confirmation
  if (confirm('This will replace your current cart with items from this order. Continue?')) {
    cartStore.clearCart()

    // Add each item from the order to cart
    order.items.forEach(item => {
      cartStore.addToCart({
        id: item.menu_item_id,
        name: item.item_name,
        price: item.price,
        quantity: item.quantity,
        image: '' // We don't have image in order items
      })
    })

    alert('Items added to cart! Redirecting to cart...')
    // Redirect to cart
    window.location.href = '/cart'
  }
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
              <div class="order-id">Order #{{ order.order_number }}</div>
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
                      {{ item.item_name }} x{{ item.quantity }}
                    </v-list-item-title>
                    <template v-slot:append>
                      <span class="text-body-2">{{ formatPrice(item.subtotal) }}</span>
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
                    <span class="text-grey">Total Amount:</span>
                    <span>{{ formatPrice(order.total_amount) }}</span>
                  </div>
                  <div class="d-flex justify-space-between mb-1">
                    <span class="text-green">Deposit Paid:</span>
                    <span class="text-green font-weight-bold">{{ formatPrice(order.deposit_amount) }}</span>
                  </div>
                  <div class="d-flex justify-space-between">
                    <span class="text-amber-darken-2">Balance Due:</span>
                    <span class="text-amber-darken-2 font-weight-bold">{{ formatPrice(order.balance_due) }}</span>
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

            <!-- Edit button - only for pending/confirmed orders -->
            <v-btn
              v-if="order.status !== 'cancelled' && order.status !== 'completed' && order.status !== 'delivered'"
              variant="text"
              color="blue"
              size="small"
              icon="mdi-pencil"
              @click="openEditDialog(order)"
            >
            </v-btn>

            <!-- Cancel button - only for pending/confirmed orders -->
            <v-btn
              v-if="order.status !== 'cancelled' && order.status !== 'completed' && order.status !== 'delivered'"
              variant="text"
              color="red"
              size="small"
              icon="mdi-close-circle"
              @click="openCancelDialog(order)"
            >
            </v-btn>

            <!-- Delete button - only for cancelled orders -->
            <v-btn
              v-if="order.status === 'cancelled'"
              variant="text"
              color="red"
              size="small"
              prepend-icon="mdi-delete"
              @click="openDeleteDialog(order)"
            >
              Delete
            </v-btn>

            <!-- Reorder button - for delivered or completed orders -->
            <v-btn
              v-if="order.status === 'delivered' || order.status === 'completed'"
              variant="outlined"
              color="amber-darken-2"
              prepend-icon="mdi-refresh"
              size="small"
              @click="reorderItems(order)"
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

    <!-- Edit Order Dialog -->
    <v-dialog v-model="editDialog" max-width="600">
      <v-card v-if="selectedOrder">
        <v-card-title class="bg-blue-lighten-5">
          <v-icon color="blue" class="mr-2">mdi-pencil</v-icon>
          Edit Order
        </v-card-title>

        <v-card-text class="pa-6">
          <v-form>
            <v-text-field
              v-model="selectedOrder.delivery_address"
              label="Delivery Address"
              variant="outlined"
              color="blue"
              class="mb-3"
            ></v-text-field>

            <v-text-field
              v-model="selectedOrder.delivery_date"
              label="Delivery Date"
              type="date"
              variant="outlined"
              color="blue"
              class="mb-3"
            ></v-text-field>

            <v-text-field
              v-model="selectedOrder.delivery_time"
              label="Delivery Time"
              type="time"
              variant="outlined"
              color="blue"
              class="mb-3"
            ></v-text-field>

            <v-textarea
              v-model="selectedOrder.notes"
              label="Special Instructions"
              variant="outlined"
              color="blue"
              rows="3"
            ></v-textarea>
          </v-form>
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="editDialog = false">Cancel</v-btn>
          <v-btn color="blue" @click="saveOrder">Save Changes</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Cancel Order Dialog -->
    <v-dialog v-model="cancelDialog" max-width="400">
      <v-card>
        <v-card-title class="bg-red-lighten-5">
          <v-icon color="red" class="mr-2">mdi-alert-circle</v-icon>
          Cancel Order
        </v-card-title>

        <v-card-text class="pa-6">
          <p>Are you sure you want to cancel this order?</p>
          <p class="text-caption text-grey mt-2">This action cannot be undone.</p>
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="cancelDialog = false">No, Keep It</v-btn>
          <v-btn color="red" @click="confirmCancelOrder">Yes, Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Order Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title class="bg-red-lighten-5">
          <v-icon color="red" class="mr-2">mdi-delete-alert</v-icon>
          Delete Order
        </v-card-title>

        <v-card-text class="pa-6">
          <p>Are you sure you want to permanently delete this order?</p>
          <p class="text-caption text-grey mt-2">This will remove the order from your history. This action cannot be undone.</p>
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="deleteDialog = false">No, Keep It</v-btn>
          <v-btn color="red" @click="confirmDeleteOrder">Yes, Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
