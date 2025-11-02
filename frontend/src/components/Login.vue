<template>
  <div class="login-box">
    <h2>Entrar</h2>
    <form @submit.prevent="login">
      <label>
        Email
        <input v-model="email" type="email" required />
      </label>

      <label>
        Senha
        <input v-model="password" type="password" required />
      </label>

      <div class="actions">
        <button type="submit">Entrar</button>
      </div>
    </form>

    <p class="hint">(login fake para demo — qualquer email/senha funciona)</p>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
    }
  },
  methods: {
    login() {
      const user = {
        name: this.email.split('@')[0] || 'Usuário',
        email: this.email,
        avatar: '',
      }
      localStorage.setItem('authUser', JSON.stringify(user))
      // notify app
      window.dispatchEvent(new CustomEvent('auth-changed'))
    }
  }
}
</script>

<style scoped>

</style>
