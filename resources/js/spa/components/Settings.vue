<script setup lang="ts">
import { ref, onMounted } from 'vue'

const name = ref('')
const email = ref('')
const avatar = ref<string | null>(null)
const file = ref<File | null>(null)
const isAdmin = ref(false)

onMounted(() => {
  const current = JSON.parse(localStorage.getItem('authUser') || '{}')
  name.value = current?.name || ''
  email.value = current?.email || ''
  avatar.value = current?.avatar || null
  isAdmin.value = (current?.role || 'user') === 'admin'
})

const onFileChange = (files: File[] | undefined) => {
  const f = files?.[0]
  if (!f) return
  file.value = f
  const reader = new FileReader()
  reader.onload = () => {
    avatar.value = String(reader.result || '')
  }
  reader.readAsDataURL(f)
}

const removeAvatar = () => {
  avatar.value = null
}

const apiBase = () => String((import.meta as any).env?.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, '')
const authHeaders = () => {
  const token = localStorage.getItem('authToken')
  return token ? { Authorization: `Bearer ${token}` } : {}
}

const save = async () => {
  const current = JSON.parse(localStorage.getItem('authUser') || '{}')
  try {
    const base = apiBase()
    await fetch(`${base}/api/profile`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json', ...authHeaders() },
      body: JSON.stringify({ name: name.value, email: email.value })
    })
  } catch {}

  const updated = {
    ...current,
    name: name.value || current.name,
    email: email.value || current.email,
    avatar: avatar.value || null,
  }
  localStorage.setItem('authUser', JSON.stringify(updated))
  window.dispatchEvent(new CustomEvent('auth-changed'))
}

const toggleAdmin = async () => {
  const current = JSON.parse(localStorage.getItem('authUser') || '{}')
  if (!current?.id) return
  const role = isAdmin.value ? 'admin' : 'user'
  try {
    const base = apiBase()
    const res = await fetch(`${base}/api/users/${current.id}/role`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json', ...authHeaders() },
      body: JSON.stringify({ role })
    })
    if (res.ok) {
      const data = await res.json()
      const updated = { ...current, role: data.role }
      localStorage.setItem('authUser', JSON.stringify(updated))
      window.dispatchEvent(new CustomEvent('auth-changed'))
    }
  } catch {}
}
</script>

<template>
  <div >
      <v-card-title class="text-h5 font-weight-bold">Configurações</v-card-title>
      <v-card-subtitle>PreferÃªncias da conta e do app.</v-card-subtitle>
      <v-divider class="my-2" />
      <v-card-text>
        <v-form class="grid grid-cols-1 md:grid-cols-2 gap-6" @submit.prevent="save">
          <div class="flex items-center gap-4 md:col-span-2">
            <v-avatar size="64">
              <img v-if="avatar" :src="avatar" alt="avatar" />
              <div v-else class="w-full h-full flex items-center justify-center bg-flexv-300 text-white font-semibold rounded-full">?
              </div>
            </v-avatar>
            <div class="flex items-center gap-2 flex-wrap">
              <v-file-input
                label="Trocar foto"
                accept="image/*"
                prepend-icon="mdi-camera"
                show-size
                hide-details
                @update:model-value="onFileChange"
              />
              <v-btn variant="text" color="error" @click="removeAvatar">Remover</v-btn>
            </div>
          </div>

          <v-text-field v-model="name" label="Nome" variant="outlined" />
          <v-text-field v-model="email" label="Email" variant="outlined" />

          <div class="md:col-span-2">
            <v-btn color="primary" type="submit">Salvar</v-btn>
          </div>
          <v-divider class="md:col-span-2 my-2" />
          <div v-if="isAdmin" class="md:col-span-2">
            <v-switch v-model="isAdmin" @change="toggleAdmin" label="Sou admin" hide-details />
            <small class="opacity-70">Apenas administradores veem esta opÃ§Ã£o.</small>
          </div>
        </v-form>
      </v-card-text>
  </div>
</template>

