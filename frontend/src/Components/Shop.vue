<script setup>
import { ref, onMounted, computed } from 'vue'
import { menuAPI } from '@/services/api'
import { useCartStore } from '@/stores/cart'

const cartStore = useCartStore()

const menuItems = ref([])
const loading = ref(false)
const selectedCategory = ref('all')
const searchQuery = ref('')
const showSnackbar = ref(false)
const snackbarMessage = ref('')

const categories = [
  { value: 'all', title: 'All Items', icon: 'mdi-food' },
  { value: 'cakes', title: 'Cakes', icon: 'mdi-cake' },
  { value: 'pastries', title: 'Pastries', icon: 'mdi-croissant' },
  { value: 'breads', title: 'Breads', icon: 'mdi-baguette' },
  { value: 'cookies', title: 'Cookies', icon: 'mdi-cookie' },
  { value: 'packages', title: 'Packages', icon: 'mdi-package-variant' },
  { value: 'baking_classes', title: 'Baking Classes', icon: 'mdi-school' }
]

const filteredItems = computed(() => {
  let filtered = menuItems.value

  // Filter by category
  if (selectedCategory.value !== 'all') {
    filtered = filtered.filter(item => item.category === selectedCategory.value)
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(item =>
      item.name?.toLowerCase().includes(query) ||
      item.description?.toLowerCase().includes(query)
    )
  }

  // Only show available items
  return filtered.filter(item => item.is_available)
})

async function loadMenuItems() {
  loading.value = true
  try {
    const response = await menuAPI.getAll()
    menuItems.value = response.data || []
  } catch (error) {
    console.error('Error loading menu items:', error)
    // Fallback to sample data if API fails
    menuItems.value = getSampleMenuItems()
  } finally {
    loading.value = false
  }
}

function getSampleMenuItems() {
  return [
    {
      id: 1,
      name: 'Chocolate Cake',
      description: 'Rich and moist chocolate cake with chocolate frosting',
      category: 'cakes',
      price: 3500,
      is_available: true,
      image: null
    },
    {
      id: 2,
      name: 'Vanilla Cupcakes',
      description: 'Light and fluffy vanilla cupcakes (set of 6)',
      category: 'pastries',
      price: 1200,
      is_available: true,
      image: null
    },
    {
      id: 3,
      name: 'Croissants',
      description: 'Buttery and flaky croissants (set of 4)',
      category: 'breads',
      price: 800,
      is_available: true,
      image: null
    }
  ]
}

function addToCart(item) {
  cartStore.addItem(item)
  snackbarMessage.value = `${item.name} added to cart!`
  showSnackbar.value = true
}

function formatPrice(price) {
  return new Intl.NumberFormat('en-KE', {
    style: 'currency',
    currency: 'KES'
  }).format(price)
}

function getCategoryIcon(category) {
  const cat = categories.find(c => c.value === category)
  return cat ? cat.icon : 'mdi-food'
}

onMounted(() => {
  loadMenuItems()
})
</script>

