// API Base URL - Update this to your PHP backend URL
const API_BASE_URL = 'http://localhost:8000/backend/api';

// Helper function to make API calls
async function apiCall(endpoint, method = 'GET', data = null) {
  const options = {
    method,
    headers: {
      'Content-Type': 'application/json',
    },
  };

  if (data && (method === 'POST' || method === 'PUT')) {
    options.body = JSON.stringify(data);
  }

  try {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, options);
    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.error || result.message || 'API request failed');
    }

    return result;
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
}

// Reservations API
export const reservationsAPI = {
  create: (data) => apiCall('/reservations/create.php', 'POST', data),
  getAll: () => apiCall('/reservations/get.php'),
  getById: (id) => apiCall(`/reservations/get.php?id=${id}`),
  checkDate: (date) => apiCall(`/reservations/check-date.php?date=${date}`),
};

// Menu API
export const menuAPI = {
  getAll: (category = null) => {
    const url = category ? `/menu/get.php?category=${category}` : '/menu/get.php';
    return apiCall(url);
  },
  getById: (id) => apiCall(`/menu/get.php?id=${id}`),
  create: (data) => apiCall('/menu/create.php', 'POST', data),
};

// Orders API
export const ordersAPI = {
  create: (data) => apiCall('/orders/create.php', 'POST', data),
  getAll: () => apiCall('/orders/get.php'),
  getById: (id) => apiCall(`/orders/get.php?id=${id}`),
  getByOrderNumber: (orderNumber) => apiCall(`/orders/get.php?order_number=${orderNumber}`),
};

// Events API
export const eventsAPI = {
  getAll: (type = null, upcoming = false) => {
    let url = '/events/get.php?';
    if (type) url += `type=${type}&`;
    if (upcoming) url += 'upcoming=true';
    return apiCall(url);
  },
  getById: (id) => apiCall(`/events/get.php?id=${id}`),
};

// Authentication API
export const authAPI = {
  signup: (data) => apiCall('/auth/signup.php', 'POST', data),
  login: (data) => apiCall('/auth/login.php', 'POST', data),
  getProfile: (userId) => apiCall(`/auth/profile.php?user_id=${userId}`),
};

// Contact API
export const contactAPI = {
  sendMessage: (data) => apiCall('/contact/create.php', 'POST', data),
};

// Storage helpers for authentication
export const authStorage = {
  setUser: (user) => localStorage.setItem('user', JSON.stringify(user)),
  getUser: () => {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  },
  setToken: (token) => localStorage.setItem('token', token),
  getToken: () => localStorage.getItem('token'),
  clear: () => {
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    localStorage.removeItem('isLoggedIn');
  },
  setLoggedIn: (status) => localStorage.setItem('isLoggedIn', status),
  isLoggedIn: () => localStorage.getItem('isLoggedIn') === 'true',
};
