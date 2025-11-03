<template>
  <v-app>
  <div class="min-h-screen flex bg-flexvpadrao-neutra1 dark:bg-flexvpadrao-neutro5/10 transition-colors duration-300">
    <div class="fixed top-4 right-4 z-50">
      <DarkModeToggle />
    </div>

    <component v-if="authUser" :is="AuthLayout" />

    <component v-else :is="Login" />
  </div>
  </v-app>
</template>

<script lang="ts">
import { ref, onMounted, defineAsyncComponent, defineComponent } from 'vue'

export default defineComponent({
  name: 'App',
  components: {
    DarkModeToggle: defineAsyncComponent(() => import('./components/DarkModeToggle.vue')),
  },
  setup() {
    const TodoThematic = defineAsyncComponent(() => import('./components/TodoThematic.vue'))
    const AuthLayout = defineAsyncComponent(() => import('./components/AuthLayout.vue'))
    const Login = defineAsyncComponent(() => import('./components/Login.vue'))

    const authUser = ref<Record<string, any> | null>(null)

    const loadAuth = () => {
      try {
        const raw = localStorage.getItem('authUser')
        authUser.value = raw ? JSON.parse(raw) : null
      } catch {
        authUser.value = null
      }
    }

    onMounted(() => {
      loadAuth()
      window.addEventListener('auth-changed', loadAuth)
      window.addEventListener('storage', loadAuth)
    })

    return { authUser, AuthLayout, TodoThematic, Login }
  },
})
</script>


