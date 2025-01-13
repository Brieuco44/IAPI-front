<template>
  <div class="flex flex-col h-screen">
    <!-- Header -->
    <div class="header p-4 flex items-center space-x-4">
      <!-- Back Arrow -->
      <button @click="quitChat" class="hover:text-base-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <!-- Chat Title -->
      <h2 class="text-lg font-bold">Chat avec {{ fullname }}</h2>
    </div>

    <!-- Messages -->
    <div class="flex-1 p-4 overflow-y-auto mb-16 flex flex-col-reverse">
      <div v-for="message in messages" :key="message.id" class="mb-4 transition-all duration-500 opacity-0 animate-fadeInUp">
        <!-- Sent Message -->
        <div v-if="message.sender_id === currentUserId" class="chat chat-end">
          <div class="chat-header flex justify-between gap-1">
            <time class="text-xs opacity-50">{{ formatDate(message.date) }}</time>
            <!-- Trois points -->
            <div class="" @click="openContextMenu(message)">
              <div class="dropdown dropdown-left dropdown-end">
                <button class="text-xs opacity-50" tabindex="0" role="button">
                  <font-awesome-icon icon="fa-solid fa-ellipsis" />
                </button>
                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
                  <li>
                    <button @click="editMessage(message)" class="hover:text-yellow-400 mb-1 transition">
                      Modifier
                    </button>
                  </li>
                  <li>
                    <button @click="Supprmessage(message.id)" class="hover:text-red-500 transition">
                      Supprimer
                    </button>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div :class="[
            'chat-bubble',
            message.id.includes('offline-') ? 'chat-bubble-error text-white' : 'chat-bubble-primary text-white'
          ]">
            {{ message.text }}
          </div>
          <div class="chat-footer opacity-50">
            <p v-if="message.edited" class="text-xs ">edité</p>
            <p v-if="message.id.includes('offline-')" class="text-xs text-red-400 ">
              Echec de l'envoi
            </p>
          </div>
        </div>

        <!-- Received Message -->
        <div v-else class="chat chat-start transition-shadow duration-300 ease-in-out opacity-0 animate-fadeInLeft">
          <div class="chat-header">
            <span class="text-xs opacity-50">{{ formatDate(message.date) }}</span>
          </div>
          <div class="chat-bubble">
            {{ message.text }}
          </div>
          <div class="chat-footer opacity-50">
            <p v-if="message.edited" class="text-xs">edité</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Input -->
    <div class="w-full p-4 bg-base-100 sticky bottom-0 z-10">
      <div class="flex items-center space-x-2">
        <!-- Text Input -->
        <input v-model="messageText" type="text" @keypress.enter="sendMessage" class="input input-bordered flex-1"
               placeholder="Écrire un message..." />

        <!-- Send Button -->
        <button @click="sendMessage" class="btn btn-primary">
          <font-awesome-icon icon="fa-solid fa-paper-plane" />
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, onBeforeUnmount, watch } from "vue";
import { useRoute } from "vue-router";
import { joinConversationRoom, leaveConversationRoom } from "@/plugins/socket";
import socket from "@/plugins/socket";
import { sendMessage, fetchConversation, deleteMessage, updateMessage } from "@/api/messages";
import { useAuthStore } from "@/stores/auth";
import { Message } from "@/types/messages";
import { useNetwork, useWebNotification } from "@vueuse/core";
import { syncOfflineApiRequests } from "@/api/apirequest";

import router from "@/router";

