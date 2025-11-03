<template>

      <v-card-subtitle

        class="text-center text-flexvpadrao-neutro4 mt-4 dark:text-gray-300"

      >
        Organize suas tarefas por tema — cores e filtros rápidos.
      </v-card-subtitle>



      <v-divider class="my-4" />



      <v-card-text>
        <v-form @submit.prevent="addTodo" class="flex gap-3 items-end flex-wrap">
          <v-text-field
            v-model="newTitle"
            label="Nova tarefa"
            variant="outlined"
            hide-details
            class="flex-1 min-w-[220px] dark:text-white"
          />

          <v-select
            v-model="newTheme"
            :items="themes"
            item-title="name"
            item-value="id"
            label="Tema"
            variant="outlined"
            hide-details
            class="min-w-[180px]"
          />

          <v-text-field
            v-model="newDueAt"
            type="date"
            label="Prazo"
            variant="outlined"
            hide-details
            class="min-w-[160px]"
          />

          <v-combobox
            v-model="newTags"
            :items="tagsOptions"
            label="Tags"
            multiple
            chips
            variant="outlined"
            hide-details
            class="min-w-[220px]"
          />

          <v-select
            v-model="newAssigneeId"
            :items="users"
            item-title="name"
            item-value="id"
            label="Atribuir para"
            variant="outlined"
            hide-details
            class="min-w-[220px]"
            v-if="currentRole === 'admin'"
          />

          <v-select
            v-if="currentRole === 'admin'"
            v-model="newOwnerId"
            :items="users"
            item-title="name"
            item-value="id"
            label="Criador (owner)"
            variant="outlined"
            hide-details
            class="min-w-[220px]"
          />

          <v-switch v-model="newIsPublic" label="Público" hide-details density="compact" />

          <v-btn
            type="submit"
            block
            variant="text"
            size="large"
            class="border-none !text-flexv-300 font-bold bg-transparent hover:!bg-transparent"
          >
            Adicionar
          </v-btn>
        </v-form>


        <div class="mt-5">

          <v-chip-group v-model="filterTheme" column>

            <v-chip :value="''" variant="elevated" class="mr-2 mb-2">Todos</v-chip>

            <v-chip

              v-for="t in themes"

              :key="t.id"

              :value="t.id"

              :style="{ '--v-theme-primary': t.color }"

              color="primary"

              variant="tonal"

              class="mr-2 mb-2"

            >

              <span

                class="w-2 h-2 rounded-full inline-block mr-2"

                :style="{ background: t.color }"

              ></span>

              {{ t.name }}

            </v-chip>

          </v-chip-group>

        </div>



        <!-- Lista ro-do -->
        <v-list class="mt-4 rounded">
          <v-list-item
            v-for="t in sorted(filtered)"
            :key="t.id"
            :class="t.done ? 'opacity-60' : ''"
            class="transition-opacity border rounded my-2 mx-2"
          >
            <template #prepend>
              <v-checkbox v-model="t.done" density="comfortable" hide-details @change="toggleDone(t)" />
            </template>


            <v-list-item-title

              class="font-medium text-flexvpadrao-neutro4 dark:text-white"

            >

              {{ t.title }}

            </v-list-item-title>



            <v-list-item-subtitle :style="{ color: (themeById(t.themeId) && themeById(t.themeId).color) }">
              {{ themeById(t.themeId) && themeById(t.themeId).name }}
            </v-list-item-subtitle>

            <div class="text-xs mt-1" :class="(dueMeta(t) && dueMeta(t).overdue) ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-300'" v-if="dueMeta(t)">
              {{ dueMeta(t) && dueMeta(t).text }}
            </div>

            <div class="flex items-center gap-2 mt-1">
              <v-chip
                v-for="tag in t.tags || []"
                :key="tag"
                size="small"
                variant="tonal"
                class="mr-1"
              >{{ tag }}</v-chip>
              <v-chip v-if="t.assignee && t.assignee.name" size="small" color="primary" variant="outlined">
                @ {{ t.assignee.name }}
              </v-chip>
            </div>


            <template #append>
              <v-btn
                icon="mdi-delete"
                variant="text"
                color="error"
                class="hover:!opacity-80"
                @click="removeTodo(t.id)"
              />
              <v-btn icon="mdi-pencil" variant="text" @click="openEdit(t)" />
              <v-btn icon="mdi-history" variant="text" @click="toggleHistory(t)" />
            </template>
          </v-list-item>
        </v-list>

        <div v-if="activeHistory.todoId" class="mt-4 p-3 rounded bg-gray-50 dark:bg-gray-800">
          <div class="flex items-center justify-between">
            <strong class="text-sm">Histórico</strong>
            <v-btn size="small" variant="text" @click="activeHistory = { todoId: null, items: [], comments: [] }">Fechar</v-btn>
          </div>
          <ul class="mt-2 text-sm space-y-1">
            <li v-for="h in activeHistory.items" :key="h.id">
              <span class="opacity-70">{{ new Date(h.created_at).toLocaleString() }}</span>
               <b>{{ h.action }}</b>
              <span v-if="h.performer"> por {{ h.performer.name }}</span>
            </li>
          </ul>
          <v-divider class="my-3" />
          <div>
            <strong class="text-sm">ComentÃ¡rios</strong>
            <ul class="mt-2 text-sm space-y-1">
              <li v-for="c in activeHistory.comments" :key="c.id">
                <span class="opacity-70">{{ new Date(c.created_at).toLocaleString() }}</span>
                 <b v-if="c.user">{{ c.user.name }}</b>: {{ c.body }}
              </li>
            </ul>
            <div class="mt-2 flex gap-2">
              <v-text-field v-model="newComment" label="Adicionar comentÃ¡rio" hide-details class="flex-1" density="compact" />
              <v-btn size="small" @click="postComment">Enviar</v-btn>
            </div>
          </div>
        </div>
      </v-card-text>


      <v-divider />



      <v-card-actions class="justify-end">
        <div class="flex items-center gap-3">
          <small class="text-caption text-flexvpadrao-neutro4 dark:text-gray-300">
            {{ todos.length }}   tarefas | {{ completedCount }} concluídas | {{ currentRole }}
          </small>
        </div>
      </v-card-actions>

      <!-- Edit Dialog -->
      <v-dialog v-model="editDialog" max-width="640">
        <v-card>
          <v-card-title>Editar Tarefa</v-card-title>
          <v-card-text>
            <v-form class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <v-text-field v-model="editForm.title" label="Titulo" variant="outlined" class="md:col-span-2" />
              <v-textarea v-model="editForm.description" label="Descrição" variant="outlined" rows="3" class="md:col-span-2" />
              <v-select v-model="editForm.theme_id" :items="themes" item-title="name" item-value="id" label="Tema" variant="outlined" />
              <v-text-field v-model="editForm.due_at" type="date" label="Prazo" variant="outlined" />
              <v-combobox v-model="editForm.tags" :items="tagsOptions" label="Tags" multiple chips variant="outlined" class="md:col-span-2" />
              <v-select v-if="currentRole === 'admin'" v-model="editForm.assignee_id" :items="users" item-title="name" item-value="id" label="AtribuÃ­do a" variant="outlined" />
              <v-select v-if="currentRole === 'admin'" v-model="editForm.user_id" :items="users" item-title="name" item-value="id" label="Criador (owner)" variant="outlined" />
              <v-switch v-model="editForm.is_public" label="Público" hide-details density="compact" />
              <v-switch v-model="editForm.done" label="Concluído" hide-details density="compact" />
            </v-form>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn variant="text" @click="editDialog=false">Cancelar</v-btn>
            <v-btn color="primary" @click="saveEdit">Salvar</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    
