<script setup>
import { ref, computed } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { orderAPI } from '@/services/api'

const cartStore = useCartStore()
const authStore = useAuthStore()
const router = useRouter()

// If cart is empty, redirect to menu
if (cartStore.items.length === 0) {
  router.push('/menu')
}

// Form fields
const formRef = ref(null)
const loading = ref(false)
const name = ref(authStore.user?.name || '')
const email = ref(authStore.user?.email || '')
const phone = ref(authStore.user?.phone || '')
const deliveryAddress = ref('')
const deliveryDate = ref(null)
const deliveryTime = ref('')
const deliveryMethod = ref('delivery') // delivery or pickup
const paymentMethod = ref('mpesa') // mpesa or cash
const specialInstructions = ref('')
const dateMenu = ref(false)

const formattedDeliveryDate = computed(() => {
  if (!deliveryDate.value) return ''
  const dateObj = new Date(deliveryDate.value)
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

async function submitOrder() {
  if (!formRef.value) return

  const { valid } = await formRef.value.validate()

  if (!valid) {
    return
  }

  if (!deliveryDate.value) {
    alert('Please select a delivery date')
    return
  }

  loading.value = true

  try {
    const orderData = {
      customer_name: name.value,
      customer_email: email.value,
      customer_phone: phone.value,
      delivery_address: deliveryMethod.value === 'delivery' ? deliveryAddress.value : 'Pickup at store',
      delivery_date: deliveryDate.value,
      delivery_time: deliveryTime.value,
      delivery_method: deliveryMethod.value,
      payment_method: paymentMethod.value,
      special_instructions: specialInstructions.value,
      items: cartStore.items,
      subtotal: cartStore.subtotal,
      deposit_amount: cartStore.deposit,
      balance_amount: cartStore.balance,
      status: 'pending'
    }

    // Submit order to backend
    const response = await orderAPI.create(orderData)

    // Save order data for success page
    localStorage.setItem('lastOrder', JSON.stringify({
      ...orderData,
      order_id: response.data.order_id || 'ORD-' + Date.now(),
      created_at: new Date().toISOString()
    }))

    // If M-Pesa payment, initiate STK push
    if (paymentMethod.value === 'mpesa') {
      // TODO: Integrate M-Pesa STK Push
      // For now, just proceed to success page
      console.log('M-Pesa payment will be initiated')
    }

    // Clear cart
    cartStore.clearCart()

    // Redirect to success page
    router.push('/order-success')

  } catch (error) {
    console.error('Order submission error:', error)
    alert('Failed to submit order. Please try again.')
  } finally {
    loading.value = false
  }
}

function goBack() {
  router.push('/cart')
}
</script>

<template>
  <v-container class="checkout-container py-6 py-md-8">
    <!-- Page Header -->
    <v-row class="mb-6">
      <v-col cols="12">
        <div class="d-flex align-center mb-2">
          <v-btn
            icon
            variant="text"
            color="amber-darken-2"
            @click="goBack"
            class="mr-2"
          >
            <v-icon>mdi-arrow-left</v-icon>
          </v-btn>
          <h1 class="page-title">
            <v-icon color="amber-darken-2" size="40" class="mr-2">mdi-shopping</v-icon>
            Checkout
          </h1>
        </div>
        <p class="page-subtitle">Complete your order details and payment information</p>
      </v-col>
    </v-row>

    <v-form @submit.prevent="submitOrder" ref="formRef">
      <v-row>
        <!-- Checkout Form -->
        <v-col cols="12" lg="8">
          <v-card class="checkout-form-card mb-4" elevation="3">
            <v-card-title class="form-section-title">
              <v-icon color="amber-darken-2" class="mr-2">mdi-account</v-icon>
              Contact Information
            </v-card-title>
            <v-card-text class="px-4 px-md-6">
              <v-text-field
                v-model="name"
                label="Full Name"
                variant="outlined"
                color="amber-darken-2"
                prepend-inner-icon="mdi-account"
                :rules="[v => !!v || 'Name is required']"
                required
                class="mb-2"
              ></v-text-field>

              <v-text-field
                v-model="email"
                label="Email Address"
                type="email"
                variant="outlined"
                color="amber-darken-2"
                prepend-inner-icon="mdi-email"
                :rules="[v => !!v || 'Email is required', v => /.+@.+\..+/.test(v) || 'Email must be valid']"
                required
                class="mb-2"
              ></v-text-field>

              <v-text-field
                v-model="phone"
                label="Phone Number (M-Pesa)"
                variant="outlined"
                color="amber-darken-2"
                prepend-inner-icon="mdi-phone"
                placeholder="254712345678"
                :rules="[v => !!v || 'Phone is required']"
                required
              ></v-text-field>
            </v-card-text>
          </v-card>

          <v-card class="checkout-form-card mb-4" elevation="3">
            <v-card-title class="form-section-title">
              <v-icon color="amber-darken-2" class="mr-2">mdi-truck</v-icon>
              Delivery Information
            </v-card-title>
            <v-card-text class="px-4 px-md-6">
              <v-radio-group v-model="deliveryMethod" inline class="mb-3">
                <v-radio label="Delivery" value="delivery" color="amber-darken-2"></v-radio>
                <v-radio label="Pickup at Store" value="pickup" color="amber-darken-2"></v-radio>
              </v-radio-group>

              <v-text-field
                v-if="deliveryMethod === 'delivery'"
                v-model="deliveryAddress"
                label="Delivery Address"
                variant="outlined"
                color="amber-darken-2"
                prepend-inner-icon="mdi-map-marker"
                :rules="[v => deliveryMethod === 'pickup' || !!v || 'Address is required for delivery']"
                placeholder="Enter your full delivery address"
                class="mb-2"
              ></v-text-field>

              <v-menu
                v-model="dateMenu"
                :close-on-content-click="false"
                transition="scale-transition"
              >
                <template v-slot:activator="{ props }">
                  <v-text-field
                    v-model="formattedDeliveryDate"
                    label="Delivery/Pickup Date"
                    variant="outlined"
                    color="amber-darken-2"
                    prepend-inner-icon="mdi-calendar"
                    readonly
                    v-bind="props"
                    :rules="[v => !!deliveryDate || 'Date is required']"
                    required
                    class="mb-2"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="deliveryDate"
                  color="amber-darken-2"
                  @update:model-value="dateMenu = false"
                  :min="new Date().toISOString().split('T')[0]"
                ></v-date-picker>
              </v-menu>

              <v-text-field
                v-model="deliveryTime"
                label="Preferred Time"
                type="time"
                variant="outlined"
                color="amber-darken-2"
                prepend-inner-icon="mdi-clock-outline"
                :rules="[v => !!v || 'Time is required']"
                required
                class="mb-2"
              ></v-text-field>

              <v-textarea
                v-model="specialInstructions"
                label="Special Instructions (Optional)"
                variant="outlined"
                color="amber-darken-2"
                prepend-inner-icon="mdi-message-text"
                rows="3"
                placeholder="Any special instructions for your order..."
              ></v-textarea>
            </v-card-text>
          </v-card>

          <v-card class="checkout-form-card" elevation="3">
            <v-card-title class="form-section-title">
              <v-icon color="amber-darken-2" class="mr-2">mdi-credit-card</v-icon>
              Payment Method
            </v-card-title>
            <v-card-text class="px-4 px-md-6">
              <v-radio-group v-model="paymentMethod">
                <v-radio color="amber-darken-2" value="mpesa">
                  <template v-slot:label>
                    <div class="d-flex align-center">
                      <v-icon color="green" class="mr-2">mdi-cellphone</v-icon>
                      <div>
                        <div class="font-weight-bold">M-Pesa (Recommended)</div>
                        <div class="text-caption text-grey">Pay deposit now via M-Pesa STK Push</div>
                      </div>
                    </div>
                  </template>
                </v-radio>

                <v-radio color="amber-darken-2" value="cash">
                  <template v-slot:label>
                    <div class="d-flex align-center">
                      <v-icon color="amber-darken-2" class="mr-2">mdi-cash</v-icon>
                      <div>
                        <div class="font-weight-bold">Cash on Delivery/Pickup</div>
                        <div class="text-caption text-grey">Pay deposit when order is ready</div>
                      </div>
                    </div>
                  </template>
                </v-radio>
              </v-radio-group>

              <v-alert
                type="info"
                variant="tonal"
                density="compact"
                class="mt-3"
              >
                <div class="text-caption">
                  <strong>Deposit Required:</strong> {{ formatPrice(cartStore.deposit) }} (50% of total)
                  <br>
                  <strong>Balance Due:</strong> {{ formatPrice(cartStore.balance) }} (on delivery/pickup)
                </div>
              </v-alert>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Order Summary -->
        <v-col cols="12" lg="4">
          <v-card class="order-summary-card sticky-summary" elevation="3">
            <v-card-title class="summary-title">
              <v-icon color="amber-darken-2" class="mr-2">mdi-receipt-text</v-icon>
              Order Summary
            </v-card-title>

            <v-card-text class="px-4">
              <!-- Cart Items -->
              <div class="mb-3">
                <h4 class="summary-section-title mb-2">Items ({{ cartStore.itemCount }})</h4>
                <v-list density="compact" class="summary-items-list">
                  <v-list-item
                    v-for="item in cartStore.items"
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
              </div>

              <v-divider class="my-3"></v-divider>

              <!-- Price Breakdown -->
              <v-list density="compact">
                <v-list-item class="px-0">
                  <v-list-item-title>Subtotal</v-list-item-title>
                  <template v-slot:append>
                    <span class="summary-value">{{ formatPrice(cartStore.subtotal) }}</span>
                  </template>
                </v-list-item>

                <v-divider class="my-2"></v-divider>

                <v-list-item class="px-0">
                  <v-list-item-title class="font-weight-bold text-green">
                    Deposit (Pay Now)
                  </v-list-item-title>
                  <template v-slot:append>
                    <span class="summary-value font-weight-bold text-green">
                      {{ formatPrice(cartStore.deposit) }}
                    </span>
                  </template>
                </v-list-item>

                <v-list-item class="px-0">
                  <v-list-item-title class="text-grey">
                    Balance (Pay Later)
                  </v-list-item-title>
                  <template v-slot:append>
                    <span class="summary-value text-grey">
                      {{ formatPrice(cartStore.balance) }}
                    </span>
                  </template>
                </v-list-item>
              </v-list>

              <v-alert
                type="warning"
                variant="tonal"
                density="compact"
                icon="mdi-shield-check"
                class="mt-3"
              >
                <div class="text-caption">
                  Your order will be confirmed once the deposit is received.
                </div>
              </v-alert>
            </v-card-text>

            <v-card-actions class="px-4 pb-4">
              <v-btn
                color="amber-darken-2"
                size="large"
                block
                type="submit"
                :loading="loading"
                prepend-icon="mdi-check-circle"
              >
                Place Order
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-form>
  </v-container>
</template>

<style scoped>
.checkout-container {
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
  margin-left: 48px;
}

.checkout-form-card {
  background: #fffbe6;
  border-radius: 16px;
}

.form-section-title {
  color: #b28704;
  font-size: 1.2rem;
  font-weight: 700;
  padding: 16px 24px;
}

.order-summary-card {
  background: #fffbe6;
  border-radius: 16px;
}

.sticky-summary {
  position: sticky;
  top: 80px;
}

.summary-title {
  color: #b28704;
  font-size: 1.3rem;
  font-weight: 700;
  padding: 16px;
}

.summary-section-title {
  color: #b28704;
  font-size: 1rem;
  font-weight: 600;
}

.summary-items-list {
  background: transparent;
  max-height: 200px;
  overflow-y: auto;
}

.summary-value {
  font-size: 1rem;
  font-weight: 600;
  color: #333;
}

:deep(.v-field) {
  border-radius: 12px;
}

:deep(.v-field--focused) {
  box-shadow: 0 0 0 2px rgba(178, 135, 4, 0.1);
}

@media (max-width: 1280px) {
  .sticky-summary {
    position: static;
  }
}

@media (max-width: 600px) {
  .page-title {
    font-size: 1.5rem;
  }

  .page-subtitle {
    margin-left: 0;
  }
}
</style>
