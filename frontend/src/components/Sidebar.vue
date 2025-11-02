<template>
  <aside class="sidebar">
    <div class="user">
      <div class="avatar-wrap">
        <img v-if="user.avatar" :src="user.avatar" alt="avatar" class="avatar" />
        <div v-else class="avatar-placeholder">{{ initials }}</div>
        <input ref="file" class="file" type="file" accept="image/*" @change="onFileChange" />
      </div>
      <div class="info">
        <div class="name">{{ user.name }}</div>
        <div class="email">{{ user.email }}</div>
      </div>
    </div>

    <nav class="nav">
      <button @click="$emit('navigate','dashboard')">Dashboard</button>
      <button @click="$emit('navigate','todo')">To‑Do Temático</button>
      <button @click="$emit('navigate','settings')">Configurações</button>
    </nav>

    <div class="bottom">
      <button class="logout" @click="logout">Sair</button>
    </div>
  </aside>
</template>

<script>
export default {
  name: 'Sidebar',
  props: {
    user: {
      type: Object,
      required: true,
    }
  },
  computed: {
    initials() {
      const n = (this.user && this.user.name) || ''
      return n.split(' ').map(s => s[0]).slice(0,2).join('').toUpperCase()
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('authUser')
      window.dispatchEvent(new CustomEvent('auth-changed'))
    },
    onFileChange(e) {
      const f = e.target.files && e.target.files[0]
      if (!f) return
      const reader = new FileReader()
      reader.onload = () => {
        const data = reader.result
        const current = JSON.parse(localStorage.getItem('authUser') || '{}')
        current.avatar = data
        localStorage.setItem('authUser', JSON.stringify(current))
        window.dispatchEvent(new CustomEvent('auth-changed'))
      }
      reader.readAsDataURL(f)
    }
  }
}
</script>

<style scoped>

</style>
