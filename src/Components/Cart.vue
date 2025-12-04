<script setup>
import { computed } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useRouter } from 'vue-router'

const cartStore = useCartStore()
const router = useRouter()

function formatPrice(price) {
  return new Intl.NumberFormat('en-KE', {
    style: 'currency',
    currency: 'KES'
  }).format(price)
}

function getCategoryIcon(category) {
  const icons = {
    cakes: 'mdi-cake',
    pastries: 'mdi-croissant',
    breads: 'mdi-baguette',
    cookies: 'mdi-cookie',
    packages: 'mdi-package-variant',
    baking_classes: 'mdi-school'
  }
  return icons[category] || 'mdi-food'
}

function proceedToCheckout() {
  router.push('/checkout')
}

function continueShopping() {
  router.push('/menu')
}
</script>

<template>
  <v-container class="cart-container py-6 py-md-8">
    <!-- Page Header -->
    <v-row class="mb-6">
      <v-col cols="12">
        <div class="d-flex align-center justify-space-between flex-wrap">
          <h1 class="page-title mb-2 mb-md-0">
            <v-icon color="amber-darken-2" size="40" class="mr-2">mdi-cart</v-icon>
            Shopping Cart
          </h1>
          <v-btn
            color="amber-darken-2"
            variant="outlined"
            prepend-icon="mdi-storefront"
            @click="continueShopping"
          >
            Continue Shopping
          </v-btn>
        </div>
        <p class="page-subtitle mt-2" v-if="cartStore.itemCount > 0">
          {{ cartStore.itemCount }} {{ cartStore.itemCount === 1 ? 'item' : 'items' }} in your cart
        </p>
      </v-col>
    </v-row>

    <!-- Cart Content -->
    <v-row v-if="cartStore.items.length > 0">
      <!-- Cart Items -->
      <v-col cols="12" lg="8">
        <v-card class="cart-items-card" elevation="3">
          <v-card-text class="pa-0">
            <v-list lines="three" class="py-0">
              <template v-for="(item, index) in cartStore.items" :key="item.id">
                <v-list-item class="cart-item py-4 px-4 px-md-6">
                  <template v-slot:prepend>
                    <v-avatar
                      size="80"
                      rounded="lg"
                      class="item-image"
                    >
                      <v-img
                        :src="item.image || 'https://via.placeholder.com/100x100?text=' + encodeURIComponent(item.name)"
                        cover
                      ></v-img>
                    </v-avatar>
                  </template>

                  <v-list-item-title class="item-name mb-1">
                    {{ item.name }}
                  </v-list-item-title>

                  <v-list-item-subtitle class="item-category mb-2">
                    <v-icon size="small" class="mr-1">{{ getCategoryIcon(item.category) }}</v-icon>
                    {{ item.category }}
                  </v-list-item-subtitle>

                  <v-list-item-subtitle class="item-description">
                    {{ item.description }}
                  </v-list-item-subtitle>

                  <template v-slot:append>
                    <div class="item-controls d-flex flex-column align-end">
                      <!-- Price -->
                      <div class="item-price mb-3">
                        {{ formatPrice(item.price * item.quantity) }}
                      </div>

                      <!-- Quantity Controls -->
                      <div class="d-flex align-center mb-2">
                        <v-btn
                          icon
                          size="small"
                          variant="outlined"
                          color="amber-darken-2"
                          @click="cartStore.decrementQuantity(item.id)"
                        >
                          <v-icon>mdi-minus</v-icon>
                        </v-btn>

                        <span class="mx-3 text-h6 quantity-display">{{ item.quantity }}</span>

                        <v-btn
                          icon
                          size="small"
                          variant="outlined"
                          color="amber-darken-2"
                          @click="cartStore.incrementQuantity(item.id)"
                        >
                          <v-icon>mdi-plus</v-icon>
                        </v-btn>
                      </div>

                      <!-- Remove Button -->
                      <v-btn
                        size="small"
                        variant="text"
                        color="error"
                        prepend-icon="mdi-delete"
                        @click="cartStore.removeItem(item.id)"
                      >
                        Remove
                      </v-btn>
                    </div>
                  </template>
                </v-list-item>

                <v-divider v-if="index < cartStore.items.length - 1" class="my-0"></v-divider>
              </template>
            </v-list>
          </v-card-text>

          <!-- Clear Cart Button -->
          <v-card-actions class="px-4 px-md-6 pb-4">
            <v-btn
              color="error"
              variant="outlined"
              prepend-icon="mdi-delete-sweep"
              @click="cartStore.clearCart"
            >
              Clear Cart
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <!-- Order Summary -->
      <v-col cols="12" lg="4">
        <v-card class="order-summary-card sticky-summary" elevation="3">
          <v-card-title class="summary-title">
            <v-icon color="amber-darken-2" class="mr-2">mdi-receipt-text</v-icon>
            Order Summary
          </v-card-title>

          <v-card-text class="px-4 px-md-6">
            <v-list class="summary-list">
              <v-list-item class="px-0">
                <v-list-item-title>Subtotal</v-list-item-title>
                <template v-slot:append>
                  <span class="summary-value">{{ formatPrice(cartStore.subtotal) }}</span>
                </template>
              </v-list-item>

              <v-divider class="my-2"></v-divider>

              <v-list-item class="px-0">
                <v-list-item-title class="font-weight-bold">
                  Deposit Required (50%)
                </v-list-item-title>
                <template v-slot:append>
                  <span class="summary-value font-weight-bold text-green">
                    {{ formatPrice(cartStore.deposit) }}
                  </span>
                </template>
              </v-list-item>

              <v-list-item class="px-0">
                <v-list-item-title class="text-grey">
                  Balance Due Later
                </v-list-item-title>
                <template v-slot:append>
                  <span class="summary-value text-grey">
                    {{ formatPrice(cartStore.balance) }}
                  </span>
                </template>
              </v-list-item>
            </v-list>

            <v-alert
              type="info"
              variant="tonal"
              density="compact"
              class="mt-4 mb-3"
            >
              <div class="text-caption">
                50% deposit is required to confirm your order. The remaining balance will be due on delivery.
              </div>
            </v-alert>
          </v-card-text>

          <v-card-actions class="px-4 px-md-6 pb-4">
            <v-btn
              color="amber-darken-2"
              size="large"
              block
              prepend-icon="mdi-lock"
              @click="proceedToCheckout"
            >
              Proceed to Checkout
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Empty Cart State -->
    <v-row v-else>
      <v-col cols="12">
        <v-card class="empty-cart-card pa-8 pa-md-12" elevation="2">
          <div class="text-center">
            <v-icon color="grey-lighten-1" size="120" class="mb-6">
              mdi-cart-outline
            </v-icon>
            <h2 class="empty-title mb-3">Your Cart is Empty</h2>
            <p class="empty-subtitle mb-6">
              Looks like you haven't added any items to your cart yet.
            </p>
            <v-btn
              color="amber-darken-2"
              size="large"
              prepend-icon="mdi-storefront"
              @click="continueShopping"
            >
              Browse Our Menu
            </v-btn>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.cart-container {
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
}

