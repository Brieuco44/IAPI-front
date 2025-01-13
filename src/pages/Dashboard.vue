<template>
  <div class="home-page h-screen flex flex-col p-6 bg-base-100">
    <!-- Header -->
    <header class="mb-6">
      <h1 class="text-3xl font-semibold text-base-content">👋 Bienvenue, {{ authStore.contact?.fullname }}</h1>
    </header>

    <!-- Search Section -->
    <section class="search-bar mb-6">
      <input v-model="searchQuery" @input="searchContacts" type="text" placeholder="Rechercher des contacts..."
        class="input input-bordered w-full rounded-lg px-4 py-2 shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary" />
    </section>

    <!-- Search Results -->
    <section v-if="searchResults.length" class="search-results mb-6 bg-base-200  rounded-lg shadow p-4">
      <h3 class="text-xl font-semibold  mb-4">Résultats de recherche</h3>
      <ul>
        <li v-for="result in searchResults" :key="result.id"
          class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-700">
          <span class="">{{ result.username }}</span>
          <button @click="addContact(result.id)"
            class="btn btn-primary text-sm px-4 py-2 rounded-lg bg-primary text-gray-800 hover:bg-primary-dark">
            Ajouter
          </button>
        </li>
      </ul>
    </section>

    <!-- Contacts List -->
    <section class="contacts-list bg-base-200 rounded-lg shadow p-4">
      <h2 class="text-xl font-semibold mb-4">Mes contacts :</h2>
      <ul>
        <li v-for="contact in contacts" :key="contact.id"
        @click="openChat(contact.room, contact.id, contact.fullname)"
          class="flex justify-between items-center p-3 rounded-lg hover:bg-base-300 hover:cursor-pointer">
          <span class="">{{ contact.fullname }}</span>
          <div class="flex space-x-2">
            <button @click="openChat(contact.room, contact.id, contact.fullname)"
              class="btn btn-primary text-sm px-4 py-2">
              Discussion
            </button>
            <button @click="removeContact(contact.id)"
              class="btn btn-error text-sm px-4 py-2">
              Supprimer
            </button>
          </div>
        </li>
      </ul>
    </section>
  </div>
</template>



<script lang="ts">
import { ref, onMounted, computed, onBeforeUnmount } from "vue";
import { useRouter } from "vue-router";
import { useContactsStore } from "@/stores/contacts";
import { useAuthStore } from "@/stores/auth";
import { Contact } from "@/types/contacts";
import { searchContactAPI } from "@/api/contacts";
import socket, { joinUserRoom, leaveUserRoom } from "@/plugins/socket";

export default {
  name: "dashboard",
  setup() {
    const contactsStore = useContactsStore();
    const router = useRouter();
    const searchQuery = ref("");
    const searchResults = ref<Contact[]>([]);
    const contacts = computed(() => contactsStore.contacts);

    onMounted(() => {
      contactsStore.loadContacts();
    });

    const searchContacts = async () => {
      if (searchQuery.value.trim().length < 2) {
        searchResults.value = [];
        return;
      }
      if (searchQuery.value.trim()) {
        searchResults.value = await searchContactAPI(searchQuery.value);
      } else {
        searchResults.value = [];
      }
    };

    const addContact = async (contactId: string) => {
      try {

        // Wait for the room join confirmation
        joinUserRoom(contactId);

        await contactsStore.addContact(contactId);
        searchQuery.value = ""; // Efface la barre de recherche
        searchResults.value = []; // Efface les résultats de recherche
        console.log(contactsStore.contacts); // Affiche les contacts après ajout but not updated the view
      } catch (error) {
        console.error("Erreur lors de l'ajout du contact :", error);
      }
    };

    const removeContact = async (contactId: string) => {
      try {
        await contactsStore.removeContact(contactId);
        console.log(contactsStore.contacts); // Affiche les contacts après suppression but not updated the view
      } catch (error) {
        console.error("Erreur lors de la suppression du contact :", error);
      }
    };

    const openChat = (room: string | null, contactId: string | null, fullname: string) => {
      router.push({ name: "Chat", params: { room: room, id: contactId, fullname: fullname } });
    };
    
    return {
      searchQuery,
      searchResults,
      contacts,
      searchContacts,
      addContact,
      removeContact,
      openChat,
      authStore: useAuthStore(),
    };
  },
};
</script>
