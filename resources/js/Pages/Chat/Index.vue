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
      <div class="flex-1 overflow-y-auto custom-scrollbar p-2">
        <div v-for="conversation in conversations" :key="conversation.id" class="mb-1">
          <div
            @click="selectConversation(conversation)"
            :class="[
              'p-2 rounded-lg cursor-pointer transition-colors',
              selectedConversation?.id === conversation.id 
                ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-900 dark:text-blue-100' 
                : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100'
            ]"
          >
            <div class="font-medium text-sm truncate">
              {{ conversation.title }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
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
            <span v-if="selectedConversation.profile" class="ml-3 px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
              {{ selectedConversation.profile.full_name || selectedConversation.profile.name }}
            </span>
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
        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50 dark:bg-gray-900 min-h-0 custom-scrollbar" ref="messagesContainer">
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
                'max-w-3xl rounded-lg',
                message.role === 'user' 
                  ? 'bg-blue-600 dark:bg-blue-500 text-white px-3 py-2' 
                  : 'bg-white dark:bg-gray-800 shadow-md text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700 p-3'
              ]"
            >
              <div class="whitespace-pre-wrap break-words">{{ message.content }}</div>
              
              <!-- Display attachments -->
              <div v-if="message.attachments && message.attachments.length > 0" class="mt-2 space-y-2">
                <div v-for="attachment in message.attachments" :key="attachment.id || attachment.url" 
                     :class="[
                       'rounded overflow-hidden',
                       message.role === 'user' ? 'bg-blue-700 dark:bg-blue-600' : 'bg-gray-100 dark:bg-gray-700'
                     ]"
                >
                  <!-- Image attachments -->
                  <img 
                    v-if="attachment.is_image || (attachment.mime_type && attachment.mime_type.startsWith('image/'))"
                    :src="attachment.url || attachment.public_url"
                    :alt="attachment.original_name || attachment.filename"
                    class="max-w-full h-auto rounded"
                  />
                  
                  <!-- Other file attachments -->
                  <div v-else 
                       :class="[
                         'p-2 flex items-center space-x-2',
                         message.role === 'user' ? 'text-white' : 'text-gray-700 dark:text-gray-300'
                       ]"
                  >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-sm truncate">{{ attachment.original_name || attachment.filename }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Streaming message -->
          <div v-if="isStreaming" class="flex justify-start">
            <div class="max-w-3xl p-3 rounded-lg bg-white dark:bg-gray-800 shadow-md text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700">
              <div class="whitespace-pre-wrap break-words">{{ streamingMessage }}</div>
              <div class="flex items-center mt-2" v-if="!streamingMessage">
                <div class="animate-pulse w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full mr-1"></div>
                <div class="animate-pulse w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full mr-1" style="animation-delay: 0.2s"></div>
                <div class="animate-pulse w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full" style="animation-delay: 0.4s"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Input area - Fixed at the bottom -->
        <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 flex-shrink-0">
          <!-- File attachments preview -->
          <div v-if="attachments.length > 0" class="mb-3 flex flex-wrap gap-2">
            <div
              v-for="(attachment, index) in attachments"
              :key="index"
              class="relative inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg"
            >
              <svg v-if="attachment.type.startsWith('image/')" class="w-4 h-4 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <svg v-else class="w-4 h-4 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <span class="text-sm text-gray-700 dark:text-gray-300">{{ attachment.name }}</span>
              <button
                @click="removeAttachment(index)"
                class="ml-2 text-gray-500 hover:text-red-600"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>
          
          <div 
            @drop.prevent="handleDrop"
            @dragover.prevent
            @dragenter.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            :class="[
              'flex space-x-4 transition-colors duration-200',
              isDragging ? 'bg-blue-50 dark:bg-blue-900/20 border-2 border-dashed border-blue-500 rounded-lg p-2' : ''
            ]"
          >
            <div class="flex-1 relative">
              <textarea
                v-model="newMessage"
                @keydown.enter.prevent="sendMessage"
                placeholder="Type your message or drag files here..."
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                rows="3"
                :disabled="isStreaming"
                ref="messageInput"
              ></textarea>
              <!-- File input -->
              <input
                type="file"
                ref="fileInput"
                @change="handleFileSelect"
                multiple
                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.md,.markdown"
                class="hidden"
              />
              <button
                @click="$refs.fileInput.click()"
                class="absolute bottom-2 right-2 p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                :disabled="isStreaming"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
              </button>
            </div>
            <button
              @click="sendMessage"
              :disabled="(!newMessage.trim() && attachments.length === 0) || isStreaming"
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

    <!-- Profile selector modal -->
    <div v-if="showProfileSelector" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-2xl w-full mx-4 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Choose Personality Profile (Optional)</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
          Select a personality profile to make the AI impersonate that person, or start without a profile for a standard conversation.
        </p>
        
        <!-- No profile option -->
        <div class="mb-6">
          <label class="flex items-center p-4 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-500 cursor-pointer transition-colors">
            <input
              type="radio"
              :value="null"
              v-model="selectedProfile"
              class="mr-3 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400"
            />
            <div class="flex-1">
              <div class="font-medium text-gray-900 dark:text-gray-100">No Profile - Standard Conversation</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Start a regular conversation without any specific personality</div>
            </div>
          </label>
        </div>

        <!-- Available profiles -->
        <div v-if="availableProfiles.length > 0" class="space-y-3">
          <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Available Profiles</h4>
          <div class="max-h-96 overflow-y-auto space-y-2">
            <label
              v-for="profile in availableProfiles"
              :key="profile.id"
              class="flex items-center p-4 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-500 cursor-pointer transition-colors"
            >
              <input
                type="radio"
                :value="profile.id"
                v-model="selectedProfile"
                class="mr-3 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400"
              />
              <div class="flex-1">
                <div class="font-medium text-gray-900 dark:text-gray-100">{{ profile.name }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Personality profile for impersonation</div>
              </div>
            </label>
          </div>
        </div>

        <div v-else class="text-center py-8">
          <div class="text-gray-500 dark:text-gray-400 mb-2">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
          <p class="text-gray-600 dark:text-gray-400">No personality profiles available</p>
          <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Create a profile first to use this feature</p>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
          <button
            @click="showProfileSelector = false"
            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"
          >
            Cancel
          </button>
          <button
            @click="createConversationWithProfile"
            class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600"
          >
            Start Conversation
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
const showProfileSelector = ref(false)
const availableProfiles = ref([])
const selectedProfile = ref(null)
const attachments = ref([])
const isDragging = ref(false)
const fileInput = ref(null)

// Load data on mount
onMounted(async () => {
  await loadConversations()
  await loadAvailableModels()
  await loadAvailableProfiles()
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

const loadAvailableProfiles = async () => {
  try {
    const response = await axios.get('/api/profiles')
    availableProfiles.value = response.data
  } catch (error) {
    console.error('Error loading profiles:', error)
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

const createNewConversation = () => {
  showProfileSelector.value = true
}

const createConversationWithProfile = async () => {
  try {
    const payload = {
      title: 'New Conversation',
      model: selectedModel.value.model,
      provider: selectedModel.value.provider
    }
    
    if (selectedProfile.value) {
      payload.profile_id = selectedProfile.value
    }
    
    const response = await axios.post('/api/conversations', payload)
    
    const newConversation = response.data
    conversations.value.unshift(newConversation)
    selectedConversation.value = newConversation
    messages.value = []
    showProfileSelector.value = false
    selectedProfile.value = null
  } catch (error) {
    console.error('Error creating conversation:', error)
  }
}

// Reemplaza la función sendMessage con esta versión mejorada

const sendMessage = async () => {
  if ((!newMessage.value.trim() && attachments.value.length === 0) || !selectedConversation.value || isStreaming.value) return

  const messageContent = newMessage.value.trim()
  const messageAttachments = [...attachments.value]
  
  newMessage.value = ''
  attachments.value = []

  // Upload attachments first if any
  let uploadedAttachments = []
  if (messageAttachments.length > 0) {
    try {
      for (const file of messageAttachments) {
        const formData = new FormData()
        formData.append('file', file)
        
        const uploadResponse = await axios.post('/api/upload', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        uploadedAttachments.push(uploadResponse.data)
      }
    } catch (error) {
      console.error('Error uploading files:', error)
      // Show error to user
      return
    }
  }

  // Add user message to UI immediately
  const userMessage = {
    id: Date.now(),
    role: 'user',
    content: messageContent,
    attachments: uploadedAttachments.map(a => ({
      original_name: a.filename,
      mime_type: a.mime_type,
      is_image: a.is_image,
      url: a.url
    })),
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
      body: JSON.stringify({ 
        content: messageContent,
        attachments: uploadedAttachments
      })
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const reader = response.body.getReader()
    const decoder = new TextDecoder()
    let buffer = ''

    while (true) {
      const { done, value } = await reader.read()
      if (done) break

      // Decode chunk and add to buffer
      buffer += decoder.decode(value, { stream: true })
      
      // Process complete lines from buffer
      const lines = buffer.split('\n')
      // Keep the last incomplete line in the buffer
      buffer = lines.pop() || ''

      for (const line of lines) {
        if (line.trim() === '') continue
        
        // Handle ray() interference: remove the extra "data: " prefix
        let processedLine = line
        if (line.startsWith('data: ')) {
          processedLine = line.slice(6) // Remove "data: " prefix
        }
        
        // Skip event: lines - they're not JSON
        if (processedLine.startsWith('event: ')) {
          continue
        }
        
        // Process data: lines (after removing the first "data: " if present)
        if (processedLine.startsWith('data: ')) {
          try {
            const data = JSON.parse(processedLine.slice(6))
            
            if (data.type === 'chunk') {
              streamingMessage.value += data.content
            } else if (data.type === 'end') {
              // Add final assistant message to messages array
              const assistantMessage = {
                id: data.message_id || Date.now() + 1,
                role: 'assistant',
                content: streamingMessage.value,
                created_at: new Date().toISOString()
              }
              messages.value.push(assistantMessage)
              streamingMessage.value = ''
              isStreaming.value = false
              
              // Update conversation title if provided
              if (data.conversation_title && selectedConversation.value) {
                selectedConversation.value.title = data.conversation_title
                // Also update in conversations list
                const conversationIndex = conversations.value.findIndex(c => c.id === selectedConversation.value.id)
                if (conversationIndex !== -1) {
                  conversations.value[conversationIndex].title = data.conversation_title
                }
              }
            } else if (data.type === 'error') {
              console.error('Streaming error:', data.error)
              isStreaming.value = false
              streamingMessage.value = ''
            }
          } catch (e) {
            console.error('Error parsing JSON:', e, 'Line:', line)
          }
        }
      }
    }
    
    // Process any remaining data in buffer
    if (buffer.trim() !== '' && buffer.startsWith('data: ')) {
      try {
        const data = JSON.parse(buffer.slice(6))
        if (data.type === 'chunk') {
          streamingMessage.value += data.content
        }
      } catch (e) {
        console.error('Error parsing final buffer:', e)
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
  // await loadConversations() // COMMENTED OUT - This was causing duplicate messages!
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

const handleDrop = (e) => {
  isDragging.value = false
  const files = Array.from(e.dataTransfer.files)
  handleFiles(files)
}

const handleFileSelect = (e) => {
  const files = Array.from(e.target.files)
  handleFiles(files)
}

const handleFiles = (files) => {
  const allowedTypes = [
    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'text/plain', 'text/csv', 'text/markdown', 'text/x-markdown'
  ]
  
  // También permitir archivos .md que pueden tener MIME type diferente
  const allowedExtensions = ['.md', '.markdown']
  
  files.forEach(file => {
    const fileExtension = '.' + file.name.split('.').pop().toLowerCase()
    const isAllowedType = allowedTypes.includes(file.type)
    const isAllowedExtension = allowedExtensions.includes(fileExtension)
    const isSizeOk = file.size <= 10 * 1024 * 1024 // 10MB
    
    if ((isAllowedType || isAllowedExtension) && isSizeOk) {
      attachments.value.push(file)
    } else {
      console.warn(`File ${file.name} is not allowed or too large. Type: ${file.type}, Size: ${file.size}`)
    }
  })
  
  // Clear file input
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const removeAttachment = (index) => {
  attachments.value.splice(index, 1)
}
</script>

<style scoped>
/* Custom scrollbar styles */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgb(209 213 219) transparent; /* gray-300 */
}

.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgb(209 213 219); /* gray-300 */
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgb(156 163 175); /* gray-400 */
}

/* Dark mode scrollbar */
@media (prefers-color-scheme: dark) {
  .custom-scrollbar {
    scrollbar-color: rgb(75 85 99) transparent; /* gray-600 */
  }
  
  .custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgb(75 85 99); /* gray-600 */
  }
  
  .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgb(107 114 128); /* gray-500 */
  }
}
</style>