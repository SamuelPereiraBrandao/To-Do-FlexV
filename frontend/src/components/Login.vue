<template>
  <v-container class="d-flex justify-center items-center min-h-screen">
    <div
      class="bg-flexvpadrao-neutra1 dark:bg-flexvpadrao-vuetify-dark/50 rounded-lg p-6 w-full max-w-md shadow-flexv transition-colors duration-300"
    >
      <v-card-title class="text-center text-flexv-100 text-2xl dark:text-flexv-500 mb-6 ">
        <p class="font-semibold">Entrar</p>
      </v-card-title>

      <v-form ref="form" v-model="isValid" @submit.prevent="onSubmit" class="space-y-3">
        <!-- Email -->
        <v-text-field
          v-model="email"
          label="Email"
          type="email"
          variant="outlined"
          density="comfortable"
          :rules="emailRules"
          clearable
          class="text-flexvpadrao-neutro4 dark:text-white"
        />

        <!-- Senha -->
        <v-text-field
          v-model="password"
          label="Senha"
          type="password"
          variant="outlined"
          density="comfortable"
          :rules="passwordRules"
          clearable
          class="text-flexvpadrao-neutro4 dark:text-white"
        />

        <!-- Botão -->
        <v-btn
          type="submit"
          block
          variant="text"
          size="large"
          class=" border hover:none focus:none active:none"
        >
          <span class="text-flexv-300 font-bold">Entrar</span>
        </v-btn>
      </v-form>

      <v-card-text class="text-center text-sm text-flexvpadrao-500 mt-4">
        (login fake para demo — qualquer email/senha funciona)
      </v-card-text>
    </div>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const email = ref('')
const password = ref('')
const isValid = ref(false)
const form = ref()

const emailRules = [
  (v: string) => !!v || 'O campo e-mail é obrigatório.',
  (v: string) => v.includes('@') || "O e-mail deve conter '@'.",
  (v: string) =>
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ||
    'Formato de e-mail inválido. Ex: usuario@dominio.com',
]

const passwordRules = [
  (v: string) => !!v || 'O campo senha é obrigatório.',
  (v: string) => v.length >= 4 || 'A senha deve ter no mínimo 4 caracteres.',
]

const onSubmit = async () => {
  const result = await form.value?.validate()
  if (!result.valid) return

  const user = {
    name: email.value.split('@')[0] || 'Usuário',
    email: email.value,
    avatar: '',
  }
  localStorage.setItem('authUser', JSON.stringify(user))
  window.dispatchEvent(new CustomEvent('auth-changed'))
}
</script>
