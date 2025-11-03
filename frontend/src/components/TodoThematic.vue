<template>
  <v-container
    class="py-8 bg-flexvpadrao-neutra1 dark:bg-flexvpadrao-vuetify-dark/50 transition-colors duration-300 min-h-screen"
  >
    <v-card
      class="mx-auto max-w-3xl w-full shadow-flexv bg-flexvpadrao-neutra1 dark:bg-flexvpadrao-vuetify-dark/50 transition-all"
    >
      <v-card-title
        class="text-h5 font-bold text-flexv-300 dark:text-flexvpadrao-base text-center"
      >
        To-Do Temático
      </v-card-title>

      <v-card-subtitle
        class="text-center text-flexvpadrao-neutro4 dark:text-gray-300"
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

        <v-list class="mt-4">
          <v-list-item
            v-for="t in filtered"
            :key="t.id"
            :class="t.done ? 'opacity-60' : ''"
            class="transition-opacity"
          >
            <template #prepend>
              <v-checkbox v-model="t.done" density="comfortable" hide-details />
            </template>

            <v-list-item-title
              class="font-medium text-flexvpadrao-neutro4 dark:text-white"
            >
              {{ t.title }}
            </v-list-item-title>

            <v-list-item-subtitle
              :style="{ color: themeById(t.themeId)?.color }"
            >
              {{ themeById(t.themeId)?.name }}
            </v-list-item-subtitle>

            <template #append>
              <v-btn
                icon="mdi-delete"
                variant="text"
                color="error"
                class="hover:!opacity-80"
                @click="removeTodo(t.id)"
              />
            </template>
          </v-list-item>
        </v-list>
      </v-card-text>

      <v-divider />

      <v-card-actions class="justify-end">
        <small class="text-caption text-flexvpadrao-neutro4 dark:text-gray-300">
          {{ todos.length }} tarefas — {{ completedCount }} concluídas
        </small>
      </v-card-actions>
    </v-card>
  </v-container>
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
      filterTheme: "",
      newTitle: "",
      newTheme: "",
    };
  },

  created() {
    try {
      const raw = localStorage.getItem(this.STORAGE_KEY);
      this.todos = raw ? JSON.parse(raw) : [];
    } catch {
      this.todos = [];
    }

    if (this.themes.length) {
      this.newTheme = this.themes[0].id;
    }
  },

  watch: {
    todos: {
      handler(val) {
        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(val));
      },
      deep: true,
    },
  },

  computed: {
    filtered() {
      if (!this.filterTheme) return this.todos;
      return this.todos.filter((t) => t.themeId === this.filterTheme);
    },

    completedCount() {
      return this.todos.filter((t) => t.done).length;
    },
  },

  methods: {
    nextId() {
      return Date.now() + Math.floor(Math.random() * 1000);
    },

    addTodo() {
      const title = this.newTitle.trim();
      if (!title) return;

      this.todos.push({
        id: this.nextId(),
        title,
        themeId: this.newTheme,
        done: false,
      });
      this.newTitle = "";
    },

    removeTodo(id) {
      this.todos = this.todos.filter((t) => t.id !== id);
    },

    themeById(id) {
      return this.themes.find((t) => t.id === id);
    },
  },
};
</script>

<style scoped></style>
