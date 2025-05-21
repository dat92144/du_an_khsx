// resources/js/socket.js
import { io } from "socket.io-client";

// Kết nối tới WebSocket server chạy ở localhost:3001
const socket = io("http://localhost:3001", {
  transports: ["websocket"]
});

export default socket;
