<template>
  <v-container class="py-6 py-md-10">
    <v-row>
      <v-col cols="12" class="text-center mb-4 mb-md-8">
        <h1 class="menu-title">Bakery Menu & Food Plans</h1>
        <p class="menu-desc px-2 px-md-0">Choose from our cakes (by kg), pastries, desserts, and special food plans. Book baking classes or order for your event. Pay half now, half on delivery!</p>

        <!-- Shop CTA -->
        <v-btn
          color="amber-darken-2"
          size="large"
          to="/shop"
          prepend-icon="mdi-cart"
          class="mt-4"
        >
          Start Shopping & Add to Cart
        </v-btn>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12">
        <v-tabs
          v-model="tab"
          color="amber-darken-2"
          align-tabs="center"
          show-arrows
          class="menu-tabs"
        >
          <v-tab v-for="(cat, idx) in categories" :key="idx" class="text-capitalize">{{ cat }}</v-tab>
        </v-tabs>
      </v-col>
    </v-row>

    <v-row class="mt-4">
      <v-col v-for="item in filteredMenu" :key="item.id" cols="12" sm="6" lg="4" xl="3">
        <v-card class="menu-card" elevation="4" height="100%">
          <v-img :src="item.image" height="200px" cover></v-img>

          <v-card-title class="text-h6 pb-2">{{ item.name }}</v-card-title>
          <v-card-subtitle class="pb-3">{{ item.category }}</v-card-subtitle>

          <v-card-text class="flex-grow-1">
            <div v-if="item.kg" class="mb-2">
              <v-chip size="small" color="amber-lighten-4" class="text-caption">
                Available: {{ item.kg }} kg
              </v-chip>
            </div>

            <p class="text-body-2 mb-3">{{ item.description }}</p>

            <div class="price-tag mb-3">
              <template v-if="item.price !== undefined && item.price !== null">
                <span class="text-h6 font-weight-bold">Ksh {{ item.price.toLocaleString() }}</span>
                <span v-if="item.kg" class="text-caption ml-1">/ {{ item.kg }}kg</span>
              </template>
              <template v-else>
                <span class="text-caption">Price N/A</span>
              </template>
            </div>

            <div v-if="item.package" class="package-info mb-3">
              <p class="text-subtitle-2 font-weight-bold mb-1">Package Includes:</p>
              <ul class="text-caption pl-4">
                <li v-for="inc in item.package.includes" :key="inc">{{ inc }}</li>
              </ul>
              <div class="deposit-info mt-2">
                <template v-if="item.price !== undefined && item.price !== null">
                  <v-chip size="small" color="green-lighten-4">
                    Deposit: Ksh {{ Math.round(item.price/2).toLocaleString() }} (50%)
                  </v-chip>
                </template>
              </div>
            </div>

            <div v-if="item.class" class="class-info mb-3">
              <p class="text-subtitle-2 font-weight-bold mb-1">Baking Class</p>
              <p class="text-caption mb-2">{{ item.class.details }}</p>
              <div class="d-flex flex-column gap-1">
                <template v-if="item.class && item.class.price !== undefined && item.class.price !== null">
                  <div class="text-body-2 font-weight-bold">Fee: Ksh {{ item.class.price.toLocaleString() }}</div>
                  <v-chip size="small" color="green-lighten-4">
                    Deposit: Ksh {{ Math.round(item.class.price/2).toLocaleString() }} (50%)
                  </v-chip>
                </template>
              </div>
            </div>
          </v-card-text>

          <v-card-actions class="pt-0">
            <v-btn color="amber-darken-2" variant="flat" block @click="openPayDialog(item)">
              Pay Deposit
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="payDialog" max-width="420">
      <v-card>
        <v-card-title>Confirm Deposit</v-card-title>
        <v-card-text>
          <div v-if="selectedItem">Pay 50% deposit for <strong>{{ selectedItem.name }}</strong> — <span v-if="selectedItem.price">KSh {{ Math.round(selectedItem.price/2).toLocaleString() }}</span><span v-else>N/A</span></div>
        </v-card-text>
        <v-card-actions>
          <v-btn color="amber darken-2" @click="confirmPay">Pay</v-btn>
          <v-btn text @click="payDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue'
import { menuItems as sharedMenu } from '@/data/menuData'
import { addBooking } from '@/api/bookings'

const menuItems = sharedMenu
function normalizeCategory(cat) {
  if (!cat) return 'Uncategorized'
  return cat.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()).join(' ')
}
const tab = ref(0)
const categories = computed(() => {
  const set = new Set()
  for (const it of menuItems) {
    set.add(normalizeCategory(it.category))
  }
  // default tabs we want to always show (in this order)
  const defaultTabs = ['Cakes', 'Pastries', 'Desserts', 'Drinks', 'Food Plans', 'Baking Classes', 'Snacks']
  // include any default tabs even if not present in data
  for (const d of defaultTabs) set.add(d)

  const rest = Array.from(set).filter(c => !defaultTabs.includes(c)).sort()
  const sorted = defaultTabs.filter(o => set.has(o)).concat(rest)
  return ['All', ...sorted]
})
const menuItem = sharedMenu

const payDialog = ref(false)
const selectedItem = ref(null)

function openPayDialog(item) {
  selectedItem.value = item
  payDialog.value = true
}

function confirmPay() {
  if (!selectedItem.value) return
  addBooking(new Date().toISOString().slice(0,10), { itemId: selectedItem.value.id, deposit: selectedItem.value.price ? Math.round(selectedItem.value.price/2) : 0 })
  alert(`Deposit recorded for ${selectedItem.value.name}`)
  payDialog.value = false
  selectedItem.value = null
}

const filteredMenu = computed(() => {
  if (categories[tab.value] === 'All') return menuItems
  return menuItems.filter(item => item.category === categories[tab.value])
})
</script>

<style scoped>
.menu-title {
  color: #b28704;
  font-size: 1.8rem;
  font-weight: 700;
  letter-spacing: 1px;
}

@media (min-width: 600px) {
  .menu-title {
    font-size: 2.2rem;
  }
}

.menu-desc {
  color: #b28704;
  font-size: 0.95rem;
  margin-bottom: 1rem;
  line-height: 1.6;
}

@media (min-width: 600px) {
  .menu-desc {
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
  }
}

.menu-tabs {
  margin-bottom: 1rem;
}

.menu-card {
  border-radius: 16px;
  background: #fffbe6;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
}

.menu-card:hover {
  box-shadow: 0 8px 24px rgba(178, 135, 4, 0.25);
  transform: translateY(-4px);
}

.menu-card .v-card-title {
  color: #b28704;
  line-height: 1.3;
}

.menu-card .v-card-subtitle {
  color: #7a7a7a;
  font-size: 0.875rem;
}

.price-tag {
  color: #b28704;
}

.package-info,
.class-info {
  background: #fff8dc;
  padding: 12px;
  border-radius: 8px;
}

.v-btn {
  color: white;
  text-transform: none;
}
</style>
