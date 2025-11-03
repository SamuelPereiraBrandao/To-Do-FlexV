<template>
  <v-container class="d-flex justify-center">
    <div
      class="bg-flexvpadrao-neutra1 dark:bg-flexvpadrao-neutro5 rounded-lg py-5 px-5 w-full shadow-flexv"
    >
      <v-card-title
        class="text-center text-2xl font-semibold text-flexv-300 mb-4"
      >
        Entrar
      </v-card-title>



      <v-form @submit.prevent="login" class="space-y-4">
        <v-text-field
          v-model="email"
          label="Email"
          type="email"
          required
          variant="outlined"
          density="comfortable"
          hide-details
          class="text-flexvpadrao-neutro4 dark:text-white mb-4"
        />

        <v-text-field
          v-model="password"
          label="Senha"
          type="password"
          required
          variant="outlined"
          density="comfortable"
          hide-details
          class="text-flexvpadrao-neutro4 dark:text-white"
        />

        <v-btn
          type="submit"
          block
          class="bg-flexv-300 text-white dark:bg-flexvpadrao-primaria4 font-medium hover:brightness-110 shadow-flexv"
          size="large"
        >
          Entrar
        </v-btn>
      </v-form>

      <v-card-text class="text-center text-sm text-flexvpadrao-neutro3 mt-4">
        (login fake para demo — qualquer email/senha funciona)
      </v-card-text>
    </div>
  </v-container>
</template>

<script>
export default {
  name: "Login",
  data() {
    return {
      email: "",
      password: "",
    };
  },
  methods: {
    login() {
      const user = {
        name: this.email.split("@")[0] || "Usuário",
        email: this.email,
        avatar: "",
      };
      localStorage.setItem("authUser", JSON.stringify(user));
      window.dispatchEvent(new CustomEvent("auth-changed"));
    },
  },
};
</script>
