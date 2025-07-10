<template>
  <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Sidebar -->
    <div class="w-64 bg-white dark:bg-gray-800 shadow-lg flex flex-col border-r border-gray-200 dark:border-gray-700">
      <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Chat AI</h1>
      </div>
      
      <!-- New conversation button -->
      <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <button
          @click="createNewConversation"
          class="w-full bg-blue-600 dark:bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors"
        >
          + New Conversation
        </button>
      </div>
      
      <!-- Conversations list -->
      <div class="flex-1 overflow-y-auto">
        <div v-for="conversation in conversations" :key="conversation.id" class="p-2">
          <div
            @click="selectConversation(conversation)"
            :class="[
              'p-3 rounded-lg cursor-pointer transition-colors',
              selectedConversation?.id === conversation.id 
                ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-900 dark:text-blue-100' 
                : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100'
            ]"
          >
            <div class="font-medium text-sm truncate">
              {{ conversation.title }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ formatDate(conversation.updated_at) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main chat area -->
    <div class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-900">
      <div v-if="!selectedConversation" class="flex-1 flex items-center justify-center">
        <div class="text-center">
          <h2 class="text-2xl font-bold text-gray-600 dark:text-gray-300 mb-4">Welcome to Chat AI</h2>
          <p class="text-gray-500 dark:text-gray-400 mb-6">Select a conversation or create a new one to get started</p>
          <button
            @click="createNewConversation"
            class="bg-blue-600 dark:bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors"
          >
            Start New Conversation
          </button>
        </div>
      </div>
      
      <div v-else class="flex-1 flex flex-col h-full">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between flex-shrink-0">
          <div class="flex items-center">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ selectedConversation.title }}</h2>
            <span class="ml-3 text-sm text-gray-500 dark:text-gray-400">{{ selectedConversation.provider }} - {{ getModelName(selectedConversation.model) }}</span>
          </div>
          <div class="flex items-center space-x-2">
            <button
              @click="showModelSelector = true"
              class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
              title="Change model"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </button>
            <button
              @click="deleteConversation"
              class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20"
              title="Delete conversation"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Messages - This is the scrollable container -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50 dark:bg-gray-900 min-h-0" ref="messagesContainer">
          <div
            v-for="message in messages"
            :key="message.id"
            :class="[
              'flex',
              message.role === 'user' ? 'justify-end' : 'justify-start'
            ]"
          >
            <div
              :class="[
                'max-w-3xl p-4 rounded-lg',
                message.role === 'user' 
                  ? 'bg-blue-600 dark:bg-blue-500 text-white' 
                  : 'bg-white dark:bg-gray-800 shadow-md text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700'
              ]"
            >
              <div class="whitespace-pre-wrap">{{ message.content }}</div>
              <div class="text-xs mt-2 opacity-70">
                {{ formatTime(message.created_at) }}
              </div>
            </div>
          </div>

          <!-- Streaming message -->
          <div v-if="isStreaming" class="flex justify-start">
            <div class="max-w-3xl p-4 rounded-lg bg-white dark:bg-gray-800 shadow-md text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700">
              <div class="whitespace-pre-wrap">{{ streamingMessage }}</div>
              <div class="flex items-center mt-2">
                <div class="animate-pulse w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full mr-1"></div>
                <div class="animate-pulse w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full mr-1" style="animation-delay: 0.2s"></div>
                <div class="animate-pulse w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full" style="animation-delay: 0.4s"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Input area - Fixed at the bottom -->
        <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 flex-shrink-0">
          <div class="flex space-x-4">
            <div class="flex-1">
              <textarea
                v-model="newMessage"
                @keydown.enter.prevent="sendMessage"
                placeholder="Type your message..."
                class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                rows="3"
                :disabled="isStreaming"
                ref="messageInput"
              ></textarea>
            </div>
            <button
              @click="sendMessage"
              :disabled="!newMessage.trim() || isStreaming"
              class="bg-blue-600 dark:bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Model selector modal -->
    <div v-if="showModelSelector" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-md w-full mx-4 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Choose Model</h3>
        <div class="space-y-4">
          <div v-for="providerGroup in availableModels" :key="providerGroup.provider">
            <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2 capitalize">{{ providerGroup.provider }}</h4>
            <div class="space-y-2">
              <label
                v-for="model in providerGroup.models"
                :key="model.id"
                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
              >
                <input
                  type="radio"
                  :value="{ provider: providerGroup.provider, model: model.id }"
                  v-model="selectedModel"
                  class="mr-3 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400"
                />
                <div>
                  <div class="font-medium text-gray-900 dark:text-gray-100">{{ model.name }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ model.id }}</div>
                </div>
              </label>
            </div>
          </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
          <button
            @click="showModelSelector = false"
            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"
          >
            Cancel
          </button>
          <button
            @click="changeModel"
            class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600"
          >
            Change Model
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'
import axios from 'axios'

const conversations = ref([])
const selectedConversation = ref(null)
const messages = ref([])
const newMessage = ref('')
const isStreaming = ref(false)
const streamingMessage = ref('')
const messagesContainer = ref(null)
const messageInput = ref(null)
const showModelSelector = ref(false)
const availableModels = ref([])
const selectedModel = ref({ provider: 'anthropic', model: 'claude-3-5-sonnet-20241022' })

