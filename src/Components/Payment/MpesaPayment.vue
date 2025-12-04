<script setup>
import { ref, computed } from 'vue'
import { mpesaAPI } from '@/services/api'

const props = defineProps({
  amount: {
    type: Number,
    required: true
  },
  reference: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: 'Payment for Suvaya Experience'
  }
})

const emit = defineEmits(['payment-success', 'payment-failed', 'payment-cancelled'])

const phoneNumber = ref('')
const loading = ref(false)
const checking = ref(false)
const errorMessage = ref('')
const checkoutRequestId = ref(null)
const paymentStatus = ref(null)
const showStatusDialog = ref(false)
const statusCheckInterval = ref(null)

const formattedAmount = computed(() => {
  return new Intl.NumberFormat('en-KE', {
    style: 'currency',
    currency: 'KES'
  }).format(props.amount)
})

const phoneRules = [
  v => !!v || 'Phone number is required',
  v => /^(07|01|\+2547|\+2541|2547|2541)\d{8}$/.test(v.replace(/\s/g, '')) || 'Invalid phone number format'
]

async function initiatePayment() {
  if (!phoneNumber.value) {
    errorMessage.value = 'Please enter your phone number'
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const response = await mpesaAPI.initiatePayment({
      phone: phoneNumber.value,
      amount: props.amount,
      reference: props.reference,
      description: props.description
    })

    if (response.success) {
      checkoutRequestId.value = response.data.checkout_request_id
      showStatusDialog.value = true
      paymentStatus.value = 'pending'

      // Start checking payment status
      startStatusCheck()
    }
  } catch (error) {
    errorMessage.value = error.message || 'Failed to initiate payment. Please try again.'
  } finally {
    loading.value = false
  }
}

function startStatusCheck() {
  checking.value = true
  let attempts = 0
  const maxAttempts = 60 // Check for 2 minutes (every 2 seconds)

  statusCheckInterval.value = setInterval(async () => {
    attempts++

    try {
      const response = await mpesaAPI.queryStatus(checkoutRequestId.value)

      if (response.data.status === 'completed') {
        // Payment successful
        clearInterval(statusCheckInterval.value)
        paymentStatus.value = 'success'
        checking.value = false

        setTimeout(() => {
          showStatusDialog.value = false
          emit('payment-success', response.data)
        }, 2000)

      } else if (response.data.status === 'failed') {
        // Payment failed
        clearInterval(statusCheckInterval.value)
        paymentStatus.value = 'failed'
        checking.value = false
        emit('payment-failed', response.data)

      } else if (response.data.status === 'timeout') {
        // Payment timeout
        clearInterval(statusCheckInterval.value)
        paymentStatus.value = 'timeout'
        checking.value = false
        emit('payment-failed', response.data)
      }

      // Stop checking after max attempts
      if (attempts >= maxAttempts && paymentStatus.value === 'pending') {
        clearInterval(statusCheckInterval.value)
        paymentStatus.value = 'timeout'
        checking.value = false
        emit('payment-failed', { message: 'Payment check timed out' })
      }

    } catch (error) {
      console.error('Status check error:', error)
    }
  }, 2000) // Check every 2 seconds
}

function cancelPayment() {
  if (statusCheckInterval.value) {
    clearInterval(statusCheckInterval.value)
  }
  showStatusDialog.value = false
  paymentStatus.value = null
  checking.value = false
  emit('payment-cancelled')
}

function getStatusIcon() {
  switch (paymentStatus.value) {
    case 'pending':
      return 'mdi-clock-outline'
    case 'success':
      return 'mdi-check-circle'
    case 'failed':
    case 'timeout':
      return 'mdi-close-circle'
    default:
      return 'mdi-help-circle'
  }
}

function getStatusColor() {
  switch (paymentStatus.value) {
    case 'pending':
      return 'orange'
    case 'success':
      return 'green'
    case 'failed':
    case 'timeout':
      return 'red'
    default:
      return 'grey'
  }
}

function getStatusMessage() {
  switch (paymentStatus.value) {
    case 'pending':
      return 'Please check your phone and enter your M-Pesa PIN to complete the payment'
    case 'success':
      return 'Payment completed successfully!'
    case 'failed':
      return 'Payment failed. Please try again.'
    case 'timeout':
      return 'Payment request timed out. Please try again.'
    default:
      return ''
  }
}
</script>

