<template>
  <div class="container mx-auto my-auto p-5">
    <div class="mx-auto w-96">
      <h1 class="text-2xl font-bold mt-4">Inscription</h1>
      <form @submit.prevent="register">
        <input v-model="name" type="text" placeholder="Nom complet" class="input input-bordered w-full mt-4" />
        <input v-model="email" type="email" placeholder="Email" class="input input-bordered w-full mt-4" />
        <input v-model="username" type="text" placeholder="Nom d'utilisateur"
          class="input input-bordered w-full mt-4" />
        <input v-model="password" type="password" placeholder="Mot de passe" class="input input-bordered w-full mt-4" />
        <input v-model="telephone" type="tel" placeholder="Téléphone" class="input input-bordered w-full mt-4" />
        <input v-model="birthdate" type="date" placeholder="Date de naissance"
          class="input input-bordered w-full mt-4" />

        <p v-if="errorMessage" class="text-error mb-4">{{ errorMessage }}</p>
        <button type="submit" class="btn btn-primary w-full mt-5">S'inscrire</button>
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
    const name = ref<string>("");
    const email = ref<string>("");
    const username = ref<string>("");
    const password = ref<string>("");
    const telephone = ref<string>("");
    const birthdate = ref<string>("");
    const router = useRouter();
    const authStore = useAuthStore();
    const errorMessage = ref<string>("");

    const register = async () => {
      try {
        await authStore.register({ fullname: name.value, email: email.value, username: username.value, password: password.value, telephone: telephone.value, birthdate: birthdate.value });
        alert("Inscription réussie ! Veuillez vous connecter.");
        router.push({ name: "Login" });
      } catch (error: any) {
        if (error.response.data.error) {
          errorMessage.value = error.response.data.error;
        } else {
          errorMessage.value = "Erreur d'inscription.";
        }
      }
    };

    return { name, username, email, password, telephone, birthdate, register, errorMessage };
  }
};
</script>