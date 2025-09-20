// VoltGear Chat Assistant
// This script creates a floating chat assistant that appears on all pages

// Create and append CSS to the document
(function() {
    // Add CSS styles
    const style = document.createElement('style');
    style.innerHTML = `
        .volt-assistant-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .volt-chat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00FFFF, #8A2BE2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .volt-chat-icon:hover {
            transform: scale(1.1);
        }
        
        .volt-chat-icon svg {
            width: 30px;
            height: 30px;
            fill: white;
        }
        
        .volt-chat-box {
            position: absolute;
            bottom: 70px;
            right: 0;
            width: 350px;
            height: 450px;
            background-color: #121212;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
        }
        
        .volt-chat-box.active {
            opacity: 1;
            transform: translateY(0);
            pointer-events: all;
        }
        
        .volt-chat-header {
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .volt-chat-title {
            display: flex;
            align-items: center;
            color: white;
            font-weight: bold;
        }
        
        .volt-chat-title svg {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }
        
        .volt-close-chat {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        
        .volt-chat-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        
        .volt-message {
            margin-bottom: 15px;
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 15px;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .volt-bot-message {
            background-color: #1e1e1e;
            color: #ffffff;
            border-top-left-radius: 5px;
            align-self: flex-start;
        }
        
        .volt-user-message {
            background-color: #8A2BE2;
            color: white;
            border-top-right-radius: 5px;
            align-self: flex-end;
        }
        
        .volt-chat-input {
            padding: 15px;
            border-top: 1px solid #333;
            display: flex;
        }
        
        .volt-input-field {
            flex: 1;
            padding: 10px 15px;
            border: none;
            border-radius: 20px;
            background-color: #1e1e1e;
            color: white;
            font-size: 14px;
        }
        
        .volt-input-field:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 255, 255, 0.5);
        }
        
        .volt-send-button {
            background-color: #8A2BE2;
            color: white;
            border: none;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            margin-left: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }
        
        .volt-send-button:hover {
            background-color: #7A1BD2;
        }
        
        .volt-typing-indicator {
            display: flex;
            align-items: center;
            margin-top: 5px;
            margin-bottom: 15px;
            align-self: flex-start;
        }
        
        .volt-typing-dot {
            width: 8px;
            height: 8px;
            background-color: #00FFFF;
            border-radius: 50%;
            margin-right: 4px;
            animation: voltTypingAnimation 1.4s infinite ease-in-out;
        }
        
        .volt-typing-dot:nth-child(1) {
            animation-delay: 0s;
        }
        
        .volt-typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .volt-typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes voltTypingAnimation {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-5px);
            }
        }
    `;
    document.head.appendChild(style);

    // Create the chat assistant elements
    function createChatAssistant() {
        const container = document.createElement('div');
        container.className = 'volt-assistant-container';
        
        const chatIcon = document.createElement('div');
        chatIcon.className = 'volt-chat-icon';
        chatIcon.innerHTML = `
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z" fill="white"/>
                <path d="M7 9h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2z" fill="white"/>
            </svg>
        `;
        
        const chatBox = document.createElement('div');
        chatBox.className = 'volt-chat-box';
        chatBox.innerHTML = `
            <div class="volt-chat-header">
                <div class="volt-chat-title">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" fill="white"/>
                        <path d="M13 7h-2v5.5l3.5 3.5 1.42-1.42-2.92-2.92z" fill="white"/>
                    </svg>
                    VOLT GEAR Assistant
                </div>
                <button class="volt-close-chat">&times;</button>
            </div>
            <div class="volt-chat-messages">
                <div class="volt-message volt-bot-message">
                    Hi there! 👋 I'm your VOLT GEAR assistant. How can I help you with gaming gear today?
                </div>
            </div>
            <div class="volt-chat-input">
                <input type="text" class="volt-input-field" placeholder="Ask about gaming gear...">
                <button class="volt-send-button">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" fill="white"/>
                    </svg>
                </button>
            </div>
        `;
        
        container.appendChild(chatBox);
        container.appendChild(chatIcon);
        document.body.appendChild(container);
        
        // Add event listeners
        chatIcon.addEventListener('click', () => {
            chatBox.classList.toggle('active');
        });
        
        const closeButton = chatBox.querySelector('.volt-close-chat');
        closeButton.addEventListener('click', () => {
            chatBox.classList.remove('active');
        });
        
        const sendButton = chatBox.querySelector('.volt-send-button');
        const inputField = chatBox.querySelector('.volt-input-field');
        const messagesContainer = chatBox.querySelector('.volt-chat-messages');
        
        function sendMessage() {
            const message = inputField.value.trim();
            if (message) {
                // Add user message
                addMessage(message, 'user');
                inputField.value = '';
                
                // Show typing indicator
                showTypingIndicator();
                
                // Get bot response after a delay
                setTimeout(() => {
                    removeTypingIndicator();
                    const response = getBotResponse(message);
                    addMessage(response, 'bot');
                }, 1500);
            }
        }
        
        sendButton.addEventListener('click', sendMessage);
        inputField.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
        
        function addMessage(message, sender) {
            const messageElement = document.createElement('div');
            messageElement.className = `volt-message volt-${sender}-message`;
            messageElement.textContent = message;
            messagesContainer.appendChild(messageElement);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        function showTypingIndicator() {
            const indicator = document.createElement('div');
            indicator.className = 'volt-typing-indicator';
            indicator.innerHTML = `
                <div class="volt-typing-dot"></div>
                <div class="volt-typing-dot"></div>
                <div class="volt-typing-dot"></div>
            `;
            messagesContainer.appendChild(indicator);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        function removeTypingIndicator() {
            const indicator = messagesContainer.querySelector('.volt-typing-indicator');
            if (indicator) {
                messagesContainer.removeChild(indicator);
            }
        }
        
        function getBotResponse(message) {
            // Convert message to lowercase for easier matching
            const lowerMsg = message.toLowerCase();
            
            // Gaming peripherals responses
            if (lowerMsg.includes('keyboard') && (lowerMsg.includes('recommend') || lowerMsg.includes('best'))) {
                return "For gaming keyboards, our VOLT Spark RGB Mechanical Keyboard with hot-swappable switches is highly recommended for competitive gaming. We also have the VOLT Thunder TKL if you prefer a smaller form factor!";
            }
            
            if (lowerMsg.includes('mouse') && (lowerMsg.includes('recommend') || lowerMsg.includes('best'))) {
                return "Our VOLT Precision Pro gaming mouse features adjustable DPI up to 25,600, ergonomic design, and 8 programmable buttons - perfect for both FPS and MOBA games!";
            }
            
            if (lowerMsg.includes('headset') || lowerMsg.includes('headphone')) {
                return "Check out our VOLT Sonic gaming headsets! They feature 7.1 surround sound, memory foam ear cushions, and a noise-cancelling microphone for crystal clear communication.";
            }
            
            if (lowerMsg.includes('mousepad') || lowerMsg.includes('mouse pad')) {
                return "Our VOLT Control and VOLT Speed mousepads offer different surface textures for precision or fast movements. The XL and XXL sizes give you plenty of desk coverage!";
            }
            
            // Gaming chair related
            if (lowerMsg.includes('chair')) {
                return "The VOLT Commander gaming chair provides premium comfort with 4D adjustable armrests, multi-tilt mechanism, and lumbar support. Perfect for those long gaming sessions!";
            }
            
            // Game genres
            if (lowerMsg.includes('fps') || lowerMsg.includes('shooter')) {
                return "For FPS games, we recommend our VOLT Precision Pro mouse with adjustable weights, the VOLT Spark keyboard with fast linear switches, and our low-latency VOLT Sonic headset.";
            }
            
            if (lowerMsg.includes('moba') || lowerMsg.includes('league') || lowerMsg.includes('dota')) {
                return "MOBA players love our VOLT Tactician mouse with its programmable side buttons. Pair it with our VOLT Spark keyboard with tactile switches for the best experience!";
            }
            
            if (lowerMsg.includes('mmo') || lowerMsg.includes('rpg')) {
                return "For MMO/RPG games, our VOLT Commander mouse with 12 side buttons is perfect for all your abilities and macros. It works great with our full-size VOLT Spark keyboard!";
            }
            
            // Pricing and sales
            if (lowerMsg.includes('price') || lowerMsg.includes('cost') || lowerMsg.includes('discount')) {
                return "Our products range from ₹2,500 for mousepads to ₹15,000 for our premium gaming chair. Check our website for current promotions - we often have seasonal sales with up to 30% off!";
            }
            
            // Shipping and delivery
            if (lowerMsg.includes('shipping') || lowerMsg.includes('delivery')) {
                return "We offer free shipping on orders over ₹5,000! Standard delivery takes 3-5 business days, and express delivery (additional fee) is 1-2 business days. We ship throughout India.";
            }
            
            // Warranty and support
            if (lowerMsg.includes('warranty') || lowerMsg.includes('support')) {
                return "All VOLT GEAR products come with a 2-year warranty against manufacturing defects. Our support team is available 24/7 via email at support@voltgear.com or by phone at +91-XXXX-XXXXXX.";
            }
            
            // Product comparison
            if (lowerMsg.includes('compare') || lowerMsg.includes('difference')) {
                return "Looking to compare products? Visit our website's comparison tool to see detailed specs side by side. You can also chat with our support team for personalized recommendations based on your gaming style.";
            }
            
            // Default responses
            if (lowerMsg.includes('hello') || lowerMsg.includes('hi') || lowerMsg.includes('hey')) {
                return "Hey there! How can I help you with gaming gear today?";
            }
            
            if (lowerMsg.includes('thank')) {
                return "You're welcome! Let me know if you have any other questions about our gaming products.";
            }
            
            // Catch-all response
            return "I'd be happy to help with your gaming gear needs! You can ask me about keyboards, mice, headsets, mousepads, gaming chairs, or specific recommendations for different game types. What would you like to know?";
        }
    }

    // Wait for DOM to be fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createChatAssistant);
    } else {
        createChatAssistant();
    }
})();