<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const order = ref(null)

onMounted(() => {
  const orderData = JSON.parse(localStorage.getItem('lastOrder') || '{}')

  if (!orderData || !orderData.order_id) {
    router.push('/menu')
    return
  }

  order.value = orderData
  localStorage.removeItem('lastOrder')
})

const formattedDate = computed(() => {
  if (!order.value?.delivery_date) return ''
  const dateObj = new Date(order.value.delivery_date)
  return dateObj.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
})

function formatPrice(price) {
  return new Intl.NumberFormat('en-KE', {
    style: 'currency',
    currency: 'KES'
  }).format(price)
}

function viewOrders() {
  router.push('/orders')
}

function continueShopping() {
  router.push('/menu')
}

function goHome() {
  router.push('/')
}

function printReceipt() {
  window.print()
}
</script>

<template>
  <v-container class="success-container py-8 py-md-12" v-if="order">
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <!-- Success Header -->
        <v-card class="success-card mb-6" elevation="4">
          <v-card-text class="text-center pa-8 pa-md-12">
            <v-icon color="green" size="100" class="mb-4 success-icon">
              mdi-check-circle
            </v-icon>
            <h1 class="success-title mb-3">Order Placed Successfully!</h1>
            <p class="success-subtitle mb-4">
              Thank you for your order! We've received it and will start preparing your delicious treats.
            </p>
            <v-chip color="green" size="large" class="ma-2">
              <v-icon start>mdi-receipt</v-icon>
              Order #{{ order.order_id }}
            </v-chip>
          </v-card-text>
        </v-card>

        <!-- Order Details -->
        <v-card class="details-card mb-4" elevation="3">
          <v-card-title class="card-title">
            <v-icon color="amber-darken-2" class="mr-2">mdi-information</v-icon>
            Order Details
          </v-card-title>
          <v-card-text class="px-4 px-md-6">
            <v-row>
              <v-col cols="12" md="6">
                <div class="detail-item mb-4">
                  <div class="detail-label">Order ID</div>
                  <div class="detail-value">{{ order.order_id }}</div>
                </div>

                <div class="detail-item mb-4">
                  <div class="detail-label">Customer Name</div>
                  <div class="detail-value">{{ order.customer_name }}</div>
                </div>

                <div class="detail-item mb-4">
                  <div class="detail-label">Email</div>
                  <div class="detail-value">{{ order.customer_email }}</div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">Phone</div>
                  <div class="detail-value">{{ order.customer_phone }}</div>
                </div>
              </v-col>

              <v-col cols="12" md="6">
                <div class="detail-item mb-4">
                  <div class="detail-label">Delivery Method</div>
                  <div class="detail-value text-capitalize">
                    <v-icon size="small" class="mr-1">
                      {{ order.delivery_method === 'delivery' ? 'mdi-truck' : 'mdi-store' }}
                    </v-icon>
                    {{ order.delivery_method }}
                  </div>
                </div>

                <div class="detail-item mb-4" v-if="order.delivery_method === 'delivery'">
                  <div class="detail-label">Delivery Address</div>
                  <div class="detail-value">{{ order.delivery_address }}</div>
                </div>

                <div class="detail-item mb-4">
                  <div class="detail-label">{{ order.delivery_method === 'delivery' ? 'Delivery' : 'Pickup' }} Date</div>
                  <div class="detail-value">{{ formattedDate }}</div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">{{ order.delivery_method === 'delivery' ? 'Delivery' : 'Pickup' }} Time</div>
                  <div class="detail-value">{{ order.delivery_time }}</div>
                </div>
              </v-col>
            </v-row>

            <v-divider class="my-4"></v-divider>

            <!-- Order Items -->
            <div class="mb-3">
              <h4 class="section-title mb-3">Order Items</h4>
              <v-list class="items-list">
                <v-list-item
                  v-for="item in order.items"
                  :key="item.id"
                  class="px-0 py-2"
                >
                  <template v-slot:prepend>
                    <v-avatar size="50" rounded="lg">
                      <v-img
                        :src="item.image || 'https://via.placeholder.com/50x50?text=' + encodeURIComponent(item.name)"
                        cover
                      ></v-img>
                    </v-avatar>
                  </template>

                  <v-list-item-title>{{ item.name }}</v-list-item-title>
                  <v-list-item-subtitle>Quantity: {{ item.quantity }}</v-list-item-subtitle>

                  <template v-slot:append>
                    <span class="item-price">{{ formatPrice(item.price * item.quantity) }}</span>
                  </template>
                </v-list-item>
              </v-list>
            </div>

            <v-divider class="my-4"></v-divider>

            <!-- Payment Summary -->
            <div>
              <h4 class="section-title mb-3">Payment Summary</h4>
              <v-list density="compact" class="payment-list">
                <v-list-item class="px-0">
                  <v-list-item-title>Subtotal</v-list-item-title>
                  <template v-slot:append>
                    <span>{{ formatPrice(order.subtotal) }}</span>
                  </template>
                </v-list-item>

                <v-list-item class="px-0">
                  <v-list-item-title class="font-weight-bold text-green">
                    Deposit Paid (50%)
                  </v-list-item-title>
                  <template v-slot:append>
                    <span class="font-weight-bold text-green">{{ formatPrice(order.deposit_amount) }}</span>
                  </template>
                </v-list-item>

                <v-list-item class="px-0">
                  <v-list-item-title class="text-amber-darken-2">
                    Balance Due on {{ order.delivery_method === 'delivery' ? 'Delivery' : 'Pickup' }}
                  </v-list-item-title>
                  <template v-slot:append>
                    <span class="text-amber-darken-2 font-weight-bold">{{ formatPrice(order.balance_amount) }}</span>
                  </template>
                </v-list-item>
              </v-list>

              <v-alert
                type="info"
                variant="tonal"
                density="compact"
                class="mt-3"
              >
                <div class="text-caption">
                  <strong>Payment Method:</strong> {{ order.payment_method === 'mpesa' ? 'M-Pesa' : 'Cash on ' + (order.delivery_method === 'delivery' ? 'Delivery' : 'Pickup') }}
                </div>
              </v-alert>
            </div>

            <div v-if="order.special_instructions" class="mt-4">
              <h4 class="section-title mb-2">Special Instructions</h4>
              <p class="text-grey">{{ order.special_instructions }}</p>
            </div>
          </v-card-text>
        </v-card>

        <!-- What's Next -->
        <v-card class="whats-next-card mb-4" elevation="3">
          <v-card-title class="card-title">
            <v-icon color="amber-darken-2" class="mr-2">mdi-timeline</v-icon>
            What Happens Next?
          </v-card-title>
          <v-card-text class="px-4 px-md-6">
            <v-timeline side="end" density="compact" class="timeline">
              <v-timeline-item
                dot-color="green"
                size="small"
                icon="mdi-check"
              >
                <div class="timeline-content">
                  <div class="timeline-title">Order Received</div>
                  <div class="timeline-text">We've received your order and payment</div>
                </div>
              </v-timeline-item>

              <v-timeline-item
                dot-color="amber-darken-2"
                size="small"
                icon="mdi-chef-hat"
              >
                <div class="timeline-content">
                  <div class="timeline-title">Preparation</div>
                  <div class="timeline-text">Our bakers will prepare your delicious items</div>
                </div>
              </v-timeline-item>

              <v-timeline-item
                dot-color="amber-darken-2"
                size="small"
                :icon="order.delivery_method === 'delivery' ? 'mdi-truck' : 'mdi-store'"
              >
                <div class="timeline-content">
                  <div class="timeline-title">{{ order.delivery_method === 'delivery' ? 'Delivery' : 'Ready for Pickup' }}</div>
                  <div class="timeline-text">
                    {{ order.delivery_method === 'delivery'
                      ? 'Your order will be delivered on ' + formattedDate
                      : 'Pick up your order on ' + formattedDate }}
                  </div>
                </div>
              </v-timeline-item>

              <v-timeline-item
                dot-color="amber-darken-2"
                size="small"
                icon="mdi-party-popper"
              >
                <div class="timeline-content">
                  <div class="timeline-title">Enjoy!</div>
                  <div class="timeline-text">Savor your delicious treats from The Suvaya Experience</div>
                </div>
              </v-timeline-item>
            </v-timeline>
          </v-card-text>
        </v-card>

        <!-- Actions -->
        <v-card class="actions-card" elevation="3">
          <v-card-text class="px-4 px-md-6 py-4">
            <v-row>
              <v-col cols="12" sm="6" md="3">
                <v-btn
                  color="amber-darken-2"
                  variant="outlined"
                  block
                  prepend-icon="mdi-printer"
                  @click="printReceipt"
                >
                  Print Receipt
                </v-btn>
              </v-col>
              <v-col cols="12" sm="6" md="3">
                <v-btn
                  color="amber-darken-2"
                  variant="outlined"
                  block
                  prepend-icon="mdi-receipt"
                  @click="viewOrders"
                >
                  View My Orders
                </v-btn>
              </v-col>
              <v-col cols="12" sm="6" md="3">
                <v-btn
                  color="amber-darken-2"
                  variant="outlined"
                  block
                  prepend-icon="mdi-storefront"
                  @click="continueShopping"
                >
                  Continue Shopping
                </v-btn>
              </v-col>
              <v-col cols="12" sm="6" md="3">
                <v-btn
                  color="amber-darken-2"
                  block
                  prepend-icon="mdi-home"
                  @click="goHome"
                >
                  Back to Home
                </v-btn>
              </v-col>
            </v-row>
          </v-card-text>
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

