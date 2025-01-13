import { io } from "socket.io-client";

let socket: any = null;

const connectSocket = () => {
  socket = io("https://apichathouse.enzopenisson.duckdns.org", {
    auth: {
      token: localStorage.getItem("token"),
    },
    reconnection: true, // Enable reconnection attempts
    reconnectionAttempts: 5, // Number of reconnection attempts
    reconnectionDelay: 1000, // Delay between reconnection attempts (in ms)
  });

  // Handle reconnect event
  socket.on("connect", () => {
    console.log("Connected to the server!");
  });

  // Handle disconnect event
  socket.on("disconnect", () => {
    console.log("Disconnected from the server.");
  });

  socket.on("contact_added", (data: any) => {
    console.log("Contact ajouter", data);
  });

  // Handle reconnect attempt event
  socket.on("reconnect_attempt", () => {
    console.log("Reconnecting...");
  });

  // Handle reconnect event
  socket.on("reconnect", (attempt: any) => {
    console.log(`Reconnected on attempt ${attempt}.`);
  });

  // Handle error event
  socket.on("connect_error", (error: any) => {
    console.log("Connection error:", error);
  });

  // Handle reconnect error event
  socket.on("reconnect_error", (error: any) => {
    console.log("Reconnection error:", error);
  });
};

// Initialize connection
connectSocket();

// Rejoindre une salle de conversation
export const joinConversationRoom = (room: string) => {
  socket.emit("join_room", { room });
  console.log("join room", room);
};

export const joinUserRoom = (userid: string | undefined) => {
  if (!userid) return;
  let roomid = "user_" + userid;
  socket.emit("join_room", { roomid });
  console.log("join room user", roomid);
};

export const leaveUserRoom = (userid: string | undefined) => {
  if (!userid) return;
  let roomid = "user_" + userid;
  socket.emit("leave_room", { roomid });
  console.log("leave room user", roomid);
};

// Quitter une salle de conversation
export const leaveConversationRoom = (room: string) => {
  socket.emit("leave_room", { room });
};

// Envoyer un événement de nouveau message
export const emitNewMessage = (message: any) => {
  socket.emit("new_message", message);
  console.log("new message", message);
};

// Function to reset and reconnect socket manually
export const resetSocket = () => {
  socket.disconnect(); // Disconnect existing socket
  socket = null; // Clear the socket object
  connectSocket(); // Reconnect
};

// when online reconnect socket
window.addEventListener("online", () => {
  // verify if socket is disconnected
  if (!socket.connected) {
    resetSocket();
  }
});

export default socket;
