<x-app-layout>

@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">Messages</h2>

    <form id="messageForm" class="mb-4">
        @csrf
        <div class="flex">
            <input type="text" id="receiver_id" placeholder="Receiver ID" class="border rounded p-2 mr-2" required>
            <input type="text" id="message" placeholder="Type your message..." class="border rounded p-2 flex-grow" required>
            <button type="submit" class="bg-blue-500 text-white rounded p-2 ml-2">Send</button>
        </div>
    </form>

    <div id="messages" class="mt-4">
        <h3 class="text-xl font-semibold">Message History</h3>
        <ul id="messageList" class="list-disc pl-5">
            <!-- Messages will be appended here -->
        </ul>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const messageForm = document.getElementById('messageForm');
        const messageList = document.getElementById('messageList');

        // Fetch messages on page load
        fetchMessages();

        messageForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const receiverId = document.getElementById('receiver_id').value;
            const message = document.getElementById('message').value;

            try {
                const response = await fetch('/messages/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ receiver_id: receiverId, message: message }),
                });

                if (response.ok) {
                    document.getElementById('message').value = ''; // Clear the message input
                    fetchMessages(); // Refresh the message list
                } else {
                    console.error('Error sending message:', response.statusText);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        async function fetchMessages() {
            try {
                const response = await fetch('/messages');
                const messages = await response.json();

                messageList.innerHTML = ''; // Clear the current list
                messages.forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = `[${msg.timestamp}] ${msg.sender_id} to ${msg.receiver_id}: ${msg.message}`;
                    messageList.appendChild(li);
                });
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        }
    });
</script>


</x-app-layout>