<template>
  <v-card class="mpesa-card pa-4 pa-md-6" elevation="4">
    <v-card-title class="mpesa-title text-center mb-3">
      <v-icon color="green" size="48" class="mr-2">mdi-cellphone</v-icon>
      <div>Pay with M-Pesa</div>
    </v-card-title>

    <v-card-subtitle class="text-center mb-4">
      Lipa na M-Pesa - Fast and Secure
    </v-card-subtitle>

    <v-card-text>
      <!-- Amount Display -->
      <v-alert type="info" variant="tonal" class="mb-4">
        <div class="d-flex align-center justify-space-between">
          <span class="text-subtitle-1">Amount to Pay:</span>
          <span class="text-h6 font-weight-bold">{{ formattedAmount }}</span>
        </div>
      </v-alert>

      <!-- Phone Number Input -->
      <v-text-field
        v-model="phoneNumber"
        label="M-Pesa Phone Number"
        placeholder="e.g., 0712345678"
        variant="outlined"
        color="green"
        prepend-inner-icon="mdi-phone"
        :rules="phoneRules"
        :disabled="loading"
        class="mb-2"
        hint="Enter the phone number registered with M-Pesa"
        persistent-hint
      ></v-text-field>

      <!-- Error Message -->
      <v-alert
        v-if="errorMessage"
        type="error"
        variant="tonal"
        class="mb-4"
        closable
        @click:close="errorMessage = ''"
      >
        {{ errorMessage }}
      </v-alert>

      <!-- How it works -->
      <v-expansion-panels class="mb-4">
        <v-expansion-panel>
          <v-expansion-panel-title>
            <v-icon class="mr-2">mdi-information</v-icon>
            How it Works
          </v-expansion-panel-title>
          <v-expansion-panel-text>
            <ol class="pl-4">
              <li>Enter your M-Pesa phone number above</li>
              <li>Click "Pay Now" button</li>
              <li>You will receive a prompt on your phone</li>
              <li>Enter your M-Pesa PIN to complete payment</li>
              <li>You will receive confirmation via SMS</li>
            </ol>
          </v-expansion-panel-text>
        </v-expansion-panel>
      </v-expansion-panels>

      <!-- Pay Button -->
      <v-btn
        color="green"
        size="large"
        block
        :loading="loading"
        :disabled="!phoneNumber || loading"
        @click="initiatePayment"
      >
        <v-icon class="mr-2">mdi-lock</v-icon>
        Pay {{ formattedAmount }} Now
      </v-btn>

      <!-- Security Badge -->
      <div class="text-center mt-4">
        <v-chip size="small" variant="text">
          <v-icon class="mr-1" size="small">mdi-shield-check</v-icon>
          Secured by Safaricom M-Pesa
        </v-chip>
      </div>
    </v-card-text>

    <!-- Payment Status Dialog -->
    <v-dialog v-model="showStatusDialog" max-width="400" persistent>
      <v-card class="status-card">
        <v-card-text class="text-center pa-6">
          <v-icon
            :color="getStatusColor()"
            size="80"
            class="mb-4"
          >
            {{ getStatusIcon() }}
          </v-icon>

          <v-progress-circular
            v-if="checking"
            indeterminate
            color="green"
            size="80"
            width="6"
            class="mb-4"
          ></v-progress-circular>

          <h3 class="text-h6 mb-2">
            {{ paymentStatus === 'pending' ? 'Processing Payment...' : 'Payment Status' }}
          </h3>

          <p class="text-body-1 mb-4">
            {{ getStatusMessage() }}
          </p>

          <v-alert
            v-if="paymentStatus === 'pending'"
            type="warning"
            variant="tonal"
            class="mb-4"
          >
            <strong>Check your phone!</strong><br>
            Enter your M-Pesa PIN to complete this transaction
          </v-alert>

          <v-btn
            v-if="paymentStatus !== 'success'"
            variant="text"
            @click="cancelPayment"
          >
            Cancel
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-card>
</template>

<style scoped>
.mpesa-card {
  background: #fffbe6;
  border-radius: 16px;
  border: 2px solid #00a859;
}

.mpesa-title {
  color: #00a859;
  font-size: 1.5rem;
  font-weight: 700;
}

.status-card {
  background: #fffbe6;
  border-radius: 16px;
}

:deep(.v-field) {
  border-radius: 12px;
}

ol {
  line-height: 1.8;
}

ol li {
  margin-bottom: 0.5rem;
}
</style>
