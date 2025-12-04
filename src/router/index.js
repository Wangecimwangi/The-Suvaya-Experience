import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/Components/Home.vue'
import ContactUs from '@/Components/ContactUs.vue'
import Login from '@/Components/Login.vue'
import Menu from '@/Components/Menu.vue'
import Orders from '@/Components/Orders.vue'
import Reservation from '@/Components/Reservation/Reservation.vue'
import SignUp from '@/Components/SignUp.vue'
import UserProfile from '@/Components/UserProfile.vue'
import Admin from '@/Components/Admin.vue'
import AboutUs from '@/Components/AboutUs.vue'
import Events from '@/Components/Events.vue'
import ReservationSuccess from '@/Components/ReservationSuccess.vue'
import MyReservations from '@/Components/MyReservations.vue'
import Shop from '@/Components/Shop.vue'
import Cart from '@/Components/Cart.vue'
import Checkout from '@/Components/Checkout.vue'
import OrderSuccess from '@/Components/OrderSuccess.vue'
import MyOrders from '@/Components/MyOrders.vue'
import PackageRecommendation from '@/Components/PackageRecommendation.vue'
import RecommendationResults from '@/Components/RecommendationResults.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      name: 'home',
      path: '/',
      component: Home
    },
    {
      name: 'contactus',
      path: '/contactus',
      component: ContactUs
    },
    {
      name: 'login',
      path: '/login',
      component: Login
    },
    {name: 'menu',
      path: '/menu',
      component: Menu
    },
    {
      name: 'events',
      path: '/events',
      component: Events
    },
    {
      name: 'aboutus',
      path: '/aboutus',
      component: AboutUs
    },
    {
      name: 'orders',
      path: '/orders',
      component: Orders
    },
    { name: 'reservation',
      path: '/reservation',
      component: Reservation
    },
    { name: 'Signup',
      path: '/signup',
      component: SignUp
      
    },
    {name: 'userprofile',
      path: '/userprofile',
      component: UserProfile
    },
    {name: 'Admin',
      path: '/Admin',
      component: Admin
    },
    {
      name: 'reservation-success',
      path: '/reservation-success',
      component: ReservationSuccess
    },
    {
      name: 'my-reservations',
      path: '/my-reservations',
      component: MyReservations
    },
    {
      name: 'shop',
      path: '/shop',
      component: Shop
    },
    {
      name: 'cart',
      path: '/cart',
      component: Cart
    },
    {
      name: 'checkout',
      path: '/checkout',
      component: Checkout
    },
    {
      name: 'order-success',
      path: '/order-success',
      component: OrderSuccess
    },
    {
      name: 'my-orders',
      path: '/my-orders',
      component: MyOrders
    },
    {
      name: 'package-recommendation',
      path: '/package-recommendation',
      component: PackageRecommendation
    },
    {
      name: 'recommendation-results',
      path: '/recommendation-results',
      component: RecommendationResults
    },
  ],
})

export default router
