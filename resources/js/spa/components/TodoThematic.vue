<template>
  <v-card class="p-4">

    <v-card-subtitle class="text-center text-flexvpadrao-neutro4 mt-7 dark:text-gray-300">
      Organize suas tarefas por tema — cores e filtros rápidos.
    </v-card-subtitle>

    <v-divider class="my-4" />

    <v-card-text>
      <v-alert v-if="fetchError" type="error" variant="tonal" class="mb-4">{{ fetchError }}</v-alert>
      <v-progress-linear v-if="loadingList" indeterminate color="primary" class="mb-4"></v-progress-linear>

      <v-form @submit.prevent="addTodo" class="flex gap-3 items-end flex-wrap">
        <v-text-field v-model="newTitle" label="Nova tarefa" variant="outlined" hide-details class="flex-1 min-w-[220px] dark:text-white" />
        <v-select v-model="newTheme" :items="themes" item-title="name" item-value="id" label="Tema" variant="outlined" hide-details class="min-w-[180px]" />
        <v-text-field v-model="newDueAt" type="date" label="Prazo" variant="outlined" hide-details class="min-w-[160px]" />
        <v-combobox v-model="newTags" :items="tagsOptions" label="Tags" multiple chips variant="outlined" hide-details class="min-w-[220px]" :menu-props="{ maxHeight: 300 }" />
        <v-select v-if="currentRole === 'admin'" v-model="newAssigneeId" :items="users" item-title="name" item-value="id" label="Atribuir para" variant="outlined" hide-details class="min-w-[220px]" />
        <v-select v-if="currentRole === 'admin'" v-model="newOwnerId" :items="users" item-title="name" item-value="id" label="Criador (owner)" variant="outlined" hide-details class="min-w-[220px]" />
        <v-switch v-model="newIsPublic" label="Público" hide-details density="compact" />
        <v-btn :loading="loadingCreate" type="submit" block variant="text" size="large" class="border !text-flexv-300 font-bold bg-transparent hover:!bg-transparent">Adicionar</v-btn>
      </v-form>

      <div class="mt-5">
        <v-chip-group v-model="filterTheme" column>
          <v-chip :value="''" variant="elevated" class="mr-2 mb-2">Todos</v-chip>
          <v-chip v-for="t in themes" :key="t.id" :value="t.id" :style="{ '--v-theme-primary': t.color }" color="primary" variant="tonal" class="mr-2 mb-2">
            <span class="w-2 h-2 rounded-full inline-block mr-2" :style="{ background: t.color }"></span>
            {{ t.name }}
          </v-chip>
        </v-chip-group>
      </div>

      <v-list class="mt-4 rounded">
        <v-list-item v-for="t in sorted(filtered)" :key="t.id" :class="t.done ? 'opacity-60' : ''" class="transition-opacity border rounded my-2 mx-2">
          <template #prepend>
            <v-checkbox v-model="t.done" :disabled="loadingToggleId===t.id" density="comfortable" hide-details @change="toggleDone(t)" />
          </template>
          <v-list-item-title class="font-medium text-flexvpadrao-neutro4 dark:text-white">{{ t.title }}</v-list-item-title>
          <v-list-item-subtitle :style="{ color: (themeById(t.themeId) && themeById(t.themeId).color) }">{{ themeById(t.themeId) && themeById(t.themeId).name }}</v-list-item-subtitle>
          <div class="text-xs mt-1" :class="(dueMeta(t) && dueMeta(t).overdue) ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-300'" v-if="dueMeta(t)">{{ dueMeta(t) && dueMeta(t).text }}</div>
          <div class="flex items-center gap-2 mt-1">
            <v-chip v-for="tag in t.tags || []" :key="tag" size="small" variant="tonal" class="mr-1">{{ tag }}</v-chip>
            <v-chip v-if="t.assignee && t.assignee.name" size="small" color="primary" variant="outlined" @click="goToProfile(t.assignee)">@ {{ t.assignee.name }}</v-chip>
          </div>
          <template #append>
            <v-btn :loading="loadingDeleteId===t.id" icon="mdi-delete" variant="text" color="error" class="hover:!opacity-80" @click="removeTodo(t.id)" />
            <v-btn icon="mdi-pencil" variant="text" @click="openEdit(t)" />
            <v-btn icon="mdi-history" variant="text" @click="openHistory(t)" />
          </template>
        </v-list-item>
      </v-list>

      <v-dialog v-model="historyDialog" max-width="800">
        <v-card>
          <v-card-title class="flex items-center justify-between">
            <span>Detalhes da Tarefa</span>
            <v-btn icon="mdi-close" variant="text" @click="closeHistory" />
          </v-card-title>
          <v-tabs v-model="historyTab" align-tabs="center">
            <v-tab value="history">Histórico</v-tab>
            <v-tab value="comments">Comentários</v-tab>
          </v-tabs>
          <v-card-text>
            <v-progress-linear v-if="loadingHistory" indeterminate color="primary" class="mb-2"></v-progress-linear>
            <v-window v-model="historyTab">
              <v-window-item value="history">
                <ul class="mt-2 text-sm space-y-1">
                  <li v-for="h in activeHistory.items" :key="h.id">
                    <span class="opacity-70">{{ new Date(h.created_at).toLocaleString() }}</span>
                    <b class="ml-1">{{ h.action }}</b>
                    <span v-if="h.performer"> por
                      <a href="#" class="underline" @click.prevent="goToProfile(h.performer)">@{{ h.performer.name }}</a>
                    </span>
                  </li>
                </ul>
              </v-window-item>
              <v-window-item value="comments">
                <ul class="mt-2 text-sm space-y-1">
                  <li v-for="c in activeHistory.comments" :key="c.id">
                    <span class="opacity-70">{{ new Date(c.created_at).toLocaleString() }}</span>
                    <b v-if="c.user" class="ml-1">
                      <a href="#" class="underline" @click.prevent="goToProfile(c.user)">@{{ c.user.name }}</a>
                    </b>:
                    <span class="ml-1">{{ c.body }}</span>
                  </li>
                </ul>
                <div class="mt-4 flex gap-2">
                  <v-text-field v-model="newComment" label="Adicionar comentário" hide-details class="flex-1" density="comfortable" />
                  <v-btn :loading="loadingComment" class="!bg-flexv-300 !text-white" @click="postComment">Enviar</v-btn>
                </div>
              </v-window-item>
            </v-window>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="editDialog" max-width="800">
        <v-card>
          <v-card-title class="flex items-center justify-between">
            <span>Editar Tarefa</span>
            <v-btn icon="mdi-close" variant="text" @click="editDialog=false" />
          </v-card-title>
          <v-card-text>
            <v-alert v-if="editError" type="error" variant="tonal" class="mb-3">{{ editError }}</v-alert>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <v-text-field v-model="editForm.title" label="Título" variant="outlined" />
              <v-text-field v-model="editForm.due_at" type="date" label="Prazo" variant="outlined" />
              <v-select v-model="editForm.theme_id" :items="themes" item-title="name" item-value="id" label="Tema" variant="outlined" />
              <v-switch v-model="editForm.is_public" label="Público" hide-details density="comfortable" />
            </div>
            <v-textarea v-model="editForm.description" label="Descrição" variant="outlined" rows="3" class="mt-3" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
              <v-combobox v-model="editForm.tags" :items="tagsOptions" label="Tags" multiple chips variant="outlined" :menu-props="{ maxHeight: 300 }" />
              <v-select v-if="currentRole==='admin'" v-model="editForm.assignee_id" :items="users" item-title="name" item-value="id" label="Atribuir para" variant="outlined" />
              <v-select v-if="currentRole==='admin'" v-model="editForm.user_id" :items="users" item-title="name" item-value="id" label="Criador (owner)" variant="outlined" />
              <v-switch v-model="editForm.done" label="Concluída" hide-details density="comfortable" />
            </div>
            <div v-if="editCurrent && (editCurrent.created_at || editCurrent.creator)" class="mt-3 text-sm text-gray-600 dark:text-gray-300">
              <div v-if="editCurrent.created_at">Criado em: {{ new Date(editCurrent.created_at).toLocaleString() }}</div>
              <div v-if="editCurrent.creator">
                Por: <a href="#" class="underline" @click.prevent="goToProfile(editCurrent.creator)">@{{ editCurrent.creator.name }}</a>
              </div>
            </div>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn variant="text" @click="editDialog=false">Cancelar</v-btn>
            <v-btn :loading="loadingSave" class="!bg-flexv-300 !text-white" @click="saveEdit">Salvar</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-card-text>

    <v-divider />

    <v-card-actions class="justify-end">
      <div class="flex items-center gap-3">
        <small class="text-caption text-flexvpadrao-neutro4 dark:text-gray-300">{{ todos.length }} tarefas — {{ completedCount }} concluídas | {{ currentRole }}</small>
      </div>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  name: 'TodoThematic',
  data() {
    return {
      themes: [
        { id: 'work', name: 'Trabalho', color: '#2563eb' },
        { id: 'personal', name: 'Pessoal', color: '#16a34a' },
        { id: 'hobby', name: 'Hobby', color: '#d97706' },
        { id: 'urgent', name: 'Urgente', color: '#dc2626' },
      ],
      todos: [],
      filterTheme: '',

      newTitle: '',
      newTheme: 'work',
      newDueAt: '',
      newTags: [],
      newAssigneeId: null,
      newOwnerId: null,
      newIsPublic: false,
      tagsOptions: [],
      users: [],
      fetchError: '',
      createError: '',

      editDialog: false,
      editForm: { id: null, title: '', description: '', theme_id: '', due_at: '', tags: [], assignee_id: null, user_id: null, is_public: false, done: false },
      editCurrent: null,
      editError: '',

      historyDialog: false,
      historyTab: 'history',
      activeHistory: { todoId: null, items: [], comments: [] },
      historyTimer: null,
      newComment: '',

      loadingList: false,
      loadingCreate: false,
      loadingDeleteId: null,
      loadingToggleId: null,
      loadingHistory: false,
      loadingComment: false,
      loadingSave: false,

      currentUser: JSON.parse(localStorage.getItem('authUser') || '{}'),
    }
  },
  computed: {
    currentRole() { return this.currentUser && this.currentUser.role === 'admin' ? 'admin' : 'user' },
    filtered() { return this.filterTheme ? this.todos.filter(t => t.themeId === this.filterTheme) : this.todos },
    completedCount() { return this.todos.filter(t => t.done).length },
  },
  created() {
    if (this.themes.length) this.newTheme = this.themes[0].id
    this.fetchTodos()
    if (this.currentRole === 'admin') this.fetchUsers()
    window.addEventListener('auth-changed', this.refreshUser)
  },
  unmounted() {
    window.removeEventListener('auth-changed', this.refreshUser)
    if (this.historyTimer) clearInterval(this.historyTimer)
  },
  methods: {
    refreshUser() {
      this.currentUser = JSON.parse(localStorage.getItem('authUser') || '{}')
      this.fetchTodos()
      if (this.currentRole === 'admin') this.fetchUsers()
    },
    apiBase() { const base = (import.meta && import.meta.env && import.meta.env.VITE_API_URL) || 'http://127.0.0.1:8000'; return String(base).replace(/\/$/, '') },
    authHeaders() { const t = localStorage.getItem('authToken'); return t ? { Authorization: `Bearer ${t}` } : {} },
    async fetchTodos() {
      try {
        this.loadingList = true
        const base = this.apiBase()
        const res = await fetch(`${base}/api/todos`, { headers: { Accept: 'application/json', ...this.authHeaders() } })
        const raw = await res.json()
        const list = Array.isArray(raw) ? raw : (Array.isArray(raw?.data) ? raw.data : [])
        this.todos = list.map(this.mapFromServer)
        const all = new Set()
        this.todos.forEach(x => (x.tags || []).forEach(t => all.add(typeof t === 'string' ? t : (t && t.name ? t.name : ''))))
        this.tagsOptions = Array.from(all)
        this.fetchError = ''
      } catch (e) {
        console.error(e)
        this.fetchError = 'Falha ao carregar tarefas'
      } finally {
        this.loadingList = false
      }
    },
    async fetchUsers() {
      try {
        const base = this.apiBase()
        const res = await fetch(`${base}/api/users`, { headers: { Accept: 'application/json', ...this.authHeaders() } })
        const data = await res.json()
        if (Array.isArray(data)) this.users = data
      } catch (e) { console.error(e) }
    },
    mapFromServer(s) {
      const rawTags = Array.isArray(s.tags) ? s.tags : []
      const tags = rawTags.map(t => (typeof t === 'string' ? t : (t && t.name ? t.name : ''))).filter(Boolean)
      return { id: s.id, title: s.title, description: s.description || '', themeId: s.theme_id || s.themeId || '', done: !!s.done, tags, user_id: s.user_id || null, is_public: !!s.is_public, due_at: s.due_at || null, assignee_id: s.assignee_id || null, assignee: s.assignee || null, creator: s.user || s.creator || null, created_at: s.created_at || null }
    },
    themeById(id) { return this.themes.find(t => t.id === id) },
    dueMeta(t) {
      if (!t?.due_at) return null
      const now = new Date(), due = new Date(t.due_at)
      const diff = due.getTime() - now.getTime(), overdue = diff < 0, abs = Math.abs(diff)
      const d = Math.floor(abs / 86400000), h = Math.floor((abs % 86400000) / 3600000), m = Math.floor((abs % 3600000) / 60000)
      const part = d > 0 ? `${d}d ${h}h` : h > 0 ? `${h}h ${m}m` : `${m}m`
      return { text: overdue ? `${part} atrasados` : `Faltam ${part}`, overdue }
    },
    sorted(arr) {
      const copy = Array.isArray(arr) ? [...arr] : []
      copy.sort((a, b) => { if (a.done !== b.done) return a.done ? 1 : -1; const da = a.due_at ? new Date(a.due_at).getTime() : Infinity; const db = b.due_at ? new Date(b.due_at).getTime() : Infinity; return da - db })
      return copy
    },
    async addTodo() {
      const title = (this.newTitle || '').trim(); if (!title) return
      try {
        this.loadingCreate = true; this.createError = ''
        const base = this.apiBase()
        const payload = { title, theme_id: this.newTheme, tags: this.newTags, assignee_id: this.newAssigneeId, is_public: !!this.newIsPublic, due_at: this.newDueAt || null, ...(this.currentRole === 'admin' && this.newOwnerId ? { user_id: this.newOwnerId } : {}) }
        const res = await fetch(`${base}/api/todos`, { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', ...this.authHeaders() }, body: JSON.stringify(payload) })
        const data = await res.json(); if (!res.ok) throw new Error(data?.message || 'Erro ao criar tarefa')
        const item = this.mapFromServer(data); this.todos.unshift(item); (item.tags || []).forEach(t => { if (!this.tagsOptions.includes(t)) this.tagsOptions.push(t) })
        this.newTitle = ''; this.newTags = []; this.newAssigneeId = null; this.newDueAt = ''; this.newOwnerId = null; this.newIsPublic = false
      } catch (e) { console.error(e); this.createError = e?.message || 'Erro ao criar tarefa' } finally { this.loadingCreate = false }
    },
    async removeTodo(id) {
      try { this.loadingDeleteId = id; const base = this.apiBase(); const res = await fetch(`${base}/api/todos/${id}`, { method: 'DELETE', headers: { Accept: 'application/json', ...this.authHeaders() } }); if (!res.ok) throw new Error('Falha ao excluir'); this.todos = this.todos.filter(t => t.id !== id); if (this.activeHistory.todoId === id) this.closeHistory() } catch (e) { console.error(e) } finally { this.loadingDeleteId = null }
    },
    async toggleDone(t) { try { this.loadingToggleId = t.id; const base = this.apiBase(); const res = await fetch(`${base}/api/todos/${t.id}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', Accept: 'application/json', ...this.authHeaders() }, body: JSON.stringify({ done: !!t.done }) }); if (!res.ok) t.done = !t.done } catch (e) { t.done = !t.done } finally { this.loadingToggleId = null } },
    openEdit(t) { this.editForm = { id: t.id, title: t.title, description: t.description || '', theme_id: t.themeId || '', due_at: t.due_at ? String(t.due_at).substring(0,10) : '', tags: [...(t.tags || [])], assignee_id: t.assignee_id || null, user_id: this.currentRole === 'admin' ? (t.user_id || this.currentUser?.id || null) : null, is_public: !!t.is_public, done: !!t.done }; this.editCurrent = t; this.editError = ''; this.editDialog = true },
    async saveEdit() {
      const id = this.editForm.id; if (!id) return
      try {
        this.loadingSave = true; this.editError = ''
        const base = this.apiBase(); const payload = { ...this.editForm, theme_id: this.editForm.theme_id, tags: this.editForm.tags }
        if (payload.due_at === '') payload.due_at = null; if (this.currentRole !== 'admin') delete payload.user_id
        const res = await fetch(`${base}/api/todos/${id}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', Accept: 'application/json', ...this.authHeaders() }, body: JSON.stringify(payload) })
        const data = await res.json(); if (!res.ok) throw new Error(data?.message || 'Erro ao salvar')
        const updated = this.mapFromServer(data); const idx = this.todos.findIndex(x => x.id === id); if (idx >= 0) this.todos[idx] = { ...this.todos[idx], ...updated }
        this.editDialog = false
      } catch (e) { console.error(e); this.editError = e?.message || 'Erro ao salvar' } finally { this.loadingSave = false }
    },
    async openHistory(t) {
      try {
        this.loadingHistory = true
        const base = this.apiBase()
        const [h, c] = await Promise.all([
          fetch(`${base}/api/todos/${t.id}/history`, { headers: { Accept: 'application/json', ...this.authHeaders() } }).then(r => r.json()),
          fetch(`${base}/api/todos/${t.id}/comments`, { headers: { Accept: 'application/json', ...this.authHeaders() } }).then(r => r.json()),
        ])
        this.activeHistory = { todoId: t.id, items: Array.isArray(h) ? h : [], comments: Array.isArray(c) ? c : [] }
        this.historyTab = 'history'; this.historyDialog = true
        if (this.historyTimer) clearInterval(this.historyTimer)
        this.historyTimer = setInterval(async () => {
          try { const c2 = await fetch(`${base}/api/todos/${t.id}/comments`, { headers: { Accept: 'application/json', ...this.authHeaders() } }).then(r => r.json()); if (Array.isArray(c2)) this.activeHistory.comments = c2 } catch {}
        }, 5000)
      } catch (e) { console.error(e) } finally { this.loadingHistory = false }
    },
    closeHistory() { this.historyDialog = false; this.activeHistory = { todoId: null, items: [], comments: [] }; if (this.historyTimer) clearInterval(this.historyTimer); this.historyTimer = null },
    async postComment() { if (!this.newComment.trim() || !this.activeHistory.todoId) return; try { this.loadingComment = true; const base = this.apiBase(); const res = await fetch(`${base}/api/todos/${this.activeHistory.todoId}/comments`, { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', ...this.authHeaders() }, body: JSON.stringify({ body: this.newComment.trim() }) }); const data = await res.json(); if (!res.ok) throw new Error(data?.message || 'Erro ao comentar'); if (!Array.isArray(this.activeHistory.comments)) this.activeHistory.comments = []; this.activeHistory.comments.unshift(data); this.newComment = '' } catch (e) { console.error(e) } finally { this.loadingComment = false } },
    goToProfile(user) {
      if (!user) return
      const id = user.id || user.user_id || (user.data && user.data.id)
      if (!id) return
      try { window.dispatchEvent(new CustomEvent('go-profile', { detail: { id } })) } catch {}
    },
  },
}
</script>

<style scoped></style>
