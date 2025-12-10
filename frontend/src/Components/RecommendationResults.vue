<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'

const router = useRouter()
const cartStore = useCartStore()
const recommendationData = ref(null)
const recommendations = ref([])
const showSnackbar = ref(false)
const snackbarMessage = ref('')

// Package database (this would come from API in production)
const packages = [
  {
    id: 'pkg-1',
    name: 'Classic Birthday Package',
    description: 'Perfect for intimate birthday celebrations',
    price: 8500,
    suitable_for: ['birthday'],
    guest_range: { min: 10, max: 30 },
    budget_tier: 'budget',
    includes: [
      '1kg Chocolate or Vanilla Cake',
      '24 Cupcakes',
      '2 Dozen Cookies',
      'Basic Decoration',
      'Birthday Candles & Knife'
    ],
    image: '/Cakes/david-holifield-_zP1AHiq6Vg-unsplash.jpg',
    category: 'packages'
  },
  {
    id: 'pkg-2',
    name: 'Deluxe Birthday Experience',
    description: 'Make your birthday unforgettable',
    price: 25000,
    suitable_for: ['birthday'],
    guest_range: { min: 30, max: 75 },
    budget_tier: 'moderate',
    includes: [
      '2kg Custom Design Cake',
      '48 Cupcakes',
      '4 Dozen Cookies',
      'Premium Decoration Package',
      'Birthday Candles, Knife & Plates',
      'Photo Cake Topper'
    ],
    image: '/Cakes/hailey-tong-3ArfXaXxLCM-unsplash.jpg',
    category: 'packages'
  },
  {
    id: 'pkg-3',
    name: 'Premium Birthday Celebration',
    description: 'Ultimate birthday party experience',
    price: 52000,
    suitable_for: ['birthday'],
    guest_range: { min: 75, max: 150 },
    budget_tier: 'premium',
    includes: [
      '3kg Premium Custom Cake',
      '100 Cupcakes',
      '8 Dozen Assorted Cookies',
      'Luxury Decoration & Setup',
      'Dessert Table Display',
      'Party Favors',
      'Professional Photos'
    ],
    image: '/Cakes/jodi-pender-54P2t0sEVKc-unsplash.jpg',
    category: 'packages'
  },
  {
    id: 'pkg-4',
    name: 'Elegant Wedding Package',
    description: 'Beautiful wedding celebration basics',
    price: 45000,
    suitable_for: ['wedding', 'anniversary'],
    guest_range: { min: 50, max: 100 },
    budget_tier: 'moderate',
    includes: [
      '3-Tier Wedding Cake (5kg)',
      '100 Cupcakes',
      'Cake Cutting Set',
      'Elegant Decoration',
      'Cake Stand & Display'
    ],
    image: '/Cakes/deva-williamson-tW0Ix_Ajg6Y-unsplash.jpg',
    category: 'packages'
  },
  {
    id: 'pkg-5',
    name: 'Grand Wedding Experience',
    description: 'Luxury wedding dessert package',
    price: 85000,
    suitable_for: ['wedding'],
    guest_range: { min: 100, max: 250 },
    budget_tier: 'luxury',
    includes: [
      '5-Tier Custom Wedding Cake (8kg)',
      '200 Cupcakes',
      'Dessert Bar with 5 Varieties',
      'Premium Decoration & Setup',
      'Cake Cutting Ceremony Setup',
      'Professional Cake Photography'
    ],
    image: '/Cakes/camera-crew-Y7Gv_O-agc0-unsplash.jpg',
    category: 'packages'
  },
  {
    id: 'pkg-6',
    name: 'Corporate Event Package',
    description: 'Professional corporate catering',
    price: 18000,
    suitable_for: ['corporate'],
    guest_range: { min: 20, max: 50 },
    budget_tier: 'moderate',
    includes: [
      '2kg Corporate Logo Cake',
      '50 Mini Cupcakes',
      '5 Dozen Assorted Cookies',
      'Professional Presentation',
      'Corporate Branding Options'
    ],
    image: '/Cakes/american-heritage-chocolate-vdx5hPQhXFk-unsplash.jpg',
    category: 'packages'
  },
  {
    id: 'pkg-7',
    name: 'Baby Shower Delight',
    description: 'Adorable baby shower treats',
    price: 15000,
    suitable_for: ['baby_shower'],
    guest_range: { min: 20, max: 50 },
    budget_tier: 'moderate',
    includes: [
      '1.5kg Themed Cake',
      '36 Themed Cupcakes',
      '3 Dozen Baby-themed Cookies',
      'Gender Reveal Option',
      'Cute Decorations'
    ],
    image: '/Cakes/sincerely-media-z10eH_RA6ZQ-unsplash.jpg',
    category: 'packages'
  },
  {
    id: 'pkg-8',
    name: 'Graduation Celebration',
    description: 'Celebrate academic achievements',
    price: 12000,
    suitable_for: ['graduation'],
    guest_range: { min: 15, max: 40 },
    budget_tier: 'budget',
    includes: [
      '1.5kg Graduation Theme Cake',
      '30 Cupcakes',
      '3 Dozen Cookies',
      'Cap & Diploma Decorations'
    ],
    image: '/Cakes/gruescu-ovidiu-UiJtiiAmJec-unsplash.jpg',
    category: 'packages'
  },
  {
    id: 'pkg-9',
    name: 'Budget-Friendly Party Pack',
    description: 'Great value for small gatherings',
    price: 5500,
    suitable_for: ['birthday', 'other'],
    guest_range: { min: 5, max: 15 },
    budget_tier: 'budget',
    includes: [
      '0.5kg Cake',
      '12 Cupcakes',
      '1 Dozen Cookies',
      'Basic Decorations'
    ],
    image: '/Cakes/annie-spratt-6SHd7Q-l1UQ-unsplash.jpg',
    category: 'packages'
  },
  {
    id: 'pkg-10',
    name: 'Premium Anniversary Package',
    description: 'Romantic anniversary celebration',
    price: 35000,
    suitable_for: ['anniversary'],
    guest_range: { min: 30, max: 80 },
    budget_tier: 'premium',
    includes: [
      '2.5kg Romantic Theme Cake',
      '60 Cupcakes',
      'Heart-shaped Cookies (4 dozen)',
      'Premium Decoration',
      'Champagne Pairing Suggestions'
    ],
    image: '/Cakes/anthony-espinosa-6iqpLKqeaE0-unsplash.jpg',
    category: 'packages'
  }
]

