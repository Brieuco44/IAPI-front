import { defineStore } from "pinia";
import { ref } from "vue";
import { Contact } from "@/types/contacts";
import {
  getContactsAPI,
  addContactAPI,
  removeContactAPI,
} from "@/api/contacts";

export const useContactsStore = defineStore("contacts", () => {
  const contacts = ref<Contact[]>([]);

  // Charger la liste des contacts
  const loadContacts = async () => {
    contacts.value = await getContactsAPI();
  };

  // Ajouter un contact
  const addContact = async (contactId: string) => {
    await addContactAPI(contactId);
    await loadContacts(); // Recharge les contacts
  };

  // Supprimer un contact
  const removeContact = async (contactId: string) => {
    await removeContactAPI(contactId);
    await loadContacts(); // Recharge les contacts
  };

  // Gérer les événements en temps réel
  // socket.on("contacts_updated", (data) => {
  //   contacts.value = data.contacts;
  // });

  return { contacts, loadContacts, addContact, removeContact };
});
