<template>
  <div class="fixed top-4 right-4 z-50">
    <button
      @click="toggleDarkMode"
      class="flex items-center gap-2 bg-flexvpadrao-neutra1 dark:bg-flexvpadrao-vuetify-dark/50
             hover:brightness-110 transition-all rounded-full px-4 py-2 shadow-flexv"
    >
      <span class="text-sm font-semibold text-flexv-100 dark:text-flexv-500">
        {{ isDarkMode ? 'Modo Claro' : 'Modo Escuro' }}
      </span>

      <!--  Sol -->
      <svg
        v-if="!isDarkMode"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.8"
        stroke="currentColor"
        class="w-5 h-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M12 3v1.5m0 15V21m9-9h-1.5M4.5 12H3m15.364-7.364l-1.06 1.06M6.697 17.303l-1.06 1.06m12.728 0l-1.06-1.06M6.697 6.697L5.636 5.636M12 6.75a5.25 5.25 0 100 10.5 5.25 5.25 0 000-10.5z"
        />
      </svg>

      <!--  Lua -->
      <svg
        v-else
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.8"
        stroke="currentColor"
        class="w-5 h-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M21.752 15.002A9.718 9.718 0 0112 21.75a9.75 9.75 0 01-9.75-9.75A9.718 9.718 0 015.998 2.25a.75.75 0 01.9.9A7.5 7.5 0 0012 19.5a7.5 7.5 0 006.35-10.102.75.75 0 01.9-.9 9.72 9.72 0 012.502 6.504z"
        />
      </svg>
    </button>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import { useTheme } from 'vuetify'

export default defineComponent({
  name: 'DarkModeToggle',
  setup() {
    const isDarkMode = ref<boolean>(false)
    const theme = useTheme()

    const toggleDarkMode = () => {
      isDarkMode.value = !isDarkMode.value
      document.documentElement.classList.toggle('dark', isDarkMode.value)
      theme.global.name.value = isDarkMode.value ? 'dark' : 'light'
      localStorage.setItem('darkMode', JSON.stringify(isDarkMode.value))
    }

    onMounted(() => {
      const saved = localStorage.getItem('darkMode')
      if (saved) {
        isDarkMode.value = JSON.parse(saved)
        document.documentElement.classList.toggle('dark', isDarkMode.value)
      }
      theme.global.name.value = isDarkMode.value ? 'dark' : 'light'
    })

    return { isDarkMode, toggleDarkMode }
  },
})
</script>
