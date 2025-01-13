<template>
  <div id="app" class="d-flex flex-column h-screen overflow-y-hidden">
    <div class="navbar bg-base-100 sticky top-0 z-10">
      <div class="navbar-start">
        <router-link to="/">
          <font-awesome-icon icon="fa-solid fa-people-roof" size="2xl" />
        </router-link>
      </div>
      <div class="navbar-end">
        <!-- Displaying network status directly -->
        <div>Status: <b :class="clazz">{{ text }}</b></div>

        <button v-if="isLogin" class="btn btn-link btn-ghost" @click="logout">
          <font-awesome-icon icon="fa-solid fa-power-off" class="text-error" />
        </button>
      </div>
    </div>
    <router-view />
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import { useOnline } from '@vueuse/core'

export default defineComponent({
  name: "App",
  setup() {
    // Accessing network status directly from useNetwork
    const online = useOnline()

    const clazz = computed(() => online.value ? 'text-primary' : 'text-gray')
    const text = computed(() => online.value ? 'Online' : 'Offline')

    const authStore = useAuthStore();
    const router = useRouter();
    const isLogin = computed(() => authStore.isLogin);

    const logout = async () => {
      await authStore.logout();
      router.push({ name: "Login" });
    };

    return { logout, isLogin, clazz, text };
  },
});
</script>