<template>
  <v-container class="shop-container py-6 py-md-8">
    <!-- Page Header -->
    <v-row class="mb-6">
      <v-col cols="12" class="text-center">
        <h1 class="page-title">
          <v-icon color="amber-darken-2" size="40" class="mr-2">mdi-storefront</v-icon>
          Our Menu
        </h1>
        <p class="page-subtitle">Browse our delicious selection and add items to your cart</p>
      </v-col>
    </v-row>

    <!-- Search Bar -->
    <v-row class="mb-4">
      <v-col cols="12" md="8" class="mx-auto">
        <v-text-field
          v-model="searchQuery"
          label="Search menu items..."
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          color="amber-darken-2"
          clearable
          hide-details
        ></v-text-field>
      </v-col>
    </v-row>

    <!-- Category Tabs -->
    <v-row class="mb-6">
      <v-col cols="12">
        <v-chip-group
          v-model="selectedCategory"
          selected-class="text-amber-darken-2"
          mandatory
          class="d-flex justify-center flex-wrap"
        >
          <v-chip
            v-for="category in categories"
            :key="category.value"
            :value="category.value"
            size="large"
            variant="outlined"
            color="amber-darken-2"
            class="ma-1"
          >
            <v-icon start>{{ category.icon }}</v-icon>
            {{ category.title }}
          </v-chip>
        </v-chip-group>
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
        <p class="mt-4">Loading menu items...</p>
      </v-col>
    </v-row>

    <!-- Menu Items Grid -->
    <v-row v-else-if="filteredItems.length > 0">
      <v-col
        v-for="item in filteredItems"
        :key="item.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
      >
        <v-card class="product-card" elevation="3">
          <!-- Product Image -->
          <v-img
            :src="item.image || 'https://via.placeholder.com/300x200?text=' + encodeURIComponent(item.name)"
            height="200"
            cover
            class="product-image"
          >
            <div class="category-badge">
              <v-chip size="small" color="amber-darken-2">
                <v-icon start size="small">{{ getCategoryIcon(item.category) }}</v-icon>
                {{ item.category }}
              </v-chip>
            </div>
          </v-img>

          <v-card-text class="product-details">
            <h3 class="product-name">{{ item.name }}</h3>
            <p class="product-description">{{ item.description }}</p>

            <div class="d-flex justify-space-between align-center mt-3">
              <div class="product-price">{{ formatPrice(item.price) }}</div>

              <div v-if="cartStore.isInCart(item.id)">
                <v-chip color="green" size="small">
                  <v-icon start size="small">mdi-check</v-icon>
                  In Cart ({{ cartStore.getItemQuantity(item.id) }})
                </v-chip>
              </div>
            </div>
          </v-card-text>

          <v-card-actions>
            <v-btn
              v-if="!cartStore.isInCart(item.id)"
              color="amber-darken-2"
              block
              @click="addToCart(item)"
            >
              <v-icon class="mr-2">mdi-cart-plus</v-icon>
              Add to Cart
            </v-btn>

            <div v-else class="d-flex align-center justify-center w-100">
              <v-btn
                icon
                size="small"
                color="amber-darken-2"
                @click="cartStore.decrementQuantity(item.id)"
              >
                <v-icon>mdi-minus</v-icon>
              </v-btn>

              <span class="mx-4 text-h6">{{ cartStore.getItemQuantity(item.id) }}</span>

              <v-btn
                icon
                size="small"
                color="amber-darken-2"
                @click="cartStore.incrementQuantity(item.id)"
              >
                <v-icon>mdi-plus</v-icon>
              </v-btn>
            </div>
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
              mdi-food-off
            </v-icon>
            <h3 class="mb-2">No Items Found</h3>
            <p class="text-grey mb-4">
              {{ searchQuery ? 'Try a different search term' : 'No items available in this category' }}
            </p>
            <v-btn
              v-if="selectedCategory !== 'all'"
              color="amber-darken-2"
              @click="selectedCategory = 'all'"
            >
              View All Items
            </v-btn>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Cart Summary FAB -->
    <v-fab-transition>
      <v-btn
        v-if="cartStore.itemCount > 0"
        color="amber-darken-2"
        icon
        size="large"
        fixed
        bottom
        right
        class="cart-fab"
        to="/cart"
      >
        <v-badge :content="cartStore.itemCount" color="red">
          <v-icon size="large">mdi-cart</v-icon>
        </v-badge>
      </v-btn>
    </v-fab-transition>

    <!-- Success Snackbar -->
    <v-snackbar v-model="showSnackbar" color="success" :timeout="2000">
      <v-icon class="mr-2">mdi-check-circle</v-icon>
      {{ snackbarMessage }}
    </v-snackbar>
  </v-container>
</template>

<style scoped>
.shop-container {
  background: #f5f5f5;
  min-height: calc(100vh - 64px);
}

.page-title {
  color: #b28704;
  font-size: 2.5rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-subtitle {
  color: #7a7a7a;
  font-size: 1.1rem;
  margin-top: 0.5rem;
}

.product-card {
  background: #fffbe6;
  border-radius: 12px;
  height: 100%;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
}

.product-image {
  border-radius: 12px 12px 0 0;
  position: relative;
}

.category-badge {
  position: absolute;
  top: 10px;
  right: 10px;
}

.product-details {
  flex-grow: 1;
}

.product-name {
  color: #b28704;
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.product-description {
  color: #7a7a7a;
  font-size: 0.875rem;
  margin-bottom: 0;
}

.product-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #2e7d32;
}

.empty-state {
  background: #fffbe6;
  border-radius: 16px;
}

.cart-fab {
  margin-bottom: 70px;
  margin-right: 16px;
}

@media (max-width: 600px) {
  .page-title {
    font-size: 1.75rem;
  }
}
</style>
