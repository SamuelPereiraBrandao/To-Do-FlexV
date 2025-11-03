<template>
  <div class="min-h-screen w-full flex bg-flexvpadrao-neutra1 dark:bg-flexvpadrao-vuetify-dark/50 transition-colors">
    <Sidebar :user="user" @navigate="onNavigate" />
    <!-- Reserve rail width (≈56px). On large screens, reserve full drawer width (≈260px). -->
    <main class="w-full flex-1 min-h-screen overflow-auto pl-14 md:pl-16 lg:pl-[260px]">
      <component :is="pageComponent" />
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed, defineAsyncComponent } from 'vue'
import Sidebar from './Sidebar.vue'

const Dashboard = defineAsyncComponent(() => import('./Dashboard.vue'))
const Profile = defineAsyncComponent(() => import('./Profile.vue'))
const TodoThematic = defineAsyncComponent(() => import('./TodoThematic.vue'))
const Settings = defineAsyncComponent(() => import('./Settings.vue'))

const user = ref(JSON.parse(localStorage.getItem('authUser') || '{}'))
const currentPage = ref<'dashboard' | 'profile' | 'todo' | 'settings'>('dashboard')

const refresh = () => (user.value = JSON.parse(localStorage.getItem('authUser') || '{}'))

onMounted(() => {
  window.addEventListener('auth-changed', refresh)
  window.addEventListener('storage', refresh)
})
onBeforeUnmount(() => {
  window.removeEventListener('auth-changed', refresh)
  window.removeEventListener('storage', refresh)
})

const onNavigate = (page: string) => {
  if (page === 'dashboard' || page === 'profile' || page === 'todo' || page === 'settings') {
    currentPage.value = page
  }
}

const pageComponent = computed(() => ({
  dashboard: Dashboard,
  profile: Profile,
  todo: TodoThematic,
  settings: Settings,
}[currentPage.value]))
</script>
