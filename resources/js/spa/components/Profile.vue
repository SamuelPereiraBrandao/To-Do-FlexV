<script setup lang="ts">
import { ref, onMounted } from 'vue'

const user = ref<any>(null)
const owned = ref<any[]>([])
const assigned = ref<any[]>([])

const apiBase = () => String((import.meta as any).env?.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, '')
const authHeaders = () => {
  const token = localStorage.getItem('authToken')
  return token ? { Authorization: `Bearer ${token}` } : {}
}

onMounted(async () => {
  const me = JSON.parse(localStorage.getItem('authUser') || 'null')
  if (!me?.id) return
  try {
    const base = apiBase()
    const res = await fetch(`${base}/api/users/${me.id}`, { headers: { Accept: 'application/json', ...authHeaders() } })
    const data = await res.json()
    user.value = data.user
    owned.value = Array.isArray(data.owned) ? data.owned : []
    assigned.value = Array.isArray(data.assigned) ? data.assigned : []
  } catch {}
})
</script>

<template>
  <div class="p-4">
    <v-card class="mb-4">
      <v-card-title>Meu Perfil</v-card-title>
      <v-card-text>
        <div class="flex items-center gap-4">
          <v-avatar size="64"><div class="w-full h-full flex items-center justify-center bg-flexv-300 text-white font-semibold rounded-full">{{ (user?.name || '?').slice(0,2).toUpperCase() }}</div></v-avatar>
          <div>
            <div class="font-semibold">{{ user?.name }}</div>
            <div class="opacity-70">{{ user?.email }}</div>
            <div class="opacity-70 text-sm">Role: {{ user?.role }}</div>
          </div>
        </div>
      </v-card-text>
    </v-card>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <v-card>
        <v-card-title>Minhas tarefas</v-card-title>
        <v-list>
          <v-list-item v-for="t in owned" :key="t.id">
            <v-list-item-title>{{ t.title }}</v-list-item-title>
            <v-list-item-subtitle>{{ t.theme_id || '-' }}</v-list-item-subtitle>
          </v-list-item>
        </v-list>
      </v-card>

      <v-card>
        <v-card-title>Atribuídas a mim</v-card-title>
        <v-list>
          <v-list-item v-for="t in assigned" :key="t.id">
            <v-list-item-title>{{ t.title }}</v-list-item-title>
            <v-list-item-subtitle>{{ t.theme_id || '-' }}</v-list-item-subtitle>
          </v-list-item>
        </v-list>
      </v-card>
    </div>
  </div>
</template>

