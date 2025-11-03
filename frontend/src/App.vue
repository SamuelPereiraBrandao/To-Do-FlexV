<script lang="ts">
import { ref, onMounted, defineAsyncComponent, defineComponent } from 'vue'

export default defineComponent({
  name: 'App',
  setup() {
    const TodoThematic = defineAsyncComponent(() => import('./components/TodoThematic.vue'))
    const AuthLayout = defineAsyncComponent(() => import('./components/AuthLayout.vue'))
    const Login = defineAsyncComponent(() => import('./components/Login.vue'))
    const DarkModeToggle = defineAsyncComponent(() => import('./components/DarkModeToggle.vue'))

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
      // Tema inicial já aplicado em index.html
    })

    return { authUser, AuthLayout, TodoThematic, Login, DarkModeToggle }
  },
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-flexvpadrao-neutra1 dark:bg-flexvpadrao-neutro5 transition-colors duration-300">
    <div class="fixed top-4 right-4 z-50">
      <DarkModeToggle />
    </div>

    <component v-if="authUser" :is="AuthLayout">
      <template #default>
        <TodoThematic />
      </template>
    </component>

    <component v-else :is="Login" />
  </div>
  
</template>

