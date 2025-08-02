<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Demo (Chat + Summarizer)</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin-bottom: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        textarea, input[type="text"] { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-top: 10px; }
        button { padding: 10px 20px; border: none; background: #007BFF; color: white; border-radius: 5px; margin-top: 10px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .response { margin-top: 15px; background: #e9ecef; padding: 15px; border-radius: 5px; white-space: pre-wrap; }
    </style>
</head>
<body>

    <h1>Laravel 12 + OpenAI Chat & Summarizer</h1>

    <!-- Chatbot -->
    <div class="box">
        <h2>Chat with AI</h2>
        <form id="chat-form">
            <input type="text" name="message" placeholder="Ask something..." required>
            <button type="submit">Send</button>
        </form>
        <div class="response" id="chat-response"></div>
    </div>

    <!-- Summarizer -->
    <div class="box">
        <h2>Summarize Text</h2>
        <form id="summarize-form">
            <textarea name="text" rows="6" placeholder="Paste text to summarize..." required></textarea>
            <button type="submit">Summarize</button>
        </form>
        <div class="response" id="summarize-response"></div>
    </div>

    <script>
        const chatForm = document.getElementById('chat-form');
        const chatResponse = document.getElementById('chat-response');
        const summarizeForm = document.getElementById('summarize-form');
        const summarizeResponse = document.getElementById('summarize-response');

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(chatForm);
            const message = formData.get('message');
            chatResponse.innerHTML = "Thinking...";

            const res = await fetch('/ai/chat', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message })
            });
            const data = await res.json();
            chatResponse.innerHTML = data.response;
        });

        summarizeForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(summarizeForm);
            const text = formData.get('text');
            summarizeResponse.innerHTML = "Summarizing...";

            const res = await fetch('/ai/summarize', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ text })
            });
            const data = await res.json();
            summarizeResponse.innerHTML = data.summary;
        });
    </script>

</body>
</html>
