<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f3f4f4; }
        .chatbot-container {
            width: 414px;
            min-height: 610px;
            display: block;
            margin: 40px auto;
            padding: 20px;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .messages { height: 420px; overflow-y: auto; border: 1px solid #eee; border-radius: 10px; padding: 12px; }
        .message { margin-bottom: 10px; }
        .message.user { text-align: right; }
        .message .bubble { display: inline-block; padding: 8px 12px; border-radius: 12px; }
        .message.user .bubble { background: #e6f2ff; }
        .message.bot .bubble { background: #f1f1f1; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        let conversationId = null;

        async function startConversation() {
            const res = await fetch(`{{ route('chatbot.start') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({})
            });
            const data = await res.json();
            conversationId = data.conversation_id;
        }

        async function sendMessage(event) {
            event.preventDefault();
            const input = document.getElementById('chat-input');
            const text = input.value.trim();
            if (!text) return;

            const messages = document.getElementById('messages');
            messages.insertAdjacentHTML('beforeend', `<div class="message user"><span class="bubble">${text}</span></div>`);
            input.value = '';
            messages.scrollTop = messages.scrollHeight;

            if (!conversationId) {
                await startConversation();
            }

            const res = await fetch(`{{ route('chatbot.message') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ conversation_id: conversationId, text })
            });
            const data = await res.json();
            const reply = data.answer || '...';
            messages.insertAdjacentHTML('beforeend', `<div class="message bot"><span class="bubble">${reply}</span></div>`);
            messages.scrollTop = messages.scrollHeight;
        }

        window.addEventListener('DOMContentLoaded', () => {
            startConversation();
        });
    </script>
</head>
<body>
    @include('layouts.sidebar')
    <div class="chatbot-container">
        <h4 class="mb-3">Chatbot</h4>
        <div class="small text-muted mb-2">Supports multilingual replies, property/location questions, and live agent handoff.</div>
        <div id="messages" class="messages mb-3"></div>
        <form onsubmit="sendMessage(event)">
            <div class="input-group">
                <input id="chat-input" type="text" class="form-control" placeholder="Type a message..." autocomplete="off" />
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>


