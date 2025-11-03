<template>
  <v-btn
    @click="toggle"
    elevation="6"
    class="flex items-center gap-2 rounded-full px-4 py-2 !bg-flexv-300 !text-white dark:!bg-flexvpadrao-primaria4 hover:brightness-110"
  >
    <v-icon :icon="isDark ? 'mdi-weather-night' : 'mdi-weather-sunny'" size="18" />
    <span class="text-sm font-medium !text-white">
      {{ isDark ? 'Modo Claro' : 'Modo Escuro' }}
    </span>
  </v-btn>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import { useTheme } from 'vuetify'

const theme = useTheme()
const isDark = ref<boolean>(document.documentElement.classList.contains('dark'))

const apply = (value: boolean) => {
  document.documentElement.classList.toggle('dark', value)
  theme.global.name.value = value ? 'dark' : 'light'
}

const toggle = () => {
  isDark.value = !isDark.value
  apply(isDark.value)
  localStorage.setItem('darkMode', JSON.stringify(isDark.value))
}

onMounted(() => {
  // tema inicial já aplicado em index.html; apenas sincroniza Vuetify e estado local
  const current = document.documentElement.classList.contains('dark')
  isDark.value = current
  apply(current)
})
</script>
