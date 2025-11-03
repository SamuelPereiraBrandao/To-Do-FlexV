import { createApp } from 'vue'
import '../../css/style.css'
import App from './App.vue'
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

// Vuetify
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const startDark = document.documentElement.classList.contains('dark')

const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: startDark ? 'dark' : 'light',
    themes: {
      light: { dark: false },
      dark: { dark: true },
    },
  },
})

createApp(App)
  .use(vuetify)
  .mount('#app')

