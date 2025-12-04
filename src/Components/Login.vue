<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const formRef = ref(null)
const loading = ref(false)
const errorMessage = ref('')

const emailRules = [
  v => !!v || 'Email is required',
  v => /.+@.+\..+/.test(v) || 'Email must be valid'
]

const passwordRules = [
  v => !!v || 'Password is required',
  v => v.length >= 6 || 'Password must be at least 6 characters'
]

async function handleLogin() {
  const { valid } = await formRef.value.validate()

  if (!valid) return

  loading.value = true
  errorMessage.value = ''

  try {
    await authStore.login({
      email: email.value,
      password: password.value
    })

    // Redirect to saved location or based on user type
    const redirect = route.query.redirect
    if (redirect) {
      router.push(redirect)
    } else if (authStore.isAdmin) {
      router.push('/admin')
    } else {
      router.push('/')
    }
  } catch (error) {
    errorMessage.value = error.message || 'Login failed. Please check your credentials.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <v-container class="py-8 py-md-12">
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="login-card pa-6 pa-md-8" elevation="4">
          <v-card-title class="login-title text-center mb-2">
            <v-icon color="amber-darken-2" size="48" class="mb-3">mdi-account-circle</v-icon>
            <h2>Welcome Back</h2>
          </v-card-title>

          <v-card-subtitle class="text-center mb-6">
            Sign in to your Suvaya account
          </v-card-subtitle>

          <v-form @submit.prevent="handleLogin" ref="formRef">
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
              v-model="password"
              :label="'Password'"
              :type="showPassword ? 'text' : 'password'"
              variant="outlined"
              color="amber-darken-2"
              prepend-inner-icon="mdi-lock"
              :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append-inner="showPassword = !showPassword"
              :rules="passwordRules"
              required
              class="mb-4"
            ></v-text-field>

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
              <v-icon left class="mr-2">mdi-login</v-icon>
              Sign In
            </v-btn>

            <v-divider class="my-4"></v-divider>

            <div class="text-center">
              <p class="text-body-2 mb-2">Don't have an account?</p>
              <v-btn
                to="/signup"
                variant="outlined"
                color="amber-darken-2"
                block
              >
                Create Account
              </v-btn>
            </div>

            <div class="text-center mt-4">
              <v-btn
                variant="text"
                size="small"
                color="amber-darken-2"
              >
                Forgot Password?
              </v-btn>
            </div>
          </v-form>

          <!-- Quick Test Credentials Info -->
          <v-alert
            type="info"
            variant="tonal"
            class="mt-6"
            density="compact"
          >
            <strong>Test Account:</strong><br>
            Email: admin@suvaya.com<br>
            Password: admin123
          </v-alert>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.login-card {
  background: #fffbe6;
  border-radius: 16px;
}

.login-title {
  color: #b28704;
}

.login-title h2 {
  font-size: 1.8rem;
  font-weight: 700;
}

@media (min-width: 600px) {
  .login-title h2 {
    font-size: 2rem;
  }
}
</style>