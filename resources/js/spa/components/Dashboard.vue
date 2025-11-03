<template>
  <div class="w-full h-full p-6">
    <v-card-title class="text-h5 font-weight-bold">Dashboard</v-card-title>
    <v-card-subtitle>Visão geral rápida do seu espaço.</v-card-subtitle>
    <v-divider class="my-2" />
    <v-card-text>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <v-card variant="tonal" class="p-4">
          <div class="text-caption p-2">Tarefas</div>
          <div class="text-h6 font-weight-bold p-2">Resumo</div>
        </v-card>
        <v-card variant="tonal" class="p-4">
          <div class="text-caption p-2">Temas</div>
          <div class="text-h6 font-weight-bold p-2">Ativos</div>
        </v-card>
        <v-card variant="tonal" class="p-4">
          <div class="text-caption p-2">Produtividade</div>
          <div class="text-h6 font-weight-bold p-2">Semana</div>
        </v-card>
      </div>
    </v-card-text>
  </div>
</template>

