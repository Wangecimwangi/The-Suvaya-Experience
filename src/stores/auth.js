import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authAPI, authStorage } from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(authStorage.getUser())
  const token = ref(authStorage.getToken())
  const loading = ref(false)
  const error = ref(null)

  const isLoggedIn = computed(() => !!user.value && !!token.value)
  const isAdmin = computed(() => user.value?.is_admin === true || user.value?.is_admin === 1)

  async function login(credentials) {
    loading.value = true
    error.value = null

    try {
      const response = await authAPI.login(credentials)
      user.value = response.data.user
      token.value = response.data.token

      authStorage.setUser(response.data.user)
      authStorage.setToken(response.data.token)
      authStorage.setLoggedIn(true)

      return response
    } catch (err) {
      error.value = err.message || 'Login failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function signup(userData) {
    loading.value = true
    error.value = null

    try {
      const response = await authAPI.signup(userData)
      user.value = response.data.user
      token.value = response.data.token

      authStorage.setUser(response.data.user)
      authStorage.setToken(response.data.token)
      authStorage.setLoggedIn(true)

      return response
    } catch (err) {
      error.value = err.message || 'Signup failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  function logout() {
    user.value = null
    token.value = null
    authStorage.clear()
  }

  async function fetchProfile() {
    if (!user.value?.id) return

    try {
      const response = await authAPI.getProfile(user.value.id)
      user.value = response.data
      authStorage.setUser(response.data)
    } catch (err) {
      console.error('Failed to fetch profile:', err)
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isLoggedIn,
    isAdmin,
    login,
    signup,
    logout,
    fetchProfile
  }
})
