<script setup lang="ts">
import { ref, computed, watch } from 'vue'

type Theme = {
  id: string
  name: string
  color: string
}

type Todo = {
  id: number
  title: string
  themeId: string
  done: boolean
}

const STORAGE_KEY = 'todo-thematic:v1'

const themes = ref<Theme[]>([
  { id: 'work', name: 'Work', color: '#2563eb' },
  { id: 'personal', name: 'Personal', color: '#16a34a' },
  { id: 'hobby', name: 'Hobby', color: '#d97706' },
  { id: 'urgent', name: 'Urgent', color: '#dc2626' },
])

let initialTodos: Todo[] = []
try {
  const raw = localStorage.getItem(STORAGE_KEY)
  initialTodos = raw ? JSON.parse(raw) as Todo[] : []
} catch (e) {
  initialTodos = []
}

const todos = ref<Todo[]>(initialTodos)

watch(todos, (v) => {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(v))
}, { deep: true })

const filterTheme = ref<string | null>(null)

const newTitle = ref('')
const newTheme = ref<string>(themes.value.length ? themes.value[0]!.id : '')

const nextId = () => Date.now() + Math.floor(Math.random() * 1000)

function addTodo() {
  const title = newTitle.value.trim()
  if (!title) return
  todos.value.push({ id: nextId(), title, themeId: newTheme.value, done: false })
  newTitle.value = ''
}

function toggleDone(t: Todo) {
  t.done = !t.done
}

function removeTodo(id: number) {
  todos.value = todos.value.filter(t => t.id !== id)
}

function setFilter(themeId: string | null) {
  filterTheme.value = themeId
}

const filtered = computed(() => {
  if (!filterTheme.value) return todos.value
  return todos.value.filter(t => t.themeId === filterTheme.value)
})

function themeById(id: string) {
  return themes.value.find(t => t.id === id)
}
</script>

<template>
  <section class="todo-thematic">
    <header class="header">
      <h1>To‑Do Temático</h1>
      <p class="subtitle">Organize suas tarefas por tema — cores e filtros rápidos.</p>
    </header>

    <form @submit.prevent="addTodo" class="new-form">
      <input v-model="newTitle" placeholder="Nova tarefa" aria-label="Nova tarefa" />
      <select v-model="newTheme" aria-label="Tema">
        <option v-for="t in themes" :key="t.id" :value="t.id">{{ t.name }}</option>
      </select>
      <button type="submit">Adicionar</button>
    </form>

    <div class="filters">
      <button :class="{ active: !filterTheme }" @click="setFilter(null)">Todos</button>
      <button v-for="t in themes" :key="t.id" :style="{ borderColor: t.color }" :class="{ active: filterTheme === t.id }" @click="setFilter(t.id)">
        <span class="dot" :style="{ background: t.color }"></span>
        {{ t.name }}
      </button>
    </div>

    <ul class="list">
      <li v-for="t in filtered" :key="t.id" :class="{ done: t.done }">
        <div class="left">
          <input type="checkbox" :checked="t.done" @change="toggleDone(t)" />
          <div class="meta">
            <div class="title">{{ t.title }}</div>
            <div class="theme" :style="{ color: themeById(t.themeId)?.color }">{{ themeById(t.themeId)?.name }}</div>
          </div>
        </div>
        <div class="actions">
          <button class="delete" @click="removeTodo(t.id)">Excluir</button>
        </div>
      </li>
    </ul>

    <footer class="footer">
      <small>{{ todos.length }} tarefas — {{ todos.filter(t => t.done).length }} concluídas</small>
    </footer>
  </section>
</template>

<style scoped>

</style>
