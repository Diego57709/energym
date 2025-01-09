<!-- Reemplaza este bloque si ya incluyes Bootstrap y Bootstrap Icons en otra parte -->
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
    z-index: 9999; /* Queda por encima de otros elementos */
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
    height: 480px;
    margin-top: 10px; 
    display: none; /* Oculto por defecto */
    flex-direction: column;
  }
  .chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
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
    background: #eee;
    color: #333;
    border-radius: 10px 10px 10px 0;
  }
  .bubble {
    padding: 0.6rem 1rem;
    max-width: 70%;
    word-wrap: break-word;
  }
  .chat-input-container {
    border-top: 1px solid #ccc;
    padding: 0.5rem;
    display: flex;
    gap: 0.5rem;
  }
  .chat-input-container input {
    flex: 1;
  }
  /* Animación de typing (3 puntos) */
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
</style>

<div class="chat-widget-container">
  <!-- Botón flotante para abrir/cerrar el chat -->
  <button 
    id="chatToggleBtn" 
    class="btn btn-primary chat-toggle-btn"
    title="Abrir/Cerrar Chat"
  >
    <i class="bi bi-chat-dots"></i>
  </button>

  <!-- Tarjeta/ventana del chat -->
  <div id="chatCard" class="card chat-card d-none">
    <div class="card-header bg-primary text-white">
      ChatBot del Gimnasio
    </div>
    <div class="chat-messages" id="chatMessages">
      <!-- Aquí se mostrarán los mensajes -->
    </div>
    <div class="chat-input-container">
      <input 
        type="text" 
        id="chatInput" 
        class="form-control" 
        placeholder="Escribe tu consulta..."
      />
      <button class="btn btn-primary" id="sendBtn">
        <i class="bi bi-send"></i>
      </button>
    </div>
  </div>
</div>

<!-- Si tu página no tiene Bootstrap JS incluido, añádelo aquí -->
<script 
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>

<script>
  const chatToggleBtn = document.getElementById("chatToggleBtn");
  const chatCard = document.getElementById("chatCard");
  const chatMessages = document.getElementById("chatMessages");
  const chatInput = document.getElementById("chatInput");
  const sendBtn = document.getElementById("sendBtn");

  // Abre/cierra el chat
  chatToggleBtn.addEventListener("click", () => {
  chatCard.classList.toggle("d-none");
});


  // Enviar mensaje al hacer clic en el botón
  sendBtn.addEventListener("click", () => {
    sendMessage();
  });

  // Enviar mensaje al presionar Enter
  chatInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      sendMessage();
    }
  });

  function sendMessage() {
    const userText = chatInput.value.trim();
    if (!userText) return;

    // 1. Agrega el mensaje del usuario (a la derecha)
    addMessage(userText, "user");
    chatInput.value = "";

    // 2. Agrega el indicador de "typing" para el bot
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

    // 3. Llamar a la API (ej. "response.php")
    fetch("response.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ text: userText }),
    })
      .then((res) => res.text())
      .then((botReply) => {
        // Remover indicador de escritura
        chatMessages.removeChild(typingIndicator);

        // Añadir mensaje del bot a la izquierda
        addMessage(botReply, "bot");
        scrollToBottom();
      })
      .catch((error) => {
        // Manejo de errores
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
