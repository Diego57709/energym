<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>AI ChatBot</title>
	<style>
        /* General Page Styling */
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(to right, #667eea, #764ba2);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Chat Container */
#chat-container {
    width: 400px;
    background: #fff;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
}

/* Chat Box */
#chat-box {
    height: 350px;
    overflow-y: auto;
    border: none;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 10px;
    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* User Message */
.user-message {
    background: #B6B6B6;
    color: white;
    padding: 10px;
    border-radius: 10px;
    margin: 8px 0;
    text-align: right;
    max-width: 75%;
    align-self: flex-end;
}

/* Bot Message */
.bot-message {
    background: #8D8D8D;
    color: white;
    padding: 10px;
    border-radius: 10px;
    margin: 8px 0;
    text-align: left;
    max-width: 75%;
    align-self: flex-start;
}

/* Chat Input */
#user-input {
    flex: 1;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 20px;
    outline: none;
    font-size: 16px;
    transition: 0.3s;
}

#user-input:focus {
    border-color: #007bff;
}

/* Send Button */
button {
    padding: 12px 20px;
    margin-left: 10px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 16px;
    transition: 0.3s;
}

button:hover {
    background: #0056b3;
}

/* Input Container */
.input-container {
    display: flex;
    margin-top: 15px;
}

/* Smooth Scroll */
#chat-box::-webkit-scrollbar {
    width: 8px;
}

#chat-box::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}
    </style>
</head>
<body>
    <div id="chat-container">
    	<h2>AI Chatbot</h2>
    	<div id="chat-box"></div>
    	<div class="input-container">
    		<input type="text" id="user-input"  placeholder="Type your message...">
    		<button onclick="sendMessage()">Send</button>
    	</div>
    </div>
    <script type="text/javascript">
        function sendMessage(){
	const userInput = document.getElementById('user-input').value.trim();

    if (userInput === "") return;

    const chatBox = document.getElementById('chat-box');
    // append user message
    const userMessage = document.createElement('div');
    userMessage.className = 'user-message';
    userMessage.textContent = userInput;
    chatBox.appendChild(userMessage);

    fetch("chatbottest.php", {
    	method: 'POST',
    	headers: {'Content-Type': 'application/json'},
    	body: JSON.stringify({message: userInput})
    }).then(respose=> respose.json())
      .then(data => {
      	const botMessage = document.createElement('div');
      	botMessage.className = 'bot-message';
        botMessage.textContent = data.error ? `Bot: ${data.error}`:  `Bot: ${data.response}`;
       chatBox.appendChild(botMessage);
       document.getElementById('user-input').value='';
       chatBox.scrollTop = chatBox.scrollHeight;
    }).catch(error=> {
    	const errorMessage = document.createElement('div');
      	errorMessage.className = 'bot-message';
        errorMessage.textContent = 'Bot: Failed to fetch  respose.';
       chatBox.appendChild(errorMessage);
    });
}
    </script>
</body>
</html>