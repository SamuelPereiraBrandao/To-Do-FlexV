<template>
  <v-container class="d-flex justify-center items-center min-h-screen">
    <div class="bg-flexvpadrao-neutra1 dark:bg-flexvpadrao-vuetify-dark/50 rounded-lg p-6 w-full max-w-md shadow-flexv transition-colors duration-300">
      <div class="flex justify-center mb-4">
        <img src="/images/flexv-logo.svg" alt="FlexV" class="h-10" />
      </div>
      <v-card-title class="text-center text-flexv-100 text-2xl dark:text-flexv-500 mb-6">
        <p class="font-semibold">{{ isRegister ? 'Criar Conta' : 'Entrar' }}</p>
      </v-card-title>

      <v-form ref="form" v-model="isValid" @submit.prevent="onSubmit" class="space-y-3">
        <v-text-field v-if="isRegister" v-model="name" label="Nome" type="text" variant="outlined" density="comfortable" :rules="nameRules" clearable class="text-flexvpadrao-neutro4 dark:text-white" />
        <v-text-field v-model="email" label="Email" type="email" variant="outlined" density="comfortable" :rules="emailRules" clearable class="text-flexvpadrao-neutro4 dark:text-white" />
        <v-text-field v-model="password" label="Senha" type="password" variant="outlined" density="comfortable" :rules="passwordRules" clearable class="text-flexvpadrao-neutro4 dark:text-white" />

        <v-btn :loading="loading" type="submit" block size="large" class="!bg-flexv-300 !text-white dark:!bg-flexvpadrao-primaria4 font-bold hover:brightness-110 shadow-flexv">
          {{ isRegister ? 'Cadastrar' : 'Entrar' }}
        </v-btn>
      </v-form>

      <v-alert v-if="error" type="error" class="mt-4" variant="tonal">{{ error }}</v-alert>
      <div class="mt-3 text-center">
        <button class="underline text-flexvpadrao-neutro4 dark:text-gray-300 hover:opacity-80" @click="toggleMode">
          {{ isRegister ? 'Já tenho conta — fazer login' : 'Não tem conta? Criar agora' }}
        </button>
      </div>
    </div>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'

const isRegister = ref(false)
const name = ref('')
const email = ref('')
const password = ref('')
const isValid = ref(false)
const form = ref()
const loading = ref(false)
const error = ref('')

const nameRules = [
  (v: string) => !!v || 'O campo nome é obrigatório.',
]
const emailRules = [
  (v: string) => !!v || 'O campo e-mail é obrigatório.',
  (v: string) => v.includes('@') || "O e-mail deve conter '@'.",
  (v: string) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'Formato de e-mail inválido. Ex: usuario@dominio.com',
]
const passwordRules = [
  (v: string) => !!v || 'O campo senha é obrigatório.',
  (v: string) => v.length >= 4 || 'A senha deve ter no mínimo 4 caracteres.',
]

const onSubmit = async () => {
  const result = await form.value?.validate()
  if (!result.valid) return
  loading.value = true
  error.value = ''
  try {
    const API_BASE = (import.meta as any).env?.VITE_API_URL || 'http://127.0.0.1:8000'
    const base = String(API_BASE).replace(/\/$/, '')
    const url = isRegister.value ? `${base}/api/register` : `${base}/api/login`
    const payload: Record<string, any> = { email: email.value, password: password.value, device_name: 'web' }
    if (isRegister.value) payload.name = name.value
    const { data } = await axios.post(url, payload, {
      headers: { Accept: 'application/json' },
      withCredentials: false,
      validateStatus: () => true,
    })

    if (!data || !data.token) {
      throw new Error((data && (data.message || data.error)) || 'Falha no login')
    }
    const token = data.token
    localStorage.setItem('authToken', token)
    if (data.user) localStorage.setItem('authUser', JSON.stringify(data.user))
    window.dispatchEvent(new CustomEvent('auth-changed'))
  } catch (e: any) {
    error.value = e?.message || 'Falha no login'
  } finally {
    loading.value = false
  }
}

function toggleMode() {
  isRegister.value = !isRegister.value
  error.value = ''
}
</script>