// Load data on mount
onMounted(async () => {
  await loadConversations()
  await loadAvailableModels()
})

// Watch for conversation changes - messages are already loaded from selectConversation
// No need to reload messages here since they come with the conversation data

// Watch messages for auto-scroll
watch(messages, () => {
  nextTick(() => {
    scrollToBottom()
  })
}, { deep: true })

// Watch streaming message for auto-scroll
watch(streamingMessage, () => {
  nextTick(() => {
    scrollToBottom()
  })
})

const loadConversations = async () => {
  try {
    const response = await axios.get('/api/conversations')
    conversations.value = response.data
  } catch (error) {
    console.error('Error loading conversations:', error)
  }
}

const loadAvailableModels = async () => {
  try {
    const response = await axios.get('/api/models')
    availableModels.value = response.data.models
  } catch (error) {
    console.error('Error loading models:', error)
  }
}

const loadMessages = async (conversationId) => {
  try {
    const response = await axios.get(`/api/conversations/${conversationId}`)
    messages.value = response.data.messages
  } catch (error) {
    console.error('Error loading messages:', error)
  }
}

const selectConversation = (conversation) => {
  selectedConversation.value = conversation
  // Set messages from the conversation data (already loaded with messages)
  messages.value = conversation.messages || []
}

const createNewConversation = async () => {
  try {
    const response = await axios.post('/api/conversations', {
      title: 'New Conversation',
      model: selectedModel.value.model,
      provider: selectedModel.value.provider
    })
    
    const newConversation = response.data
    conversations.value.unshift(newConversation)
    selectedConversation.value = newConversation
    messages.value = []
  } catch (error) {
    console.error('Error creating conversation:', error)
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedConversation.value || isStreaming.value) return

  const messageContent = newMessage.value.trim()
  newMessage.value = ''

  // Add user message to UI immediately
  const userMessage = {
    id: Date.now(),
    role: 'user',
    content: messageContent,
    created_at: new Date().toISOString()
  }
  messages.value.push(userMessage)

  // Start streaming
  isStreaming.value = true
  streamingMessage.value = ''

  try {
    // Get CSRF token safely
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    
    const response = await fetch(`/api/conversations/${selectedConversation.value.id}/messages`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'text/event-stream',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ content: messageContent })
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const reader = response.body.getReader()
    const decoder = new TextDecoder()

    while (true) {
      const { done, value } = await reader.read()
      if (done) break

      const chunk = decoder.decode(value)
      const lines = chunk.split('\n')

      for (const line of lines) {
        if (line.startsWith('data: ')) {
          try {
            const data = JSON.parse(line.slice(6))
            
            if (data.type === 'chunk') {
              streamingMessage.value += data.content
            } else if (data.type === 'end') {
              // Add final assistant message to messages array
              const assistantMessage = {
                id: Date.now() + 1,
                role: 'assistant',
                content: streamingMessage.value,
                created_at: new Date().toISOString()
              }
              messages.value.push(assistantMessage)
              streamingMessage.value = ''
              isStreaming.value = false
            } else if (data.type === 'error') {
              console.error('Streaming error:', data.error)
              isStreaming.value = false
              streamingMessage.value = ''
            }
          } catch (e) {
            // Ignore malformed JSON
          }
        }
      }
    }
  } catch (error) {
    console.error('Error sending message:', error)
    isStreaming.value = false
    streamingMessage.value = ''
    
    // Show error message to user
    const errorMessage = {
      id: Date.now() + 1,
      role: 'assistant',
      content: 'Sorry, there was an error processing your message. Please try again.',
      created_at: new Date().toISOString()
    }
    messages.value.push(errorMessage)
  }

  // Update conversation timestamp only (don't reload messages to avoid duplicates)
  await loadConversations()
}

const changeModel = async () => {
  if (!selectedConversation.value) return

  try {
    await axios.put(`/api/conversations/${selectedConversation.value.id}`, {
      title: selectedConversation.value.title,
      model: selectedModel.value.model,
      provider: selectedModel.value.provider
    })
    
    selectedConversation.value.model = selectedModel.value.model
    selectedConversation.value.provider = selectedModel.value.provider
    showModelSelector.value = false
    
    // Refresh conversations list
    await loadConversations()
  } catch (error) {
    console.error('Error changing model:', error)
  }
}

const deleteConversation = async () => {
  if (!selectedConversation.value) return
  
  if (!confirm('Are you sure you want to delete this conversation?')) return

  try {
    await axios.delete(`/api/conversations/${selectedConversation.value.id}`)
    
    // Remove from conversations list
    conversations.value = conversations.value.filter(c => c.id !== selectedConversation.value.id)
    
    // Clear selection
    selectedConversation.value = null
    messages.value = []
  } catch (error) {
    console.error('Error deleting conversation:', error)
  }
}

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getModelName = (modelId) => {
  for (const providerGroup of availableModels.value) {
    const model = providerGroup.models.find(m => m.id === modelId)
    if (model) return model.name
  }
  return modelId
}
</script>