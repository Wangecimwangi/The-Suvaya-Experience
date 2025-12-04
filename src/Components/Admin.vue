<script setup>
import { ref, onMounted, computed } from 'vue'
import { reservationsAPI, ordersAPI, menuAPI, eventsAPI, contactAPI, authAPI } from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const router = useRouter()
const authStore = useAuthStore()

// Check if user is admin
if (!authStore.isAdmin) {
  router.push('/')
}

const currentTab = ref('dashboard')
const loading = ref(false)

// Dashboard Statistics
const stats = ref({
  totalReservations: 0,
  totalOrders: 0,
  totalRevenue: 0,
  totalUsers: 0,
  pendingReservations: 0,
  pendingOrders: 0,
  contactMessages: 0,
  upcomingEvents: 0
})

// Data for each section
const reservations = ref([])
const orders = ref([])
const menuItems = ref([])
const events = ref([])
const users = ref([])
const contactMessages = ref([])

// Search and filters
const reservationSearch = ref('')
const orderSearch = ref('')
const menuSearch = ref('')
const eventSearch = ref('')
const userSearch = ref('')
const messageSearch = ref('')

// Dialogs
const showReservationDialog = ref(false)
const showOrderDialog = ref(false)
const showMenuDialog = ref(false)
const showEventDialog = ref(false)
const editingItem = ref(null)

