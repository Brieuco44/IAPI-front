import apiRequest from "@/api/apirequest";

const API_URL = "https://apichathouse.enzopenisson.duckdns.org/communication";
// Send Message
export const sendMessage = async (
  receiverId: string,
  message: string,
  id_offline: string | null
) => {
  const url = `${API_URL}/send_message`;
  const method = "POST";
  if (id_offline == null) {
    const body = { receiver_id: receiverId, message };
    let attendre = await apiRequest(url, method, body);
    return attendre;
  } else {
    const body = { receiver_id: receiverId, message, id_offline: id_offline };
    let attendre = await apiRequest(url, method, body);
    return attendre;
  }
};

// Update Message
export const updateMessage = async (
  receiverId: string,
  messageId: string,
  newText: string
) => {
  const url = `${API_URL}/edit_message`;
  const method = "PUT";
  const body = {
    message_id: messageId,
    receiver_id: receiverId,
    new_text: newText,
  };

  return await apiRequest(url, method, body);
};

// Delete Message
export const deleteMessage = async (messageId: string) => {
  const url = `${API_URL}/remove_message`;
  const method = "DELETE";
  const body = { message_id: messageId };

  return await apiRequest(url, method, body);
};

// Fetch Conversation
export const fetchConversation = async (receiverid: string) => {
  const url = `${API_URL}/get_conversation?receiver_id=${receiverid}`;
  const method = "GET";

  return await apiRequest(url, method);
};
