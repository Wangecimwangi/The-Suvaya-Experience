import { createApp, shallowReactive } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'
import * as labsComponent from 'vuetify/labs/components'

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'


const vuetify = createVuetify({
  components,
  directives,
   icons: {
    defaultSet: 'mdi', // This is already the default value - only for display purposes
  },
})

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(vuetify)

app.mount('#app')