function getBudgetRange(budgetTier) {
  const ranges = {
    'budget': { min: 0, max: 10000 },
    'moderate': { min: 10000, max: 30000 },
    'premium': { min: 30000, max: 60000 },
    'luxury': { min: 60000, max: 999999 }
  }
  return ranges[budgetTier] || { min: 0, max: 999999 }
}

function calculateMatchScore(pkg, data) {
  let score = 0

  // Event type match (40 points)
  if (pkg.suitable_for.includes(data.eventType)) {
    score += 40
  }

  // Guest count match (30 points)
  if (data.guestCount >= pkg.guest_range.min && data.guestCount <= pkg.guest_range.max) {
    score += 30
  } else if (data.guestCount < pkg.guest_range.min) {
    // Penalty for packages too large
    const diff = pkg.guest_range.min - data.guestCount
    score += Math.max(0, 30 - (diff * 0.5))
  } else {
    // Penalty for packages too small
    const diff = data.guestCount - pkg.guest_range.max
    score += Math.max(0, 30 - (diff * 0.3))
  }

  // Budget match (30 points)
  if (pkg.budget_tier === data.budget) {
    score += 30
  } else {
    // Partial points for adjacent budget tiers
    const tiers = ['budget', 'moderate', 'premium', 'luxury']
    const pkgIndex = tiers.indexOf(pkg.budget_tier)
    const dataIndex = tiers.indexOf(data.budget)
    const diff = Math.abs(pkgIndex - dataIndex)
    score += Math.max(0, 30 - (diff * 10))
  }

  return score
}

function generateRecommendations(data) {
  // Calculate match score for each package
  const scoredPackages = packages.map(pkg => ({
    ...pkg,
    matchScore: calculateMatchScore(pkg, data),
    is_available: true
  }))

  // Sort by match score (highest first)
  scoredPackages.sort((a, b) => b.matchScore - a.matchScore)

  // Return top 3 recommendations
  return scoredPackages.slice(0, 3)
}

function formatPrice(price) {
  return new Intl.NumberFormat('en-KE', {
    style: 'currency',
    currency: 'KES'
  }).format(price)
}

