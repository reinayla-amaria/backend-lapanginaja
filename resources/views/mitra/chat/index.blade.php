<x-app-layout>
    <div class="py-8 px-6 lg:px-8 max-w-7xl mx-auto bg-gray-50 min-h-screen">

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Pesan Masuk</h2>
            <p class="text-gray-500 text-sm mt-1">Chat dari penyewa yang sedang aktif booking</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[75vh]">

            {{-- SIDEBAR: LIST PENYEWA --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-100 bg-blue-50">
                    <h3 class="font-bold text-gray-800">Daftar Chat</h3>
                </div>
                <div class="overflow-y-auto flex-1" id="chat-list">
                    {{-- Diisi oleh JavaScript dari Firebase --}}
                    <div class="p-4 text-center text-gray-400 text-sm" id="empty-list">
                        Memuat daftar chat...
                    </div>
                </div>
            </div>

            {{-- MAIN: AREA CHAT --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col overflow-hidden">

                {{-- Header Chat --}}
                <div class="p-4 border-b border-gray-100 flex items-center gap-3" id="chat-header">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800" id="chat-name">Pilih chat</p>
                        <p class="text-xs text-gray-400" id="chat-sub">Pilih penyewa dari daftar kiri</p>
                    </div>
                </div>

                {{-- Pesan --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" id="messages-container">
                    <div class="text-center text-gray-400 text-sm" id="empty-messages">
                        Pilih chat untuk melihat pesan
                    </div>
                </div>

                {{-- Input Pesan --}}
                <div class="p-4 border-t border-gray-100 bg-white">
                    <div class="flex gap-2">
                        <input
                            type="text"
                            id="message-input"
                            placeholder="Tulis pesan..."
                            class="flex-1 border border-gray-300 rounded-full px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            disabled
                        />
                        <button
                            id="send-btn"
                            onclick="sendMessage()"
                            disabled
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-full w-10 h-10 flex items-center justify-center transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Firebase SDK --}}
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.0/firebase-app.js";
        import { getDatabase, ref, onValue, push, set, query, orderByChild } from "https://www.gstatic.com/firebasejs/10.7.0/firebase-database.js";

        const firebaseConfig = {
            apiKey: "AIzaSyDAu-j9iXGfr2NoToV9zJqImIaAnQNg84A",
            authDomain: "lapanginaja.firebaseapp.com",
            databaseURL: "https://lapanginaja-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "lapanginaja",
            storageBucket: "lapanginaja.firebasestorage.app",
            messagingSenderId: "456444985147",
            appId: "1:456444985147:android:54aa3fd5b278b6dc1a011e"
        };

        const app = initializeApp(firebaseConfig);
        const db = getDatabase(app);

        // ID mitra yang sedang login (dari Laravel)
        const mitraId = "{{ Auth::id() }}";
        let activeUserId = null;
        let activeChatId = null;
        let activeListener = null;

        // -------------------------------------------------------
        // LOAD daftar chat dari Firebase
        // Cari semua chat yang path-nya mengandung mitraId
        // -------------------------------------------------------
        const chatsRef = ref(db, 'chats');
        onValue(chatsRef, (snapshot) => {
            const chatList = document.getElementById('chat-list');
            const emptyList = document.getElementById('empty-list');
            const data = snapshot.val();

            if (!data) {
                emptyList.style.display = 'block';
                chatList.innerHTML = '<div class="p-4 text-center text-gray-400 text-sm">Belum ada chat masuk</div>';
                return;
            }

            // Filter hanya chat yang melibatkan mitra ini
            // Format chatId: {userId}_{mitraId}
            const mitraChats = Object.keys(data).filter(chatId => chatId.endsWith('_' + mitraId));

            if (mitraChats.length === 0) {
                chatList.innerHTML = '<div class="p-4 text-center text-gray-400 text-sm">Belum ada chat masuk</div>';
                return;
            }

            chatList.innerHTML = '';
            mitraChats.forEach(chatId => {
                const userId = chatId.replace('_' + mitraId, '');
                const messages = data[chatId]?.messages;
                let lastMsg = 'Belum ada pesan';
                if (messages) {
                    const msgList = Object.values(messages);
                    lastMsg = msgList[msgList.length - 1]?.text ?? lastMsg;
                }

                const item = document.createElement('div');
                item.className = `p-4 border-b border-gray-100 cursor-pointer hover:bg-blue-50 transition-colors ${activeChatId === chatId ? 'bg-blue-50 border-l-4 border-l-blue-600' : ''}`;
                item.onclick = () => openChat(userId, chatId, item);
                item.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800 text-sm">Penyewa #${userId}</p>
                            <p class="text-xs text-gray-400 truncate">${lastMsg}</p>
                        </div>
                    </div>
                `;
                chatList.appendChild(item);
            });
        });

        // -------------------------------------------------------
        // BUKA chat dengan penyewa tertentu
        // -------------------------------------------------------
        window.openChat = function(userId, chatId, element) {
            activeUserId = userId;
            activeChatId = chatId;

            // Update header
            document.getElementById('chat-name').textContent = `Penyewa #${userId}`;
            document.getElementById('chat-sub').textContent = 'Aktif';
            document.getElementById('message-input').disabled = false;
            document.getElementById('send-btn').disabled = false;

            // Highlight item
            document.querySelectorAll('#chat-list > div').forEach(el => {
                el.classList.remove('bg-blue-50', 'border-l-4', 'border-l-blue-600');
            });
            element.classList.add('bg-blue-50', 'border-l-4', 'border-l-blue-600');

            // Listen pesan
            const messagesRef = ref(db, `chats/${chatId}/messages`);
            onValue(messagesRef, (snapshot) => {
                const container = document.getElementById('messages-container');
                const data = snapshot.val();

                if (!data) {
                    container.innerHTML = '<div class="text-center text-gray-400 text-sm">Belum ada pesan</div>';
                    return;
                }

                const messages = Object.values(data).sort((a, b) => a.time.localeCompare(b.time));
                container.innerHTML = '';

                messages.forEach(msg => {
                    const isMitra = msg.senderId == mitraId;
                    const time = new Date(msg.time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                    const bubble = document.createElement('div');
                    bubble.className = `flex ${isMitra ? 'justify-end' : 'justify-start'}`;
                    bubble.innerHTML = `
                        <div class="max-w-xs lg:max-w-md px-4 py-2.5 rounded-2xl ${isMitra
                            ? 'bg-blue-600 text-white rounded-br-none'
                            : 'bg-white text-gray-800 rounded-bl-none shadow-sm border border-gray-100'}">
                            <p class="text-sm">${msg.text}</p>
                            <p class="text-xs mt-1 ${isMitra ? 'text-blue-200' : 'text-gray-400'} text-right">${time}</p>
                        </div>
                    `;
                    container.appendChild(bubble);
                });

                // Scroll ke bawah
                container.scrollTop = container.scrollHeight;
            });
        }

        // -------------------------------------------------------
        // KIRIM pesan dari mitra
        // -------------------------------------------------------
        window.sendMessage = function() {
            const input = document.getElementById('message-input');
            const text = input.value.trim();
            if (!text || !activeChatId) return;

            const messagesRef = ref(db, `chats/${activeChatId}/messages`);
            push(messagesRef, {
                text: text,
                senderId: mitraId,
                time: new Date().toISOString(),
            });

            input.value = '';
        }

        // Enter untuk kirim
        document.getElementById('message-input').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    </script>
</x-app-layout>