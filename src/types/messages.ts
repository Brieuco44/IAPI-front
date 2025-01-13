export interface Message {
  id: string;
  sender_id: string;
  receiver_id: string;
  text: string;
  date: string;
  edited: boolean;
}