function getMatchLabel(score) {
  if (score >= 90) return { text: 'Perfect Match', color: 'green' }
  if (score >= 70) return { text: 'Great Match', color: 'blue' }
  if (score >= 50) return { text: 'Good Match', color: 'orange' }
  return { text: 'Possible Option', color: 'grey' }
}

function addToCart(pkg) {
  cartStore.addItem(pkg)
  snackbarMessage.value = `${pkg.name} added to cart!`
  showSnackbar.value = true
}

function viewAllPackages() {
  router.push('/shop')
}

function startOver() {
  router.push('/package-recommendation')
}

onMounted(() => {
  const data = JSON.parse(localStorage.getItem('recommendationData') || '{}')

  if (!data || !data.eventType) {
    router.push('/package-recommendation')
    return
  }

  recommendationData.value = data
  recommendations.value = generateRecommendations(data)

  // Clean up
  localStorage.removeItem('recommendationData')
})
</script>

<template>
  <v-container class="results-container py-6 py-md-8" v-if="recommendationData">
    <v-row justify="center">
      <v-col cols="12" lg="10">
        <!-- Header -->
        <div class="text-center mb-6">
          <v-icon color="green" size="80" class="mb-3">mdi-check-circle</v-icon>
          <h1 class="page-title mb-2">Your Perfect Packages</h1>
          <p class="page-subtitle">
            Based on your preferences, we recommend these packages for your
            <span class="font-weight-bold text-capitalize">{{ recommendationData.eventType }}</span>
            event with {{ recommendationData.guestCount }} guests
          </p>
        </div>

        <!-- Recommendations -->
        <v-row>
          <v-col
            v-for="(pkg, index) in recommendations"
            :key="pkg.id"
            cols="12"
            md="4"
          >
            <v-card class="recommendation-card" elevation="4" height="100%">
              <!-- Best Match Badge -->
              <div v-if="index === 0" class="best-match-badge">
                <v-chip color="green" size="small">
                  <v-icon start size="small">mdi-star</v-icon>
                  Best Match
                </v-chip>
              </div>

              <!-- Package Image -->
              <v-img
                :src="pkg.image || 'https://via.placeholder.com/400x250?text=' + encodeURIComponent(pkg.name)"
                height="200"
                cover
                class="package-image"
              ></v-img>

              <v-card-text class="package-content">
                <!-- Match Score -->
                <div class="match-indicator mb-3">
                  <v-chip
                    :color="getMatchLabel(pkg.matchScore).color"
                    size="small"
                  >
                    <v-icon start size="small">mdi-check-decagram</v-icon>
                    {{ getMatchLabel(pkg.matchScore).text }} ({{ pkg.matchScore }}%)
                  </v-chip>
                </div>

                <h3 class="package-name">{{ pkg.name }}</h3>
                <p class="package-description">{{ pkg.description }}</p>

                <div class="package-price mb-3">
                  {{ formatPrice(pkg.price) }}
                  <v-chip size="x-small" color="green-lighten-4" class="ml-2">
                    50% deposit: {{ formatPrice(pkg.price * 0.5) }}
                  </v-chip>
                </div>

                <div class="package-details mb-3">
                  <div class="detail-item">
                    <v-icon size="small" color="amber-darken-2">mdi-account-group</v-icon>
                    <span>{{ pkg.guest_range.min }}-{{ pkg.guest_range.max }} guests</span>
                  </div>
                </div>

                <v-divider class="my-3"></v-divider>

                <div class="package-includes">
                  <h4 class="includes-title">Package Includes:</h4>
                  <ul class="includes-list">
                    <li v-for="item in pkg.includes" :key="item">{{ item }}</li>
                  </ul>
                </div>
              </v-card-text>

              <v-card-actions class="pa-4">
                <v-btn
                  color="amber-darken-2"
                  block
                  @click="addToCart(pkg)"
                  prepend-icon="mdi-cart-plus"
                >
                  Add to Cart
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>

        <!-- Additional Actions -->
        <v-row class="mt-6">
          <v-col cols="12">
            <v-card class="actions-card" elevation="2">
              <v-card-text class="pa-6">
                <h3 class="actions-title mb-4">Not quite what you're looking for?</h3>
                <v-row>
                  <v-col cols="12" sm="6" md="3">
                    <v-btn
                      color="amber-darken-2"
                      variant="outlined"
                      block
                      prepend-icon="mdi-storefront"
                      @click="viewAllPackages"
                    >
                      Browse All Packages
                    </v-btn>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
                    <v-btn
                      color="amber-darken-2"
                      variant="outlined"
                      block
                      prepend-icon="mdi-refresh"
                      @click="startOver"
                    >
                      Start Over
                    </v-btn>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
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
                  <v-col cols="12" sm="6" md="3">
                    <v-btn
                      color="amber-darken-2"
                      variant="outlined"
                      block
                      prepend-icon="mdi-phone"
                      to="/contactus"
                    >
                      Contact Us
                    </v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Why These Packages -->
        <v-row class="mt-4">
          <v-col cols="12">
            <v-card class="info-card" elevation="2">
              <v-card-title class="info-title">
                <v-icon color="amber-darken-2" class="mr-2">mdi-information</v-icon>
                Why We Recommend These Packages
              </v-card-title>
              <v-card-text>
                <v-list>
                  <v-list-item>
                    <template v-slot:prepend>
                      <v-icon color="green">mdi-check-circle</v-icon>
                    </template>
                    <v-list-item-title>Perfect for your {{ recommendationData.eventType }} event</v-list-item-title>
                  </v-list-item>
                  <v-list-item>
                    <template v-slot:prepend>
                      <v-icon color="green">mdi-check-circle</v-icon>
                    </template>
                    <v-list-item-title>Sized appropriately for {{ recommendationData.guestCount }} guests</v-list-item-title>
                  </v-list-item>
                  <v-list-item>
                    <template v-slot:prepend>
                      <v-icon color="green">mdi-check-circle</v-icon>
                    </template>
                    <v-list-item-title>Fits within your budget preferences</v-list-item-title>
                  </v-list-item>
                  <v-list-item v-if="recommendationData.cakePreference && recommendationData.cakePreference !== 'no_preference'">
                    <template v-slot:prepend>
                      <v-icon color="green">mdi-check-circle</v-icon>
                    </template>
                    <v-list-item-title class="text-capitalize">Includes your preferred {{ recommendationData.cakePreference }} cake</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>

    <!-- Success Snackbar -->
    <v-snackbar v-model="showSnackbar" color="success" :timeout="2000">
      <v-icon class="mr-2">mdi-check-circle</v-icon>
      {{ snackbarMessage }}
    </v-snackbar>
  </v-container>