export default defineComponent({
  setup() {
    const route = useRoute();
    const conversationId = route.params.room as string;
    const receiverId = route.params.id as string;
    const fullname = route.params.fullname as string;
    const authStore = useAuthStore();
    const currentUserId = authStore.contact?.id || null;
    const messages = ref<Message[]>([]);
    const messageText = ref("");
    const contactName = ref("Contact");
    const hoveredMessageId = ref<string | null>(null);
    const contextMenuMessageId = ref<string | null>(null);
    const showEmojiPicker = ref(false);
    const search = ref(""); // Ajout d'un champ de recherche pour l'EmojiPicker
    const { isOnline } = useNetwork();

    // Web Notification
    const { isSupported, show } = useWebNotification({
      title: "New Message",
    });

    const notifyUser = (message: string, senderName: string) => {
      if (isSupported.value) {
        show({
          title: `Message from ${senderName}`,
          body: message,
          icon: "logo.svg",
        });
      }
    };

    // Ajouter un emoji au texte
    const addEmoji = (emoji: any) => {
      messageText.value += emoji.emoji;
    };

    // Charger les messages de la conversation
    const loadMessages = async () => {
      try {
        const response = await fetchConversation(receiverId);
        messages.value = response;

      } catch (error) {
        console.error("Failed to load messages:", error);
      }
    };

    // Envoyer un message
    const sendMessageToServer = async () => {
      if (!messageText.value.trim()) return;

      const text = messageText.value.trim();
      messageText.value = "";

      // Supprimer le brouillon sauvegardé
      const draftMessages = JSON.parse(localStorage.getItem("draftMessages") || "{}");
      delete draftMessages[conversationId];
      localStorage.setItem("draftMessages", JSON.stringify(draftMessages));

      let timestampInSeconds = Math.floor(Date.now() / 1000);

      // if offline unshift the message bc it will be added to the chat when the user is back online
      let idoffline = null;
      if (!isOnline.value) {
        idoffline = "offline-" + Date.now();
        messages.value.unshift({
          id: idoffline, // how can we associate this with the actual message id?
          sender_id: currentUserId as string,
          receiver_id: receiverId,
          text,
          date: timestampInSeconds.toString(),
          edited: false,
        });
      }

      try {
        const response = await sendMessage(receiverId, text, idoffline);
      } catch (error) {
        console.error("Failed to send message:", error);
      }
    };

    // Recevoir un message en temps réel
    const receiveMessage = (message: any) => {
      // if offline add to the chat
      if (
        (message.sender_id === currentUserId && message.receiver_id === receiverId) ||
        (message.receiver_id === currentUserId && message.sender_id === receiverId)
      ) {
        messages.value.unshift(message);
      }

      // Trigger notification for received message
      if (message.sender_id !== currentUserId) {
        notifyUser(message.text, fullname);
      }
    };

    const quitChat = () => {
      router.push({ name: "Dashboard" });
    };

    const receiveUpdateMessage = (editEvent: any) => {

      // Extract relevant data from the emitted event
      const { message_id, new_text, edited } = editEvent;

      // Update the message in the list if it exists
      messages.value = messages.value.map((msg) => {
        if (msg.id === message_id) {
          return {
            ...msg,
            text: new_text,
            edited: edited || false, // Default to false if `edited` is not present
          };
        }
        return msg;
      });
    };


    // Supprimer un message
    const Supprmessage = async (messageId: string) => {
      try {
        await deleteMessage(messageId);
        messages.value = messages.value.filter((message) => message.id !== messageId);
      } catch (error) {
        console.error("Failed to delete message:", error);
      }
    };

    const openContextMenu = (message: Message) => {
      contextMenuMessageId.value = contextMenuMessageId.value === message.id ? null : message.id;
    };


    // Modifier un message
    const editMessage = async (message: Message) => {
      const newText = prompt("Modifier le message :", message.text);

      if (!isOnline.value) {
        // update the message in the chat
        messages.value = messages.value.map((msg) => {
          if (msg.id === message.id) {
            return {
              ...msg,
              text: newText,
              edited: true,
            };
          }
          return msg;
        }) as Message[];
      }


      if (newText && newText.trim() !== message.text) {
        try {
          const updatedMessage = await updateMessage(receiverId, message.id, newText.trim());
        } catch (error) {
          console.error("Failed to edit message:", error);
        }
      }
    };

    const formatDate = (date: string) => {
      // Convert the timestamp to number
      const timestamp = Number(date);

      const optionsTime: Intl.DateTimeFormatOptions = { hour: "numeric", minute: "numeric", hour12: false };
      const messageDate = new Date(timestamp * 1000); // Convert from seconds to milliseconds
      const currentDate = new Date();
      const diff = currentDate.getTime() - messageDate.getTime();

      // If less than 1 minute
      if (diff < 60000) { // 60,000 ms = 1 minute
        return "now";
      }

      // If less than 24 hours
      if (diff < 86400000) { // 86,400,000 ms = 24 hours
        return messageDate.toLocaleTimeString("fr-FR", optionsTime);
      }

      // Return the full date in French if more than 24 hours
      return messageDate.toLocaleDateString("fr-FR");
    };

    const saveDraft = () => {
      const draftMessages = JSON.parse(localStorage.getItem("draftMessages") || "{}");
      draftMessages[conversationId] = messageText.value;
      localStorage.setItem("draftMessages", JSON.stringify(draftMessages));
    };

    const loadDraft = () => {
      const draftMessages = JSON.parse(localStorage.getItem("draftMessages") || "{}");
      messageText.value = draftMessages[conversationId] || "";
    };

    const watchLocalStorage = () => {
      setInterval(async () => {
        const offlineRequests = JSON.parse(localStorage.getItem("offlineApiRequests") || "[]");

        // Only proceed if there are unsynced requests and the user is online
        if (offlineRequests.length > 0 && isOnline.value) {
          console.log("Attempting to sync unsynced requests...");
          try {
            await syncOfflineApiRequests(async () => {
              await loadMessages();
            }
            );
            console.log("All unsynced requests processed.");
          } catch (error) {
            console.error("Error syncing offline requests:", error);
          }
        } else if (offlineRequests.length === 0) {
          console.log("No offline requests to sync.");
        }
      }, 5000); // Check every 5 seconds
    };



    watch(
      () => messageText.value,
      saveDraft
    );

    onMounted(() => {
      loadDraft();
      loadMessages(); // Assume this function loads previously saved messages
      joinConversationRoom(conversationId);
      watchLocalStorage();

      socket.on("new_message", receiveMessage);
      socket.on("edit_message", receiveUpdateMessage);
      socket.on("reload_messages", loadMessages);

      // Clean up on unmount
      onBeforeUnmount(() => {
        leaveConversationRoom(conversationId);
        socket.off("new_message", receiveMessage);
        socket.off("edit_message", receiveUpdateMessage);
      });
    });

    return {
      fullname,
      messages,
      messageText,
      currentUserId,
      contactName,
      sendMessage: sendMessageToServer,
      Supprmessage,
      formatDate,
      editMessage,
      hoveredMessageId,
      contextMenuMessageId,
      openContextMenu,
      quitChat,
    };
  },
});

</script>
<style scoped>
@keyframes fadeInUp {
  0% {
    opacity: 0;
    transform: translateY(10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInLeft {
  0% {
    opacity: 0;
    transform: translateX(-10px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-fadeInUp {
  animation: fadeInUp 0.5s ease-out forwards;
}

.animate-fadeInLeft {
  animation: fadeInLeft 0.5s ease-out forwards;
}
</style>