.success-card {
  background: linear-gradient(135deg, #fffbe6 0%, #fff8dc 100%);
  border-radius: 20px;
}

.success-icon {
  animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.success-title {
  color: #2e7d32;
  font-size: 2rem;
  font-weight: 700;
}

@media (min-width: 960px) {
  .success-title {
    font-size: 2.5rem;
  }
}

.success-subtitle {
  color: #7a7a7a;
  font-size: 1.1rem;
}

.details-card,
.whats-next-card,
.actions-card {
  background: #fffbe6;
  border-radius: 16px;
}

.card-title {
  color: #b28704;
  font-size: 1.3rem;
  font-weight: 700;
  padding: 16px 24px;
}

.detail-item {

}

.detail-label {
  color: #7a7a7a;
  font-size: 0.875rem;
  margin-bottom: 4px;
}

.detail-value {
  color: #333;
  font-size: 1rem;
  font-weight: 600;
}

.section-title {
  color: #b28704;
  font-size: 1.1rem;
  font-weight: 600;
}

.items-list {
  background: transparent;
}

.item-price {
  font-size: 1rem;
  font-weight: 600;
  color: #2e7d32;
}

.payment-list {
  background: transparent;
}

.timeline {
  margin-top: 8px;
}

.timeline-content {
  padding-bottom: 16px;
}

.timeline-title {
  color: #b28704;
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 4px;
}

.timeline-text {
  color: #7a7a7a;
  font-size: 0.875rem;
}

@media print {
  .actions-card {
    display: none;
  }

  .success-container {
    background: white;
  }
}

@media (max-width: 600px) {
  .success-title {
    font-size: 1.5rem;
  }

  .success-subtitle {
    font-size: 1rem;
  }
}
</style>