</template>

<style scoped>
.results-container {
  background: #f5f5f5;
  min-height: calc(100vh - 64px);
}

.page-title {
  color: #b28704;
  font-size: 2rem;
  font-weight: 700;
}

@media (min-width: 960px) {
  .page-title {
    font-size: 2.5rem;
  }
}

.page-subtitle {
  color: #7a7a7a;
  font-size: 1rem;
  line-height: 1.6;
}

@media (min-width: 960px) {
  .page-subtitle {
    font-size: 1.1rem;
  }
}

.recommendation-card {
  background: #fffbe6;
  border-radius: 16px;
  position: relative;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.recommendation-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(178, 135, 4, 0.3) !important;
}

.best-match-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 2;
}

.package-image {
  border-radius: 16px 16px 0 0;
}

.package-content {
  flex-grow: 1;
}

.match-indicator {
  display: flex;
  justify-content: flex-start;
}

.package-name {
  color: #b28704;
  font-size: 1.3rem;
  font-weight: 700;
  margin-bottom: 8px;
}

.package-description {
  color: #7a7a7a;
  font-size: 0.95rem;
  margin-bottom: 12px;
}

.package-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2e7d32;
}

.package-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #333;
  font-size: 0.95rem;
}

.includes-title {
  color: #b28704;
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 8px;
}

.includes-list {
  list-style-position: inside;
  color: #333;
  font-size: 0.9rem;
  line-height: 1.8;
}

.includes-list li {
  margin-bottom: 4px;
}

.actions-card,
.info-card {
  background: #fffbe6;
  border-radius: 16px;
}

.actions-title,
.info-title {
  color: #b28704;
  font-size: 1.2rem;
  font-weight: 700;
}

@media (max-width: 600px) {
  .page-title {
    font-size: 1.5rem;
  }

  .package-name {
    font-size: 1.1rem;
  }

  .package-price {
    font-size: 1.25rem;
  }
}
</style>
