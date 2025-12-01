<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const name = ref('')
const email = ref('')
const phone = ref('')
const password = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const formRef = ref(null)
const loading = ref(false)
const errorMessage = ref('')
const agreeToTerms = ref(false)

const nameRules = [
  v => !!v || 'Name is required',
  v => v.length >= 3 || 'Name must be at least 3 characters'
]

const emailRules = [
  v => !!v || 'Email is required',
  v => /.+@.+\..+/.test(v) || 'Email must be valid'
]

const phoneRules = [
  v => !!v || 'Phone number is required',
  v => v.length >= 10 || 'Phone number must be at least 10 digits'
]

const passwordRules = [
  v => !!v || 'Password is required',
  v => v.length >= 6 || 'Password must be at least 6 characters'
]

const confirmPasswordRules = [
  v => !!v || 'Please confirm your password',
  v => v === password.value || 'Passwords do not match'
]

async function handleSignup() {
  const { valid } = await formRef.value.validate()

  if (!valid) return

  if (!agreeToTerms.value) {
    errorMessage.value = 'Please agree to the terms and conditions'
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    await authStore.signup({
      name: name.value,
      email: email.value,
      phone: phone.value,
      password: password.value
    })

    // Redirect to home after successful signup
    router.push('/')
  } catch (error) {
    errorMessage.value = error.message || 'Signup failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <v-container class="py-8 py-md-12">
    <v-row justify="center">
      <v-col cols="12" sm="10" md="8" lg="6">
        <v-card class="signup-card pa-6 pa-md-8" elevation="4">
          <v-card-title class="signup-title text-center mb-2">
            <v-icon color="amber-darken-2" size="48" class="mb-3">mdi-account-plus</v-icon>
            <h2>Create Account</h2>
          </v-card-title>

          <v-card-subtitle class="text-center mb-6">
            Join The Suvaya Experience
          </v-card-subtitle>

          <v-form @submit.prevent="handleSignup" ref="formRef">
            <!-- Personal Information -->
            <v-text-field
              v-model="name"
              label="Full Name"
              variant="outlined"
              color="amber-darken-2"
              prepend-inner-icon="mdi-account"
              :rules="nameRules"
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
              :rules="emailRules"
              required
              class="mb-2"
            ></v-text-field>

            <v-text-field
              v-model="phone"
              label="Phone Number"
              type="tel"
              variant="outlined"
              color="amber-darken-2"
              prepend-inner-icon="mdi-phone"
              :rules="phoneRules"
              required
              placeholder="+254712345678"
              class="mb-4"
            ></v-text-field>

            <v-divider class="my-4"></v-divider>

            <!-- Password Section -->
            <v-text-field
              v-model="password"
              label="Password"
              :type="showPassword ? 'text' : 'password'"
              variant="outlined"
              color="amber-darken-2"
              prepend-inner-icon="mdi-lock"
              :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append-inner="showPassword = !showPassword"
              :rules="passwordRules"
              required
              hint="Minimum 6 characters"
              class="mb-2"
            ></v-text-field>

            <v-text-field
              v-model="confirmPassword"
              label="Confirm Password"
              :type="showConfirmPassword ? 'text' : 'password'"
              variant="outlined"
              color="amber-darken-2"
              prepend-inner-icon="mdi-lock-check"
              :append-inner-icon="showConfirmPassword ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append-inner="showConfirmPassword = !showConfirmPassword"
              :rules="confirmPasswordRules"
              required
              class="mb-4"
            ></v-text-field>

            <!-- Terms and Conditions -->
            <v-checkbox
              v-model="agreeToTerms"
              color="amber-darken-2"
              class="mb-2"
            >
              <template v-slot:label>
                <span class="text-body-2">
                  I agree to the <a href="#" class="text-amber-darken-2">Terms and Conditions</a>
                </span>
              </template>
            </v-checkbox>

            <v-alert
              v-if="errorMessage"
              type="error"
              variant="tonal"
              class="mb-4"
            >
              {{ errorMessage }}
            </v-alert>

            <v-btn
              type="submit"
              color="amber-darken-2"
              size="large"
              block
              :loading="loading"
              class="mb-4"
            >
              <v-icon left class="mr-2">mdi-account-plus</v-icon>
              Create Account
            </v-btn>

            <v-divider class="my-4"></v-divider>

            <div class="text-center">
              <p class="text-body-2 mb-2">Already have an account?</p>
              <v-btn
                to="/login"
                variant="outlined"
                color="amber-darken-2"
                block
              >
                Sign In
              </v-btn>
            </div>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.signup-card {
  background: #fffbe6;
  border-radius: 16px;
}

.signup-title {
  color: #b28704;
}

.signup-title h2 {
  font-size: 1.8rem;
  font-weight: 700;
}

@media (min-width: 600px) {
  .signup-title h2 {
    font-size: 2rem;
  }
}
</style>