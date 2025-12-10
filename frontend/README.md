# Suvaya Frontend

Vue.js frontend application for the Suvaya Bakery Management System.

## Technology Stack

- **Vue 3** - Progressive JavaScript Framework
- **Vuetify 3** - Material Design Component Framework
- **Pinia** - State Management
- **Vue Router** - Client-side Routing
- **Vite** - Build Tool and Dev Server
- **vue-toastification** - Toast Notifications

## Project Structure

```
src/
├── Components/           # Vue Components
│   ├── Home.vue         # Homepage with carousel
│   ├── MenuList.vue     # Menu browsing page
│   ├── Checkout.vue     # Checkout and payment
│   ├── Admin.vue        # Admin dashboard
│   ├── PackageRecommendation.vue  # Package wizard
│   ├── RecommendationResults.vue  # Package results
│   └── ...
├── stores/              # Pinia Stores
│   ├── cart.js         # Shopping cart state
│   └── auth.js         # Authentication state
├── services/           # API Services
│   └── api.js          # API client
├── router/             # Vue Router
│   └── index.js        # Route definitions
└── App.vue             # Root component
```

## Setup

### Install Dependencies
```bash
npm install
```

### Run Development Server
```bash
npm run dev
```
Runs on http://localhost:5173

### Build for Production
```bash
npm run build
```
Output: `dist/` folder

### Preview Production Build
```bash
npm run preview
```

## Environment Configuration

The API base URL is configured in `src/services/api.js`:
```javascript
const API_BASE_URL = 'http://localhost:8000/api'
```

Update this to match your backend URL.

## Features

### Public Pages
- **Home** (`/`) - Hero carousel, welcome section, features
- **Menu** (`/menu`) - Browse all menu items by category
- **Shop** (`/shop`) - Shopping view with add to cart
- **Cart** (`/cart`) - Review cart and proceed to checkout
- **Checkout** (`/checkout`) - Order form and payment
- **Package Recommendation** (`/package-recommendation`) - Guided wizard
- **Reservation** (`/reservation`) - Book dates for events
- **Events** (`/events`) - View and register for events
- **About Us** (`/aboutus`) - Company information
- **Contact** (`/contactus`) - Contact form

### Protected Pages
- **My Orders** (`/my-orders`) - User order history
- **My Reservations** (`/reservations`) - User reservations
- **Profile** (`/profile`) - User profile management
- **Admin** (`/admin`) - Admin dashboard (requires admin role)

### Authentication
- **Login** (`/login`)
- **Sign Up** (`/signup`)

## State Management

### Cart Store (`stores/cart.js`)
```javascript
// Actions
addItem(item)       // Add item to cart
removeItem(itemId)  // Remove item from cart
updateQuantity(itemId, quantity)  // Update quantity
clearCart()         // Clear entire cart

// Getters
items              // Cart items
totalItems         // Total item count
totalPrice         // Total price
```

### Auth Store (`stores/auth.js`)
```javascript
// Actions
login(credentials)  // User login
signup(userData)    // User registration
logout()            // User logout

// Getters
user               // Current user
isAuthenticated    // Login status
isAdmin            // Admin status
```

## API Services

All API calls are centralized in `src/services/api.js`:

```javascript
import { menuAPI, ordersAPI, reservationsAPI, authAPI } from '@/services/api'

// Usage examples
const items = await menuAPI.getAll()
const order = await ordersAPI.create(orderData)
const user = await authAPI.login(credentials)
```

## Styling

The app uses Vuetify's theming system with custom colors:

```javascript
// Primary color: amber-darken-2 (#b28704)
// Background: #fffbe6
// Accent: #f5f5f5
```

Custom styles are scoped to components using `<style scoped>`.

## Adding New Components

1. Create component in `src/Components/`
2. Add route in `src/router/index.js`
3. Import and use in parent components

Example:
```vue
<script setup>
import NewComponent from '@/Components/NewComponent.vue'
</script>

<template>
  <NewComponent />
</template>
```

## Toast Notifications

Using vue-toastification for user feedback:

```javascript
import { useToast } from 'vue-toastification'
const toast = useToast()

// Success
toast.success('Order placed successfully!', { icon: '✅' })

// Error
toast.error('Failed to submit order', { icon: '❌' })

// Warning
toast.warning('Please fill all fields', { icon: '⚠️' })

// Info
toast.info('Processing...', { icon: 'ℹ️' })
```

## Responsive Design

All components use Vuetify's responsive breakpoints:

- `xs` - Extra small (< 600px)
- `sm` - Small (≥ 600px)
- `md` - Medium (≥ 960px)
- `lg` - Large (≥ 1280px)
- `xl` - Extra large (≥ 1920px)

Example:
```vue
<v-col cols="12" sm="6" md="4">
  <!-- Content -->
</v-col>
```

## Icons

Using Material Design Icons via Vuetify:

```vue
<v-icon>mdi-cart</v-icon>
<v-icon color="amber-darken-2">mdi-cake-variant</v-icon>
```

Browse icons at: https://pictogrammers.com/library/mdi/

## Common Development Tasks

### Add new menu category
1. Update category list in `MenuList.vue`
2. Add category option in `Admin.vue`
3. Update database schema if needed

### Add new API endpoint
1. Create endpoint in `backend/api/`
2. Add to `frontend/src/services/api.js`
3. Use in components

### Update theme colors
Edit `src/main.js`:
```javascript
const customTheme = {
  colors: {
    primary: '#b28704',
    // Add more colors
  }
}
```

## Troubleshooting

### API Connection Errors
- Check backend is running on http://localhost:8000
- Verify CORS is enabled in backend
- Check API_BASE_URL in `services/api.js`

### Build Errors
- Delete `node_modules` and run `npm install`
- Clear Vite cache: `rm -rf .vite`
- Check Node.js version (v16+)

### Styling Issues
- Ensure Vuetify styles are imported in `main.js`
- Check component styles are scoped
- Clear browser cache

## License

Proprietary - Suvaya Bakery