</template>


<script>

export default {
  name: "TodoThematic",

  data() {
    return {
      STORAGE_KEY: "todo-thematic:v1",
      themes: [
        { id: "work", name: "Trabalho", color: "#2563eb" },
        { id: "personal", name: "Pessoal", color: "#16a34a" },
        { id: "hobby", name: "Hobby", color: "#d97706" },
        { id: "urgent", name: "Urgente", color: "#dc2626" },
      ],
      todos: [],
      users: [],
      tagsOptions: [],
      filterTheme: "",
      newTitle: "",
      newTheme: "",
      newTags: [],
      newAssigneeId: null,
      newOwnerId: null,
      newIsPublic: false,
      newDueAt: '',
      activeHistory: { todoId: null, items: [], comments: [] },
      newComment: '',
      historyTimer: null,
      editDialog: false,
      editForm: { id: null, title: '', description: '', theme_id: '', due_at: '', tags: [], assignee_id: null, user_id: null, is_public: false, done: false },
    };
  },

  beforeUnmount() {
    if (this.historyTimer) clearInterval(this.historyTimer)
  },

  created() {
    if (this.themes.length) this.newTheme = this.themes[0].id;
    this.loadInitial();
  },


  watch: {},


  computed: {
    filtered() {
      if (!this.filterTheme) return this.todos;
      return this.todos.filter((t) => t.themeId === this.filterTheme);
    },

    completedCount() {
      return this.todos.filter((t) => t.done).length;
    },

    themesWithAll() {
      return [{ id: '', name: 'Todos', color: '#9ca3af' }, ...this.themes]
    },

    currentUser() {
      try { return JSON.parse(localStorage.getItem('authUser') || 'null') } catch { return null }
    },
    currentRole() {
      return (this.currentUser && this.currentUser.role) ? this.currentUser.role : 'user'
    },
  },


  methods: {
    apiBase() {
      const API_BASE = import.meta.env?.VITE_API_URL || 'http://127.0.0.1:8000'
      return String(API_BASE).replace(/\/$/, '')
    },
    authHeaders() {
      const token = localStorage.getItem('authToken')
      return token ? { Authorization: `Bearer ${token}` } : {}
    },
    async loadInitial() {
      try {
        const base = this.apiBase()
        const [todosRes, usersRes] = await Promise.all([
          fetch(`${base}/api/todos`, { headers: { Accept: 'application/json', ...this.authHeaders() } }),
          fetch(`${base}/api/users`, { headers: { Accept: 'application/json', ...this.authHeaders() } }),
        ])
        const todosData = await todosRes.json()
        const usersData = await usersRes.json()
        const items = Array.isArray(todosData?.data) ? todosData.data : (Array.isArray(todosData) ? todosData : [])
        this.users = Array.isArray(usersData) ? usersData : []
        this.todos = items.map(this.mapFromServer)
        // coletar opÃ§Ãµes de tags
        const tagSet = new Set()
        this.todos.forEach(t => (t.tags || []).forEach(tag => tagSet.add(tag)))
        this.tagsOptions = Array.from(tagSet)
      } catch (e) {
        console.error('Falha ao carregar dados', e)
      }
    },
    mapFromServer(s) {
      return {
        id: s.id,
        title: s.title,
        description: s.description || '',
        done: !!s.done,
        themeId: s.theme_id || '',
        tags: Array.isArray(s.tags) ? s.tags.map(t => t.name) : [],
        assignee_id: s.assignee_id || null,
        assignee: s.assignee || null,
        user_id: s.user_id || null,
        is_public: !!s.is_public,
        due_at: s.due_at || null,
      }
    },
    nextId() {
      return Date.now() + Math.floor(Math.random() * 1000);
    },

    async addTodo() {
      const title = this.newTitle.trim()
      if (!title) return
      try {
        const base = this.apiBase()
        const payload = {
          title,
          theme_id: this.newTheme,
          tags: this.newTags,
          assignee_id: this.newAssigneeId,
          is_public: !!this.newIsPublic,
          due_at: this.newDueAt ? this.newDueAt : null,
          ...(this.currentRole === 'admin' && this.newOwnerId ? { user_id: this.newOwnerId } : {}),
        }
        const res = await fetch(`${base}/api/todos`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json', ...this.authHeaders() },
          body: JSON.stringify(payload),
        })
        const data = await res.json()
        if (!res.ok) throw new Error(data?.message || 'Erro ao criar tarefa')
        const item = this.mapFromServer(data)
        this.todos.unshift(item)
        // atualizar opÃ§Ãµes de tags
        ;(item.tags || []).forEach(t => { if (!this.tagsOptions.includes(t)) this.tagsOptions.push(t) })
        this.newTitle = ''
        this.newTags = []
        this.newAssigneeId = null
        this.newDueAt = ''
        this.newOwnerId = null
        this.newIsPublic = false
      } catch (e) {
        console.error(e)
      }
    },

    async removeTodo(id) {
      try {
        const base = this.apiBase()
        const res = await fetch(`${base}/api/todos/${id}`, {
          method: 'DELETE',
          headers: { Accept: 'application/json', ...this.authHeaders() },
        })
        if (!res.ok) throw new Error('Falha ao excluir')
        this.todos = this.todos.filter((t) => t.id !== id)
        if (this.activeHistory.todoId === id) this.activeHistory = { todoId: null, items: [] }
      } catch (e) { console.error(e) }
    },

    async toggleDone(t) {
      try {
        const base = this.apiBase()
        const res = await fetch(`${base}/api/todos/${t.id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json', ...this.authHeaders() },
          body: JSON.stringify({ done: !!t.done })
        })
        if (!res.ok) {
          // revert
          t.done = !t.done
        }
      } catch (e) {
        t.done = !t.done
      }
    },

    async toggleHistory(t) {
      if (this.activeHistory.todoId === t.id) {
        this.activeHistory = { todoId: null, items: [], comments: [] }
        if (this.historyTimer) clearInterval(this.historyTimer)
        return
      }
      try {
        const base = this.apiBase()
        const res = await fetch(`${base}/api/todos/${t.id}/history`, {
          headers: { Accept: 'application/json', ...this.authHeaders() },
        })
        const data = await res.json()
        // carregar comentÃ¡rios em paralelo
        const cres = await fetch(`${base}/api/todos/${t.id}/comments`, {
          headers: { Accept: 'application/json', ...this.authHeaders() },
        })
        const comments = await cres.json()
        this.activeHistory = { todoId: t.id, items: Array.isArray(data) ? data : [], comments: Array.isArray(comments) ? comments : [] }
        // polling simples para novos comentÃ¡rios
        if (this.historyTimer) clearInterval(this.historyTimer)
        this.historyTimer = setInterval(async () => {
          try {
            const c2 = await fetch(`${base}/api/todos/${t.id}/comments`, { headers: { Accept: 'application/json', ...this.authHeaders() } }).then(r => r.json())
            if (Array.isArray(c2)) this.activeHistory.comments = c2
          } catch {}
        }, 5000)
      } catch (e) {
        console.error(e)
      }
    },

    async postComment() {
      if (!this.newComment.trim() || !this.activeHistory.todoId) return
      try {
        const base = this.apiBase()
        const res = await fetch(`${base}/api/todos/${this.activeHistory.todoId}/comments`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json', ...this.authHeaders() },
          body: JSON.stringify({ body: this.newComment.trim() })
        })
        const data = await res.json()
        if (!res.ok) throw new Error(data?.message || 'Erro ao comentar')
        if (!Array.isArray(this.activeHistory.comments)) this.activeHistory.comments = []
        this.activeHistory.comments.unshift(data)
        this.newComment = ''
      } catch (e) { console.error(e) }
    },

    themeById(id) {
      return this.themes.find((t) => t.id === id);
    },

    filteredByTheme(themeId) {
      if (!themeId) return this.todos
      return this.todos.filter(t => t.themeId === themeId)
    },

    dueMeta(t) {
      if (!t?.due_at) return null
      const now = new Date()
      const due = new Date(t.due_at)
      const diffMs = due.getTime() - now.getTime()
      const overdue = diffMs < 0
      const abs = Math.abs(diffMs)
      const days = Math.floor(abs / (24*60*60*1000))
      const hours = Math.floor((abs % (24*60*60*1000)) / (60*60*1000))
      const mins = Math.floor((abs % (60*60*1000)) / (60*1000))
      let part = ''
      if (days > 0) part = `${days}d ${hours}h`
      else if (hours > 0) part = `${hours}h ${mins}m`
      else part = `${mins}m`
      const text = overdue ? `${part} atrasado` : `Faltam ${part}`
      return { text, overdue }
    },

    sorted(arr) {
      const copy = Array.isArray(arr) ? [...arr] : []
      copy.sort((a, b) => {
        if (a.done !== b.done) return a.done ? 1 : -1
        const da = a.due_at ? new Date(a.due_at).getTime() : Infinity
        const db = b.due_at ? new Date(b.due_at).getTime() : Infinity
        return da - db
      })
      return copy
    },

    openEdit(t) {
      this.editForm = {
        id: t.id,
        title: t.title,
        description: t.description || '',
        theme_id: t.themeId || '',
        due_at: t.due_at ? String(t.due_at).substring(0,10) : '',
        tags: [...(t.tags || [])],
        assignee_id: t.assignee_id || null,
        user_id: this.currentRole === 'admin' ? (t.user_id || this.currentUser?.id || null) : null,
        is_public: !!t.is_public,
        done: !!t.done,
      }
      this.editDialog = true
    },

    async saveEdit() {
      const id = this.editForm.id
      if (!id) return
      try {
        const base = this.apiBase()
        const payload = { ...this.editForm }
        // ajustar campos do modelo do front
        payload.theme_id = this.editForm.theme_id
        payload.tags = this.editForm.tags
        const res = await fetch(`${base}/api/todos/${id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json', ...this.authHeaders() },
          body: JSON.stringify(payload)
        })
        const data = await res.json()
        if (!res.ok) throw new Error(data?.message || 'Erro ao salvar')
        const updated = this.mapFromServer(data)
        const idx = this.todos.findIndex(x => x.id === id)
        if (idx >= 0) this.todos[idx] = { ...this.todos[idx], ...updated }
        this.editDialog = false
      } catch (e) { console.error(e) }
    },
  },
};
</script>


<style scoped></style>