.cart-items-card {
  background: #fffbe6;
  border-radius: 16px;
}

.cart-item {
  transition: background-color 0.2s ease;
}

.cart-item:hover {
  background-color: #fff8dc;
}

.item-image {
  border: 2px solid #b28704;
}

.item-name {
  color: #b28704;
  font-size: 1.1rem;
  font-weight: 600;
}

.item-category {
  color: #7a7a7a;
  font-size: 0.875rem;
  text-transform: capitalize;
}

.item-description {
  color: #7a7a7a;
  font-size: 0.875rem;
}

.item-controls {
  min-width: 180px;
}

.item-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #2e7d32;
}

.quantity-display {
  min-width: 40px;
  text-align: center;
  color: #b28704;
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
  padding: 16px 24px;
}

.summary-list {
  background: transparent;
}

.summary-value {
  font-size: 1.1rem;
  font-weight: 600;
  color: #333;
}

.empty-cart-card {
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

/* Mobile Responsive Adjustments */
@media (max-width: 960px) {
  .item-controls {
    min-width: auto;
    align-items: flex-end !important;
  }

  .sticky-summary {
    position: static;
  }
}

@media (max-width: 600px) {
  .cart-item {
    flex-direction: column;
    align-items: flex-start !important;
  }

  .cart-item :deep(.v-list-item__prepend) {
    align-self: flex-start;
    margin-bottom: 12px;
  }

  .item-controls {
    width: 100%;
    flex-direction: row !important;
    justify-content: space-between !important;
    align-items: center !important;
    margin-top: 12px;
  }

  .item-price {
    margin-bottom: 0 !important;
  }

  .page-title {
    font-size: 1.5rem;
  }

  .empty-title {
    font-size: 1.5rem;
  }
}
</style>
