<!-- AI Chat Bubble -->
<div id="ai-chat-bubble" class="fixed bottom-6 right-6 z-50">
    <button id="ai-chat-toggle" class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center text-white hover:scale-110">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
    </button>
</div>

<!-- AI Chat Window -->
<div id="ai-chat-window" class="fixed bottom-24 right-6 w-80 h-96 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 hidden z-50">
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364-.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">AI Learning Assistant</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Ask me anything!</p>
            </div>
        </div>
        <button id="ai-chat-close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    
    <div id="ai-chat-messages" class="flex-1 p-4 h-64 overflow-y-auto space-y-3">
        <div class="flex items-start space-x-2">
            <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364-.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 max-w-xs">
                <p class="text-sm text-gray-800 dark:text-gray-200">Hi! I'm your AI learning assistant. I can help clarify concepts, explain topics, and answer questions about your courses. What would you like to know?</p>
            </div>
        </div>
    </div>
    
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex space-x-2">
            <input type="text" id="ai-chat-input" placeholder="Ask a question..." 
                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-sm">
            <button id="ai-chat-send" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#ai-chat-toggle').on('click', function() {
        $('#ai-chat-window').toggleClass('hidden');
    });
    
    $('#ai-chat-close').on('click', function() {
        $('#ai-chat-window').addClass('hidden');
    });
    
    $('#ai-chat-send, #ai-chat-input').on('click keypress', function(e) {
        if (e.type === 'click' || e.which === 13) {
            const message = $('#ai-chat-input').val().trim();
            if (!message) return;
            
            // Add user message
            $('#ai-chat-messages').append(`
                <div class="flex items-start space-x-2 justify-end">
                    <div class="bg-blue-600 text-white rounded-lg p-3 max-w-xs">
                        <p class="text-sm">${message}</p>
                    </div>
                    <div class="w-6 h-6 bg-gray-400 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs">U</span>
                    </div>
                </div>
            `);
            
            $('#ai-chat-input').val('');
            
            // Simulate AI response
            setTimeout(() => {
                const responses = [
                    "That's a great question! Let me help clarify that concept for you.",
                    "Based on the course material, here's what you need to know:",
                    "I understand your confusion. Let me break this down step by step:",
                    "This is a common question. The key point to remember is:",
                    "Great question! This concept is fundamental to understanding the topic."
                ];
                
                const randomResponse = responses[Math.floor(Math.random() * responses.length)];
                
                $('#ai-chat-messages').append(`
                    <div class="flex items-start space-x-2">
                        <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364-.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 max-w-xs">
                            <p class="text-sm text-gray-800 dark:text-gray-200">${randomResponse}</p>
                        </div>
                    </div>
                `);
                
                $('#ai-chat-messages').scrollTop($('#ai-chat-messages')[0].scrollHeight);
            }, 1000);
            
            $('#ai-chat-messages').scrollTop($('#ai-chat-messages')[0].scrollHeight);
        }
    });
});
</script>
@endpush