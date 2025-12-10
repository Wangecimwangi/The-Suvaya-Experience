import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  // State
  const items = ref([])

  // Load cart from localStorage on init
  const savedCart = localStorage.getItem('suvaya_cart')
  if (savedCart) {
    try {
      items.value = JSON.parse(savedCart)
    } catch (e) {
      console.error('Failed to load cart from localStorage:', e)
    }
  }

  // Computed
  const itemCount = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0)
  })

  const subtotal = computed(() => {
    return items.value.reduce((total, item) => {
      return total + (item.price * item.quantity)
    }, 0)
  })

  const deposit = computed(() => {
    return subtotal.value * 0.5 // 50% deposit
  })

  const balance = computed(() => {
    return subtotal.value - deposit.value
  })

  // Actions
  function addItem(product) {
    const existingItem = items.value.find(item => item.id === product.id)

    if (existingItem) {
      existingItem.quantity += 1
    } else {
      items.value.push({
        id: product.id,
        name: product.name,
        price: product.price,
        category: product.category,
        description: product.description,
        image: product.image,
        quantity: 1
      })
    }

    saveCart()
  }

  function removeItem(productId) {
    items.value = items.value.filter(item => item.id !== productId)
    saveCart()
  }

  function updateQuantity(productId, quantity) {
    const item = items.value.find(item => item.id === productId)

    if (item) {
      if (quantity <= 0) {
        removeItem(productId)
      } else {
        item.quantity = quantity
        saveCart()
      }
    }
  }

  function incrementQuantity(productId) {
    const item = items.value.find(item => item.id === productId)
    if (item) {
      item.quantity += 1
      saveCart()
    }
  }

  function decrementQuantity(productId) {
    const item = items.value.find(item => item.id === productId)
    if (item) {
      if (item.quantity > 1) {
        item.quantity -= 1
      } else {
        removeItem(productId)
      }
      saveCart()
    }
  }

  function clearCart() {
    items.value = []
    saveCart()
  }

  function isInCart(productId) {
    return items.value.some(item => item.id === productId)
  }

  function getItemQuantity(productId) {
    const item = items.value.find(item => item.id === productId)
    return item ? item.quantity : 0
  }

  function saveCart() {
    localStorage.setItem('suvaya_cart', JSON.stringify(items.value))
  }

  return {
    // State
    items,

    // Computed
    itemCount,
    subtotal,
    deposit,
    balance,

    // Actions
    addItem,
    removeItem,
    updateQuantity,
    incrementQuantity,
    decrementQuantity,
    clearCart,
    isInCart,
    getItemQuantity
  }
})
