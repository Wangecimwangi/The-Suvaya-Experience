<template>
  <v-container class="py-8">
    <v-row>
      <v-col cols="12" class="text-center mb-6">
        <h1 class="menu-title">Bakery Menu & Food Plans</h1>
        <p class="menu-desc">Choose from our cakes (by kg), pastries, desserts, and special food plans. Book baking classes or order for your event. Pay half now, half on delivery!</p>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12">
        <v-tabs v-model="tab" color="amber-darken-2" align-tabs="center">
          <v-tab v-for="(cat, idx) in categories" :key="idx">{{ cat }}</v-tab>
        </v-tabs>
      </v-col>
    </v-row>

    <v-row>
      <v-col v-for="item in filteredMenu" :key="item.id" cols="12" sm="6" md="3">
        <v-card class="menu-card" elevation="6">
          <v-img :src="item.image" height="200px" cover></v-img>
          <v-card-title class="text-h6">{{ item.name }}</v-card-title>
          <v-card-subtitle>{{ item.category }}</v-card-subtitle>

          <v-card-text>
            <div v-if="item.kg">Available: {{ item.kg }} kg</div>
            <div>{{ item.description }}</div>

            <div class="font-weight-bold mt-2">
              <template v-if="item.price !== undefined && item.price !== null">
                Ksh{{ item.price.toLocaleString() }} <span v-if="item.kg">/ {{ item.kg }}kg</span>
              </template>
              <template v-else>N/A</template>
            </div>

            <div v-if="item.package">
              <strong>Package Includes:</strong>
              <ul>
                <li v-for="inc in item.package.includes" :key="inc">{{ inc }}</li>
              </ul>
              <div class="font-weight-bold">
                <template v-if="item.price !== undefined && item.price !== null">
                  Deposit: Ksh{{ Math.round(item.price/2).toLocaleString() }} (50%)
                </template>
                <template v-else>Deposit: N/A</template>
              </div>
            </div>

            <div v-if="item.class">
              <strong>Baking Class:</strong> {{ item.class.details }}<br />
              <div class="font-weight-bold">
                <template v-if="item.class && item.class.price !== undefined && item.class.price !== null">
                  Fee: Ksh{{ item.class.price.toLocaleString() }}
                </template>
                <template v-else>Fee: N/A</template>
              </div>
              <div class="font-weight-bold">
                <template v-if="item.class && item.class.price !== undefined && item.class.price !== null">
                  Deposit: Ksh{{ Math.round(item.class.price/2).toLocaleString() }} (50%)
                </template>
                <template v-else>Deposit: N/A</template>
              </div>
            </div>
          </v-card-text>

          <v-card-actions>
            <v-btn color="amber darken-2" variant="flat" @click="openPayDialog(item)">Pay Deposit</v-btn>
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
  font-size: 2.2rem;
  font-weight: 700;
  letter-spacing: 1px;
}
.menu-desc {
  color: #b28704;
  font-size: 1.1rem;
  margin-bottom: 1.5rem;
}
.menu-card {
  border-radius: 18px;
  background: #fffbe6;
  transition: box-shadow 0.2s;
}
.menu-card:hover {
  box-shadow: 0 8px 24px #b2870440;
}
.v-card-title {
  color: #b28704;
}
.v-btn {
  color: white;
}
</style>
