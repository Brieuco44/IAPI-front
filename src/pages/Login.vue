<template>
  <div class="container mx-auto my-auto p-5">
    <div class="mx-auto w-96">
      <h1 class="text-2xl font-bold  mb-4">Connexion</h1>
      <form @submit.prevent="login">
        <input v-model="email" type="email" placeholder="Email" class="input input-bordered w-full mb-4" />
        <input v-model="password" type="password" placeholder="Mot de passe" class="input input-bordered w-full " />

        <p v-if="errorMessage" class="text-error mt-4 text-sm">{{ errorMessage }}</p>
        <p class="text-sm my-4">
          Pas de compte ? <router-link to="/register" class="text-blue-500">Inscrivez-vous</router-link>
        </p>
        <button type="submit" class="btn btn-primary w-full">Se connecter</button>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

export default {
  setup() {
    const email = ref<string>("");
    const password = ref<string>("");
    const router = useRouter();
    const authStore = useAuthStore();
    const errorMessage = ref<string>("");

    const login = async () => {
      try {
        await authStore.login({ email: email.value, password: password.value });
        router.push({ name: "Dashboard" });
      } catch (error: any) {
        errorMessage.value = "Échec de la connexion. Veuillez vérifier vos identifiants.";
      }
    };

    return {
      email,
      password,
      login,
      errorMessage
    };
  }
};
</script>