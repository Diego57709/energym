<!-- Frontend del ChatBot -->
<link 
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
  rel="stylesheet"
/>
<link 
  rel="stylesheet" 
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css"
/>

<style>
  .chat-widget-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
  }
  .chat-toggle-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .chat-card {
    width: 320px;
    max-height: 50dvh;
    height: 50dvh !important;
    margin-top: 10px; 
    display: none;
    flex-direction: column;
    border-radius: 12px;
    overflow: hidden;
    border: none !important;
    border-top: 4px solid #0d6efd;
    border-bottom: 4px solid black;
    transform: none !important;
    box-shadow: none !important;
  }
  .chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    border-left: 1px solid rgb(172, 172, 172) !important; 
    border-right: 1px solid rgb(172, 172, 172) !important; 
  }
  .message {
    margin-bottom: 0.5rem;
    display: flex;
  }
  .message.user {
    justify-content: flex-end;
  }
  .message.user .bubble {
    background: #e1f5fe;
    color: #333;
    border-radius: 10px 10px 0 10px;
  }
  .message.bot {
    justify-content: flex-start;
  }
  .message.bot .bubble {
    background: #f1f1f1;
    color: #333;
    border-radius: 10px 10px 10px 0;
  }
  .bubble {
    padding: 0.6rem 1rem;
    max-width: 70%;
    word-wrap: break-word;
    border: 1px solid #ccc;
    font-size: 0.85rem;
    line-height: 1.3;
  }
  .chat-input-container {
    border-top: 1px solid #ccc;
    padding: 0.5rem;
    display: flex;
    gap: 0.5rem;
    border: 1px solid rgb(172, 172, 172);
    border-top: none !important;
  }
  .chat-input-container input {
    flex: 1;
    font-size: 0.85rem;
  }
  .typing-indicator {
    width: 35px;
    height: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .typing-indicator .dot {
    width: 6px;
    height: 6px;
    background-color: #999;
    border-radius: 50%;
    animation: bounce 1s infinite;
  }
  .typing-indicator .dot:nth-child(2) {
    animation-delay: 0.2s;
  }
  .typing-indicator .dot:nth-child(3) {
    animation-delay: 0.4s;
  }
  @keyframes bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
  }
  #closeChatBtn {
    border: none;
    background: transparent;
  }
</style>

<div class="chat-widget-container">
  <!-- Botón flotante del chat -->
  <button id="chatToggleBtn" class="btn btn-primary chat-toggle-btn" title="Abrir Chat">
    <i class="bi bi-chat-dots" id="chatIcon"></i>
  </button>

  <!-- Tarjeta del chat -->
  <div id="chatCard" class="card chat-card d-none">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <span id="chatTitle">Chat con Lenny</span>
      <!-- Botón de cierre en la barra superior -->
      <button id="closeChatBtn" class="btn btn-sm" style="color:white;">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="chat-messages" id="chatMessages">
    </div>
    <div class="chat-input-container">
      <input type="text" id="chatInput" class="form-control" placeholder="Escribe tu consulta..." />
      <button class="btn btn-primary" id="sendBtn">
        <i class="bi bi-send"></i>
      </button>
    </div>
  </div>
</div>

<script 
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>

<script>
  const chatToggleBtn = document.getElementById("chatToggleBtn");
  const chatCard = document.getElementById("chatCard");
  const chatMessages = document.getElementById("chatMessages");
  const chatInput = document.getElementById("chatInput");
  const sendBtn = document.getElementById("sendBtn");
  const chatTitle = document.getElementById("chatTitle");
  const closeChatBtn = document.getElementById("closeChatBtn");

  // Lista de nombres aleatorios para el bot
  const botNames = ["Lenny", "Max", "Charlie", "Alex", "Chris", "Sam", "Taylor", "Jordan"];

  // Selección de nombre aleatorio al cargar la página
  const randomName = botNames[Math.floor(Math.random() * botNames.length)];
  chatTitle.innerText = `Chat con ${randomName}`;

  // Abrir/cerrar chat al hacer clic en el botón flotante
  chatToggleBtn.addEventListener("click", toggleChat);
  closeChatBtn.addEventListener("click", toggleChat);

  function toggleChat() {
    chatCard.classList.toggle("d-none");
    chatToggleBtn.style.display = chatCard.classList.contains("d-none") ? "flex" : "none"; // Mostrar u ocultar botón
  }

  // Enviar mensaje con el botón
  sendBtn.addEventListener("click", sendMessage);
  chatInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      sendMessage();
    }
  });

  function sendMessage() {
    const userText = chatInput.value.trim();
    if (!userText) return;

    addMessage(userText, "user");
    chatInput.value = "";

    const typingIndicator = document.createElement("div");
    typingIndicator.classList.add("message", "bot");
    typingIndicator.innerHTML = `
      <div class="bubble">
        <div class="typing-indicator">
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
        </div>
      </div>
    `;
    chatMessages.appendChild(typingIndicator);
    scrollToBottom();

    // Enviar al backend
    fetch("/partials/response.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ text: userText, botName: randomName }), // Enviar nombre aleatorio al backend
    })
      .then((res) => res.json())
      .then((data) => {
        setTimeout(() => {
          chatMessages.removeChild(typingIndicator); // Quitar "typing" después del delay
          addMessage(data.response || "No entendí tu pregunta. Inténtalo de nuevo.", "bot");
          scrollToBottom();
        }, 1000);
      })
      .catch((error) => {
        chatMessages.removeChild(typingIndicator);
        addMessage("Error: " + error.message, "bot");
        scrollToBottom();
      });
  }

  function addMessage(text, sender) {
    const msgDiv = document.createElement("div");
    msgDiv.classList.add("message", sender);
    msgDiv.innerHTML = `<div class="bubble">${text}</div>`;
    chatMessages.appendChild(msgDiv);
  }

  function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }
</script>
