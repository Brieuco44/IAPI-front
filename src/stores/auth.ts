// src/stores/auth.ts
import { defineStore } from "pinia";
import axios from "axios";
import { Contact } from "@/types/contacts";

interface AuthState {
  contact: Contact | null;
  token: string | null;
}

export const useAuthStore = defineStore("auth", {
  state: (): AuthState => ({
    contact: null,
    token: localStorage.getItem("token"), // Initialize from localStorage
  }),
  actions: {
    async login(credentials: {
      email: string;
      password: string;
    }): Promise<void> {
      const response = await axios.post<{ token: string; contact: Contact }>(
        "https://apichathouse.enzopenisson.duckdns.org/auth/login",
        credentials
      );

      this.token = response.data.token; // Update the reactive state
      localStorage.setItem("token", this.token);
      axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;
    },
    async logout(): Promise<void> {
      try {
        await axios.post(
          "https://apichathouse.enzopenisson.duckdns.org/auth/logout",
          {},
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
          }
        );
      } catch {}

      this.token = null; // Update the reactive state
      localStorage.removeItem("token");
      delete axios.defaults.headers.common["Authorization"];
    },
    // Register function
    async register(data: {
      fullname: string;
      username: string;
      password: string;
      telephone: string;
      birthdate: string;
      email: string;
    }): Promise<void> {
      await axios.post(
        "https://apichathouse.enzopenisson.duckdns.org/auth/register",
        data
      );
    },
    // Fetch contact function
    async fetchContact(): Promise<void> {
      if (this.token) {
        try {
          const response = await axios.get<Contact>(
            "https://apichathouse.enzopenisson.duckdns.org/auth/profile",
            {
              headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
              },
            }
          );

          this.contact = response.data;
        } catch {
          // this.logout();
        }
      }
    },
  },
  getters: {
    isLogin(state): boolean {
      return !!state.token; // Reactively
    },
  },
});
