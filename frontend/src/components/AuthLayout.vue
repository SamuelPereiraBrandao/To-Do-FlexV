<template>
  <div class="auth-layout">
    <Sidebar :user="user" @navigate="onNavigate" />
    <main class="content">
      <slot />
    </main>
  </div>
</template>

<script>
import Sidebar from './Sidebar.vue'

export default {
  name: 'AuthLayout',
  components: { Sidebar },
  data() {
    return {
      user: JSON.parse(localStorage.getItem('authUser') || '{}')
    }
  },
  created() {
    window.addEventListener('auth-changed', this.refresh)
    window.addEventListener('storage', this.refresh)
  },
  beforeUnmount() {
    window.removeEventListener('auth-changed', this.refresh)
    window.removeEventListener('storage', this.refresh)
  },
  methods: {
    refresh() {
      this.user = JSON.parse(localStorage.getItem('authUser') || '{}')
    },
    onNavigate(page) {
      this.$emit('navigate', page)
    }
  }
}
</script>

<style scoped>

</style>