// Table headers
const reservationHeaders = [
  { title: 'ID', key: 'id', width: '80px' },
  { title: 'Name', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Phone', key: 'phone' },
  { title: 'Date', key: 'date' },
  { title: 'Time', key: 'time' },
  { title: 'Guests', key: 'guests', width: '100px' },
  { title: 'Status', key: 'status', width: '120px' },
  { title: 'Actions', key: 'actions', sortable: false, width: '120px' }
]

const orderHeaders = [
  { title: 'Order #', key: 'order_number' },
  { title: 'Customer', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Phone', key: 'phone' },
  { title: 'Total', key: 'total_amount' },
  { title: 'Delivery Date', key: 'delivery_date' },
  { title: 'Status', key: 'status', width: '120px' },
  { title: 'Actions', key: 'actions', sortable: false, width: '120px' }
]

const menuHeaders = [
  { title: 'ID', key: 'id', width: '80px' },
  { title: 'Name', key: 'name' },
  { title: 'Category', key: 'category' },
  { title: 'Price', key: 'price' },
  { title: 'Available', key: 'is_available', width: '100px' },
  { title: 'Actions', key: 'actions', sortable: false, width: '120px' }
]

const eventHeaders = [
  { title: 'ID', key: 'id', width: '80px' },
  { title: 'Title', key: 'title' },
  { title: 'Type', key: 'event_type' },
  { title: 'Date', key: 'event_date' },
  { title: 'Time', key: 'event_time' },
  { title: 'Max Attendees', key: 'max_attendees' },
  { title: 'Price', key: 'price' },
  { title: 'Actions', key: 'actions', sortable: false, width: '120px' }
]

const userHeaders = [
  { title: 'ID', key: 'id', width: '80px' },
  { title: 'Name', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Phone', key: 'phone' },
  { title: 'Admin', key: 'is_admin', width: '100px' },
  { title: 'Created', key: 'created_at' }
]

const messageHeaders = [
  { title: 'ID', key: 'id', width: '80px' },
  { title: 'Name', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Subject', key: 'subject' },
  { title: 'Status', key: 'status', width: '120px' },
  { title: 'Date', key: 'created_at' },
  { title: 'Actions', key: 'actions', sortable: false, width: '100px' }
]

// Computed filtered data
const filteredReservations = computed(() => {
  if (!reservationSearch.value) return reservations.value
  const search = reservationSearch.value.toLowerCase()
  return reservations.value.filter(r =>
    r.name?.toLowerCase().includes(search) ||
    r.email?.toLowerCase().includes(search) ||
    r.phone?.includes(search)
  )
})

const filteredOrders = computed(() => {
  if (!orderSearch.value) return orders.value
  const search = orderSearch.value.toLowerCase()
  return orders.value.filter(o =>
    o.order_number?.toLowerCase().includes(search) ||
    o.name?.toLowerCase().includes(search) ||
    o.email?.toLowerCase().includes(search)
  )
})

const filteredMenuItems = computed(() => {
  if (!menuSearch.value) return menuItems.value
  const search = menuSearch.value.toLowerCase()
  return menuItems.value.filter(m =>
    m.name?.toLowerCase().includes(search) ||
    m.category?.toLowerCase().includes(search)
  )
})

const filteredEvents = computed(() => {
  if (!eventSearch.value) return events.value
  const search = eventSearch.value.toLowerCase()
  return events.value.filter(e =>
    e.title?.toLowerCase().includes(search) ||
    e.event_type?.toLowerCase().includes(search)
  )
})

const filteredUsers = computed(() => {
  if (!userSearch.value) return users.value
  const search = userSearch.value.toLowerCase()
  return users.value.filter(u =>
    u.name?.toLowerCase().includes(search) ||
    u.email?.toLowerCase().includes(search)
  )
})

const filteredMessages = computed(() => {
  if (!messageSearch.value) return contactMessages.value
  const search = messageSearch.value.toLowerCase()
  return contactMessages.value.filter(m =>
    m.name?.toLowerCase().includes(search) ||
    m.email?.toLowerCase().includes(search) ||
    m.subject?.toLowerCase().includes(search)
  )
})

// Load all data
async function loadDashboardData() {
  loading.value = true
  try {
    // Load reservations
    const resResponse = await reservationsAPI.getAll()
    reservations.value = resResponse.data || []

    // Load orders
    const ordResponse = await ordersAPI.getAll()
    orders.value = ordResponse.data || []

    // Load menu items
    const menuResponse = await menuAPI.getAll()
    menuItems.value = menuResponse.data || []

    // Load events
    const eventsResponse = await eventsAPI.getAll()
    events.value = eventsResponse.data || []

    // Load contact messages
    const contactResponse = await contactAPI.getAll()
    contactMessages.value = contactResponse.data || []

    // Calculate statistics
    calculateStats()
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  } finally {
    loading.value = false
  }
}

function calculateStats() {
  stats.value.totalReservations = reservations.value.length
  stats.value.totalOrders = orders.value.length
  stats.value.totalUsers = users.value.length
  stats.value.contactMessages = contactMessages.value.length
  stats.value.upcomingEvents = events.value.filter(e => new Date(e.event_date) >= new Date()).length

  stats.value.pendingReservations = reservations.value.filter(r => r.status === 'pending').length
  stats.value.pendingOrders = orders.value.filter(o => o.status === 'pending').length

  stats.value.totalRevenue = orders.value.reduce((sum, order) => {
    return sum + (parseFloat(order.total_amount) || 0)
  }, 0)
}

// Reservation actions
function viewReservation(item) {
  editingItem.value = { ...item }
  showReservationDialog.value = true
}

async function updateReservationStatus(id, newStatus) {
  try {
    await reservationsAPI.updateStatus(id, newStatus)
    await loadDashboardData()
  } catch (error) {
    console.error('Error updating reservation:', error)
    alert('Failed to update reservation status')
  }
}

async function deleteReservation(id) {
  if (!confirm('Are you sure you want to delete this reservation?')) return

  try {
    await reservationsAPI.delete(id)
    await loadDashboardData()
  } catch (error) {
    console.error('Error deleting reservation:', error)
    alert('Failed to delete reservation')
  }
}

// Order actions
function viewOrder(item) {
  editingItem.value = { ...item }
  showOrderDialog.value = true
}

async function updateOrderStatus(id, newStatus) {
  try {
    await ordersAPI.updateStatus(id, newStatus)
    await loadDashboardData()
  } catch (error) {
    console.error('Error updating order:', error)
    alert('Failed to update order status')
  }
}

async function deleteOrder(id) {
  if (!confirm('Are you sure you want to delete this order?')) return

  try {
    await ordersAPI.delete(id)
    await loadDashboardData()
  } catch (error) {
    console.error('Error deleting order:', error)
    alert('Failed to delete order')
  }
}

// Menu actions
function editMenuItem(item) {
  editingItem.value = { ...item }
  showMenuDialog.value = true
}

function addMenuItem() {
  editingItem.value = {
    name: '',
    description: '',
    category: 'cakes',
    price: 0,
    is_available: true
  }
  showMenuDialog.value = true
}

async function saveMenuItem() {
  try {
    if (editingItem.value.id) {
      await menuAPI.update(editingItem.value.id, editingItem.value)
    } else {
      await menuAPI.create(editingItem.value)
    }
    showMenuDialog.value = false
    await loadDashboardData()
  } catch (error) {
    console.error('Error saving menu item:', error)
    alert('Failed to save menu item')
  }
}

async function deleteMenuItem(id) {
  if (!confirm('Are you sure you want to delete this menu item?')) return

  try {
    await menuAPI.delete(id)
    await loadDashboardData()
  } catch (error) {
    console.error('Error deleting menu item:', error)
    alert('Failed to delete menu item')
  }
}

// Event actions
function editEvent(item) {
  editingItem.value = { ...item }
  showEventDialog.value = true
}

function addEvent() {
  editingItem.value = {
    title: '',
    description: '',
    event_type: 'baking_class',
    event_date: '',
    event_time: '',
    max_attendees: 10,
    price: 0
  }
  showEventDialog.value = true
}

async function saveEvent() {
  try {
    if (editingItem.value.id) {
      await eventsAPI.update(editingItem.value.id, editingItem.value)
    } else {
      await eventsAPI.create(editingItem.value)
    }
    showEventDialog.value = false
    await loadDashboardData()
  } catch (error) {
    console.error('Error saving event:', error)
    alert('Failed to save event')
  }
}

async function deleteEvent(id) {
  if (!confirm('Are you sure you want to delete this event?')) return

  try {
    await eventsAPI.delete(id)
    await loadDashboardData()
  } catch (error) {
    console.error('Error deleting event:', error)
    alert('Failed to delete event')
  }
}

// Message actions
async function markMessageAsRead(id) {
  try {
    await contactAPI.markAsRead(id)
    await loadDashboardData()
  } catch (error) {
    console.error('Error marking message as read:', error)
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

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-KE', {
    style: 'currency',
    currency: 'KES'
  }).format(amount || 0)
}

function formatDate(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

onMounted(() => {
  loadDashboardData()
})
</script>

<template>
  <v-container fluid class="admin-container pa-4 pa-md-6">
    <v-row class="mb-4">
      <v-col cols="12">
        <h1 class="admin-title">
          <v-icon color="amber-darken-2" size="36" class="mr-2">mdi-shield-crown</v-icon>
          Admin Dashboard
        </h1>
        <p class="admin-subtitle">Manage your bakery system</p>
      </v-col>
    </v-row>

    <!-- Tabs Navigation -->
    <v-tabs v-model="currentTab" color="amber-darken-2" class="mb-4">
      <v-tab value="dashboard">
        <v-icon class="mr-2">mdi-view-dashboard</v-icon>
        <span class="d-none d-sm-inline">Dashboard</span>
      </v-tab>
      <v-tab value="reservations">
        <v-icon class="mr-2">mdi-calendar-clock</v-icon>
        <span class="d-none d-sm-inline">Reservations</span>
      </v-tab>
      <v-tab value="orders">
        <v-icon class="mr-2">mdi-receipt</v-icon>
        <span class="d-none d-sm-inline">Orders</span>
      </v-tab>
      <v-tab value="menu">
        <v-icon class="mr-2">mdi-food</v-icon>
        <span class="d-none d-sm-inline">Menu</span>
      </v-tab>
      <v-tab value="events">
        <v-icon class="mr-2">mdi-party-popper</v-icon>
        <span class="d-none d-sm-inline">Events</span>
      </v-tab>
      <v-tab value="messages">
        <v-icon class="mr-2">mdi-email</v-icon>
        <span class="d-none d-sm-inline">Messages</span>
      </v-tab>
    </v-tabs>

    <!-- Tab Content -->
    <v-window v-model="currentTab">
      <!-- Dashboard Tab -->
      <v-window-item value="dashboard">
        <v-row>
          <!-- Statistics Cards -->
          <v-col cols="12" sm="6" md="3">
            <v-card class="stat-card" elevation="3">
              <v-card-text>
                <div class="stat-icon-wrapper mb-3">
                  <v-icon size="40" color="amber-darken-2">mdi-calendar-check</v-icon>
                </div>
                <h3 class="stat-number">{{ stats.totalReservations }}</h3>
                <p class="stat-label">Total Reservations</p>
                <p class="stat-detail">{{ stats.pendingReservations }} pending</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card class="stat-card" elevation="3">
              <v-card-text>
                <div class="stat-icon-wrapper mb-3">
                  <v-icon size="40" color="amber-darken-2">mdi-receipt</v-icon>
                </div>
                <h3 class="stat-number">{{ stats.totalOrders }}</h3>
                <p class="stat-label">Total Orders</p>
                <p class="stat-detail">{{ stats.pendingOrders }} pending</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card class="stat-card" elevation="3">
              <v-card-text>
                <div class="stat-icon-wrapper mb-3">
                  <v-icon size="40" color="amber-darken-2">mdi-cash</v-icon>
                </div>
                <h3 class="stat-number">{{ formatCurrency(stats.totalRevenue) }}</h3>
                <p class="stat-label">Total Revenue</p>
                <p class="stat-detail">From all orders</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card class="stat-card" elevation="3">
              <v-card-text>
                <div class="stat-icon-wrapper mb-3">
                  <v-icon size="40" color="amber-darken-2">mdi-email</v-icon>
                </div>
                <h3 class="stat-number">{{ stats.contactMessages }}</h3>
                <p class="stat-label">Contact Messages</p>
                <p class="stat-detail">{{ stats.upcomingEvents }} upcoming events</p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Quick Actions -->
        <v-row class="mt-4">
          <v-col cols="12">
            <v-card class="admin-card" elevation="3">
              <v-card-title class="card-title">
                <v-icon color="amber-darken-2" class="mr-2">mdi-lightning-bolt</v-icon>
                Quick Actions
              </v-card-title>
              <v-card-text>
                <v-row>
                  <v-col cols="12" sm="6" md="3">
                    <v-btn color="amber-darken-2" block @click="currentTab = 'reservations'">
                      <v-icon class="mr-2">mdi-calendar-plus</v-icon>
                      View Reservations
                    </v-btn>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
                    <v-btn color="amber-darken-2" block @click="currentTab = 'orders'">
                      <v-icon class="mr-2">mdi-receipt-text</v-icon>
                      View Orders
                    </v-btn>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
                    <v-btn color="amber-darken-2" block @click="addMenuItem">
                      <v-icon class="mr-2">mdi-food-variant</v-icon>
                      Add Menu Item
                    </v-btn>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
                    <v-btn color="amber-darken-2" block @click="addEvent">
                      <v-icon class="mr-2">mdi-calendar-star</v-icon>
                      Add Event
                    </v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Recent Activity -->
        <v-row class="mt-4">
          <v-col cols="12" md="6">
            <v-card class="admin-card" elevation="3">
              <v-card-title class="card-title">
                <v-icon color="amber-darken-2" class="mr-2">mdi-clock-outline</v-icon>
                Recent Reservations
              </v-card-title>
              <v-card-text>
                <v-list v-if="reservations.length > 0">
                  <v-list-item v-for="reservation in reservations.slice(0, 5)" :key="reservation.id" class="px-0">
                    <v-list-item-title>{{ reservation.name }}</v-list-item-title>
                    <v-list-item-subtitle>{{ formatDate(reservation.date) }} at {{ reservation.time }}</v-list-item-subtitle>
                    <template v-slot:append>
                      <v-chip :color="getStatusColor(reservation.status)" size="small">
                        {{ reservation.status }}
                      </v-chip>
                    </template>
                  </v-list-item>
                </v-list>
                <p v-else class="text-center text-grey">No reservations yet</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" md="6">
            <v-card class="admin-card" elevation="3">
              <v-card-title class="card-title">
                <v-icon color="amber-darken-2" class="mr-2">mdi-shopping</v-icon>
                Recent Orders
              </v-card-title>
              <v-card-text>
                <v-list v-if="orders.length > 0">
                  <v-list-item v-for="order in orders.slice(0, 5)" :key="order.id" class="px-0">
                    <v-list-item-title>{{ order.order_number }}</v-list-item-title>
                    <v-list-item-subtitle>{{ order.name }} - {{ formatCurrency(order.total_amount) }}</v-list-item-subtitle>
                    <template v-slot:append>
                      <v-chip :color="getStatusColor(order.status)" size="small">
                        {{ order.status }}
                      </v-chip>
                    </template>
                  </v-list-item>
                </v-list>
                <p v-else class="text-center text-grey">No orders yet</p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-window-item>

      <!-- Reservations Tab -->
      <v-window-item value="reservations">
        <v-card class="admin-card" elevation="3">
          <v-card-title class="card-title">
            <v-icon color="amber-darken-2" class="mr-2">mdi-calendar-clock</v-icon>
            Manage Reservations
          </v-card-title>
          <v-card-text>
            <v-text-field
              v-model="reservationSearch"
              label="Search reservations..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              color="amber-darken-2"
              class="mb-4"
              clearable
            ></v-text-field>

            <v-data-table
              :headers="reservationHeaders"
              :items="filteredReservations"
              :loading="loading"
              class="admin-table"
              :items-per-page="10"
            >
              <template v-slot:item.status="{ item }">
                <v-chip :color="getStatusColor(item.status)" size="small">
                  {{ item.status }}
                </v-chip>
              </template>

              <template v-slot:item.actions="{ item }">
                <v-btn icon size="small" @click="viewReservation(item)" class="mr-1">
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                <v-btn icon size="small" color="error" @click="deleteReservation(item.id)">
                  <v-icon size="small">mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-window-item>

      <!-- Orders Tab -->
      <v-window-item value="orders">
        <v-card class="admin-card" elevation="3">
          <v-card-title class="card-title">
            <v-icon color="amber-darken-2" class="mr-2">mdi-receipt</v-icon>
            Manage Orders
          </v-card-title>
          <v-card-text>
            <v-text-field
              v-model="orderSearch"
              label="Search orders..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              color="amber-darken-2"
              class="mb-4"
              clearable
            ></v-text-field>

            <v-data-table
              :headers="orderHeaders"
              :items="filteredOrders"
              :loading="loading"
              class="admin-table"
              :items-per-page="10"
            >
              <template v-slot:item.total_amount="{ item }">
                {{ formatCurrency(item.total_amount) }}
              </template>

              <template v-slot:item.delivery_date="{ item }">
                {{ formatDate(item.delivery_date) }}
              </template>

              <template v-slot:item.status="{ item }">
                <v-chip :color="getStatusColor(item.status)" size="small">
                  {{ item.status }}
                </v-chip>
              </template>

              <template v-slot:item.actions="{ item }">
                <v-btn icon size="small" @click="viewOrder(item)" class="mr-1">
                  <v-icon size="small">mdi-eye</v-icon>
                </v-btn>
                <v-btn icon size="small" color="error" @click="deleteOrder(item.id)">
                  <v-icon size="small">mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-window-item>

      <!-- Menu Tab -->
      <v-window-item value="menu">
        <v-card class="admin-card" elevation="3">
          <v-card-title class="card-title d-flex justify-space-between align-center flex-wrap">
            <div>
              <v-icon color="amber-darken-2" class="mr-2">mdi-food</v-icon>
              Manage Menu
            </div>
            <v-btn color="amber-darken-2" @click="addMenuItem" class="mt-2 mt-sm-0">
              <v-icon class="mr-2">mdi-plus</v-icon>
              Add Item
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-text-field
              v-model="menuSearch"
              label="Search menu items..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              color="amber-darken-2"
              class="mb-4"
              clearable
            ></v-text-field>

            <v-data-table
              :headers="menuHeaders"
              :items="filteredMenuItems"
              :loading="loading"
              class="admin-table"
              :items-per-page="10"
            >
              <template v-slot:item.price="{ item }">
                {{ formatCurrency(item.price) }}
              </template>

              <template v-slot:item.is_available="{ item }">
                <v-chip :color="item.is_available ? 'green' : 'red'" size="small">
                  {{ item.is_available ? 'Available' : 'Unavailable' }}
                </v-chip>
              </template>

              <template v-slot:item.actions="{ item }">
                <v-btn icon size="small" @click="editMenuItem(item)" class="mr-1">
                  <v-icon size="small">mdi-pencil</v-icon>
                </v-btn>
                <v-btn icon size="small" color="error" @click="deleteMenuItem(item.id)">
                  <v-icon size="small">mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-window-item>

      <!-- Events Tab -->
      <v-window-item value="events">
        <v-card class="admin-card" elevation="3">
          <v-card-title class="card-title d-flex justify-space-between align-center flex-wrap">
            <div>
              <v-icon color="amber-darken-2" class="mr-2">mdi-party-popper</v-icon>
              Manage Events
            </div>
            <v-btn color="amber-darken-2" @click="addEvent" class="mt-2 mt-sm-0">
              <v-icon class="mr-2">mdi-plus</v-icon>
              Add Event
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-text-field
              v-model="eventSearch"
              label="Search events..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              color="amber-darken-2"
              class="mb-4"
              clearable
            ></v-text-field>

            <v-data-table
              :headers="eventHeaders"
              :items="filteredEvents"
              :loading="loading"
              class="admin-table"
              :items-per-page="10"
            >
              <template v-slot:item.event_date="{ item }">
                {{ formatDate(item.event_date) }}
              </template>

              <template v-slot:item.price="{ item }">
                {{ formatCurrency(item.price) }}
              </template>

              <template v-slot:item.actions="{ item }">
                <v-btn icon size="small" @click="editEvent(item)" class="mr-1">
                  <v-icon size="small">mdi-pencil</v-icon>
                </v-btn>
                <v-btn icon size="small" color="error" @click="deleteEvent(item.id)">
                  <v-icon size="small">mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-window-item>

      <!-- Messages Tab -->
      <v-window-item value="messages">
        <v-card class="admin-card" elevation="3">
          <v-card-title class="card-title">
            <v-icon color="amber-darken-2" class="mr-2">mdi-email</v-icon>
            Contact Messages
          </v-card-title>
          <v-card-text>
            <v-text-field
              v-model="messageSearch"
              label="Search messages..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              color="amber-darken-2"
              class="mb-4"
              clearable
            ></v-text-field>

            <v-data-table
              :headers="messageHeaders"
              :items="filteredMessages"
              :loading="loading"
              class="admin-table"
              :items-per-page="10"
            >
              <template v-slot:item.status="{ item }">
                <v-chip :color="item.status === 'read' ? 'green' : 'orange'" size="small">
                  {{ item.status || 'unread' }}
                </v-chip>
              </template>

              <template v-slot:item.created_at="{ item }">
                {{ formatDate(item.created_at) }}
              </template>

              <template v-slot:item.actions="{ item }">
                <v-btn
                  v-if="item.status !== 'read'"
                  icon
                  size="small"
                  color="success"
                  @click="markMessageAsRead(item.id)"
                >
                  <v-icon size="small">mdi-check</v-icon>
                </v-btn>
              </template>

              <template v-slot:expanded-row="{ item }">
                <tr>
                  <td :colspan="messageHeaders.length" class="pa-4">
                    <div class="message-details">
                      <p><strong>Message:</strong></p>
                      <p>{{ item.message }}</p>
                    </div>
                  </td>
                </tr>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-window-item>
    </v-window>

    <!-- Reservation Detail Dialog -->
    <v-dialog v-model="showReservationDialog" max-width="600">
      <v-card v-if="editingItem" class="dialog-card">
        <v-card-title class="bg-amber-lighten-4">
          <v-icon color="amber-darken-2" class="mr-2">mdi-calendar-check</v-icon>
          Reservation Details
        </v-card-title>
        <v-card-text class="pt-4">
          <v-row>
            <v-col cols="12" sm="6">
              <p><strong>Name:</strong> {{ editingItem.name }}</p>
              <p><strong>Email:</strong> {{ editingItem.email }}</p>
              <p><strong>Phone:</strong> {{ editingItem.phone }}</p>
            </v-col>
            <v-col cols="12" sm="6">
              <p><strong>Date:</strong> {{ formatDate(editingItem.date) }}</p>
              <p><strong>Time:</strong> {{ editingItem.time }}</p>
              <p><strong>Guests:</strong> {{ editingItem.guests }}</p>
            </v-col>
            <v-col cols="12">
              <p><strong>Notes:</strong></p>
              <p>{{ editingItem.notes || 'No notes' }}</p>
            </v-col>
            <v-col cols="12">
              <v-select
                v-model="editingItem.status"
                label="Status"
                :items="['pending', 'confirmed', 'completed', 'cancelled']"
                variant="outlined"
                color="amber-darken-2"
              ></v-select>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showReservationDialog = false">Close</v-btn>
          <v-btn
            color="amber-darken-2"
            @click="updateReservationStatus(editingItem.id, editingItem.status); showReservationDialog = false"
          >
            Update Status
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Order Detail Dialog -->
    <v-dialog v-model="showOrderDialog" max-width="600">
      <v-card v-if="editingItem" class="dialog-card">
        <v-card-title class="bg-amber-lighten-4">
          <v-icon color="amber-darken-2" class="mr-2">mdi-receipt</v-icon>
          Order Details
        </v-card-title>
        <v-card-text class="pt-4">
          <v-row>
            <v-col cols="12" sm="6">
              <p><strong>Order #:</strong> {{ editingItem.order_number }}</p>
              <p><strong>Customer:</strong> {{ editingItem.name }}</p>
              <p><strong>Email:</strong> {{ editingItem.email }}</p>
              <p><strong>Phone:</strong> {{ editingItem.phone }}</p>
            </v-col>
            <v-col cols="12" sm="6">
              <p><strong>Total:</strong> {{ formatCurrency(editingItem.total_amount) }}</p>
              <p><strong>Delivery Date:</strong> {{ formatDate(editingItem.delivery_date) }}</p>
              <p><strong>Payment Method:</strong> {{ editingItem.payment_method || 'N/A' }}</p>
            </v-col>
            <v-col cols="12">
              <v-select
                v-model="editingItem.status"
                label="Status"
                :items="['pending', 'confirmed', 'preparing', 'completed', 'cancelled']"
                variant="outlined"
                color="amber-darken-2"
              ></v-select>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showOrderDialog = false">Close</v-btn>
          <v-btn
            color="amber-darken-2"
            @click="updateOrderStatus(editingItem.id, editingItem.status); showOrderDialog = false"
          >
            Update Status
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Menu Item Dialog -->
    <v-dialog v-model="showMenuDialog" max-width="600">
      <v-card v-if="editingItem" class="dialog-card">
        <v-card-title class="bg-amber-lighten-4">
          <v-icon color="amber-darken-2" class="mr-2">mdi-food</v-icon>
          {{ editingItem.id ? 'Edit' : 'Add' }} Menu Item
        </v-card-title>
        <v-card-text class="pt-4">
          <v-text-field
            v-model="editingItem.name"
            label="Item Name"
            variant="outlined"
            color="amber-darken-2"
            class="mb-2"
          ></v-text-field>

          <v-textarea
            v-model="editingItem.description"
            label="Description"
            variant="outlined"
            color="amber-darken-2"
            rows="3"
            class="mb-2"
          ></v-textarea>

          <v-select
            v-model="editingItem.category"
            label="Category"
            :items="['cakes', 'pastries', 'breads', 'cookies', 'packages', 'baking_classes']"
            variant="outlined"
            color="amber-darken-2"
            class="mb-2"
          ></v-select>

          <v-text-field
            v-model.number="editingItem.price"
            label="Price (KES)"
            type="number"
            variant="outlined"
            color="amber-darken-2"
            class="mb-2"
          ></v-text-field>

          <v-switch
            v-model="editingItem.is_available"
            label="Available"
            color="amber-darken-2"
          ></v-switch>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showMenuDialog = false">Cancel</v-btn>
          <v-btn color="amber-darken-2" @click="saveMenuItem">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Event Dialog -->
    <v-dialog v-model="showEventDialog" max-width="600">
      <v-card v-if="editingItem" class="dialog-card">
        <v-card-title class="bg-amber-lighten-4">
          <v-icon color="amber-darken-2" class="mr-2">mdi-party-popper</v-icon>
          {{ editingItem.id ? 'Edit' : 'Add' }} Event
        </v-card-title>
        <v-card-text class="pt-4">
          <v-text-field
            v-model="editingItem.title"
            label="Event Title"
            variant="outlined"
            color="amber-darken-2"
            class="mb-2"
          ></v-text-field>

          <v-textarea
            v-model="editingItem.description"
            label="Description"
            variant="outlined"
            color="amber-darken-2"
            rows="3"
            class="mb-2"
          ></v-textarea>

          <v-select
            v-model="editingItem.event_type"
            label="Event Type"
            :items="['baking_class', 'special_event', 'workshop']"
            variant="outlined"
            color="amber-darken-2"
            class="mb-2"
          ></v-select>

          <v-text-field
            v-model="editingItem.event_date"
            label="Event Date"
            type="date"
            variant="outlined"
            color="amber-darken-2"
            class="mb-2"
          ></v-text-field>

          <v-text-field
            v-model="editingItem.event_time"
            label="Event Time"
            type="time"
            variant="outlined"
            color="amber-darken-2"
            class="mb-2"
          ></v-text-field>

          <v-text-field
            v-model.number="editingItem.max_attendees"
            label="Max Attendees"
            type="number"
            variant="outlined"
            color="amber-darken-2"
            class="mb-2"
          ></v-text-field>

          <v-text-field
            v-model.number="editingItem.price"
            label="Price (KES)"
            type="number"
            variant="outlined"
            color="amber-darken-2"
          ></v-text-field>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showEventDialog = false">Cancel</v-btn>
          <v-btn color="amber-darken-2" @click="saveEvent">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<style scoped>
.admin-container {
  background: #f5f5f5;
  min-height: 100vh;
}

.admin-title {
  color: #b28704;
  font-size: 2rem;
  font-weight: 700;
  display: flex;
  align-items: center;
}

.admin-subtitle {
  color: #7a7a7a;
  font-size: 1rem;
  margin-top: 0.5rem;
}

.stat-card {
  background: #fffbe6;
  border-radius: 16px;
  border-left: 4px solid #b28704;
}

.stat-icon-wrapper {
  display: flex;
  justify-content: center;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #b28704;
  text-align: center;
}

.stat-label {
  color: #5a5a5a;
  font-size: 0.9rem;
  text-align: center;
  margin: 0.5rem 0 0.25rem 0;
}

.stat-detail {
  color: #7a7a7a;
  font-size: 0.8rem;
  text-align: center;
  margin: 0;
}

.admin-card {
  background: #fffbe6;
  border-radius: 16px;
}

.card-title {
  color: #b28704;
  font-size: 1.3rem;
  font-weight: 600;
}

.admin-table {
  background: transparent;
}

.dialog-card {
  background: #fffbe6;
  border-radius: 16px;
}

.message-details {
  background: #f5f5f5;
  padding: 1rem;
  border-radius: 8px;
}

@media (max-width: 600px) {
  .admin-title {
    font-size: 1.5rem;
  }

  .stat-number {
    font-size: 1.5rem;
  }
}
</style>
