import apiRequest from "@/api/apirequest";

const API_URL = "https://apichathouse.enzopenisson.duckdns.org/contact"; // URL de votre backend

// Get Contacts
export const getContactsAPI = async () => {
  const url = `${API_URL}/get_contacts`;
  const method = "GET";

  return await apiRequest(url, method);
};

// Add Contact
export const addContactAPI = async (contactId: string) => {
  const url = `${API_URL}/add`;
  const method = "POST";
  const body = { contact_id: contactId };

  return await apiRequest(url, method, body);
};

// Remove Contact
export const removeContactAPI = async (contactId: string) => {
  const url = `${API_URL}/remove`;
  const method = "POST";
  const body = { contact_id: contactId };

  return await apiRequest(url, method, body);
};

// Search Contact
export const searchContactAPI = async (username: string) => {
  const url = `${API_URL}/search?query=${username}`;
  const method = "GET";

  return await apiRequest(url, method);
};
