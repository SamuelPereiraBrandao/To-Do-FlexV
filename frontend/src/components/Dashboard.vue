<template>
  <v-container class="py-6">
    <v-card class="mx-auto max-w-3xl w-full">
      <v-card-title class="text-h5 font-weight-bold">Dashboard</v-card-title>
      <v-card-subtitle>Visão geral rápida do seu espaço.</v-card-subtitle>
      <v-divider class="my-2" />
      <v-card-text>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <v-card variant="tonal" class="p-4">
            <div class="text-caption">Tarefas</div>
            <div class="text-h6 font-weight-bold">Resumo</div>
          </v-card>
          <v-card variant="tonal" class="p-4">
            <div class="text-caption">Temas</div>
            <div class="text-h6 font-weight-bold">Ativos</div>
          </v-card>
          <v-card variant="tonal" class="p-4">
            <div class="text-caption">Produtividade</div>
            <div class="text-h6 font-weight-bold">Semana</div>
          </v-card>
        </div>
      </v-card-text>
    </v-card>
  </v-container>
</template>

