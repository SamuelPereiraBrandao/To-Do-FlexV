<template>
  <v-navigation-drawer
    rail
    expand-on-hover
    permanent
    location="left"
    width="260"
    class="!bg-flexvpadrao-neutra2 dark:!bg-flexvpadrao-neutro5/60 !border-none"
  >
    <div class="px-1 py-4">
      <div class="flex items-center gap-3 mb-4">
        <v-avatar size="40">
          <img v-if="user.avatar" :src="user.avatar" alt="avatar" />
          <div v-else class="w-full h-full flex items-center justify-center bg-flexv-300 text-white font-semibold rounded-full">
            {{ initials }}
          </div>
        </v-avatar>
        <div class="min-w-0">
          <div class="font-semibold text-flexvpadrao-neutro5 dark:text-white truncate">{{ user.name }}</div>
          <div class="text-sm text-flexvpadrao-variacao2 dark:text-gray-300 truncate">{{ user.email }}</div>
        </div>
      </div>

      <!-- Upload de avatar movido para Settings.vue -->

      <v-divider class="mb-2" />

      <v-list density="comfortable" nav>
        <v-list-item prepend-icon="mdi-view-dashboard" title="Dashboard" @click="$emit('navigate','dashboard')" />
        <v-list-item prepend-icon="mdi-format-list-checks" title="To‑Do Temático" @click="$emit('navigate','todo')" />
        <v-list-item prepend-icon="mdi-cog" title="Configurações" @click="$emit('navigate','settings')" />
      </v-list>
    </div>

    <template #append>
      <div class="p-3">
        <v-btn block variant="text" color="error" @click="logout">Sair</v-btn>
      </div>
    </template>
  </v-navigation-drawer>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

const props = defineProps<{ user: Record<string, any> }>()

const initials = computed(() =>
  (props.user?.name || '')
    .split(' ')
    .map(s => s[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
)

const logout = () => {
  localStorage.removeItem('authUser')
  window.dispatchEvent(new CustomEvent('auth-changed'))
}

// sem upload aqui
</script>
