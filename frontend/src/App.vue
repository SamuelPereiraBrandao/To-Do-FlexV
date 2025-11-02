<script lang="ts">
import { ref, onMounted, defineAsyncComponent, defineComponent } from 'vue'

export default defineComponent({
  name: 'App',
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

    // ✅ Retorne os componentes explicitamente para o template enxergar
    return {
      authUser,
      AuthLayout,
      TodoThematic,
      Login,
    }
  },
})
</script>

<template>
  <div>
    <component v-if="authUser" :is="AuthLayout">
      <template #default>
        <TodoThematic />
      </template>
    </component>

    <component v-else :is="Login" />
  </div>
</template>
