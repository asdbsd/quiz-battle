<script setup lang="js">
import { ref, nextTick, watch } from 'vue'
import { SendIcon } from 'lucide-vue-next'

const { quizRoom, roomTeams, players } = defineProps({
    'quizRoom': Object,
    'roomTeams': Array
})
console.log(quizRoom);

// Chat state
const selectedPlayer = ref(null)
const newMessage = ref('')
const messages = ref([])
const messagesContainer = ref(null)

// Select a player to chat with
const selectPlayer = (player) => {
  selectedPlayer.value = player
  
  // Load conversation history (simulated)
  if (player.id === 1) {
    messages.value = [
      { sender: 'other', content: 'Hey there!', time: '10:30 AM' },
      { sender: 'me', content: 'Hi John, how are you?', time: '10:31 AM' },
      { sender: 'other', content: 'I\'m doing great! Just working on the new feature.', time: '10:32 AM' }
    ]
  } else if (player.id === 4) {
    messages.value = [
      { sender: 'other', content: 'Did you see the new design?', time: '9:15 AM' },
      { sender: 'me', content: 'Yes, it looks amazing!', time: '9:20 AM' }
    ]
  } else {
    messages.value = []
  }
  
  // Scroll to bottom of messages
  scrollToBottom()
}

// Send a new message
const sendMessage = () => {
  if (!selectedPlayer.value || !newMessage.value.trim()) return
  
  const now = new Date()
  const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  
  messages.value.push({
    sender: 'me',
    content: newMessage.value,
    time: timeString
  })
  
  newMessage.value = ''
  
  // Simulate response after a short delay
  setTimeout(() => {
    messages.value.push({
      sender: 'other',
      content: `This is a response from ${selectedPlayer.value.name}`,
      time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    })
    
    scrollToBottom()
  }, 1000)
  
  // Scroll to bottom of messages
  scrollToBottom()
}

// Scroll to the bottom of the messages container
const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

// Watch for changes in messages and scroll to bottom
watch(messages, () => {
  scrollToBottom()
}, { deep: true })
</script>


<template>
  <div class="flex h-screen w-full overflow-hidden bg-background text-foreground">
    <!-- Left sidebar - Teams and Players (1/6 width) -->
    <div class="w-1/6 border-r border-border overflow-y-auto">
      <div class="p-4">
        <h2 class="text-lg font-semibold mb-4">Teams</h2>
        
        <!-- Teams List -->
        <div class="space-y-4">
          <div v-for="team in roomTeams" :key="team.id" class="space-y-2">
            <div 
              class="flex items-center cursor-pointer hover:bg-muted rounded-md p-2"
            >
              <span class="font-medium">{{ team.name }}</span>
            </div>
            
            <!-- Players List -->
            <div 
            v-for="player in team.players" 
            :key="player.id"
            class="flex items-center p-2 hover:bg-muted rounded-md cursor-pointer"
            @click="selectPlayer(player)"
            >
            <div 
                class="h-2 w-2 rounded-full mr-2" 
                :class="player.online ? 'bg-green-500' : 'bg-red-500'"
            ></div>
            <span>{{ player.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Right side - Chat Interface (5/6 width) -->
    <div class="flex-1 flex flex-col">
      <!-- Chat header -->
      <div class="border-b border-border p-4">
        <h2 class="text-lg font-semibold">
          {{ quizRoom.name }}
        </h2>
      </div>
      
      <!-- Messages area -->
      <div class="flex-1 overflow-y-auto p-4 space-y-4" ref="messagesContainer">
        <div v-if="!selectedPlayer" class="flex h-full items-center justify-center text-muted-foreground">
          Waiting for the game to start...
        </div>
        
        <template v-else>
          <div 
            v-for="(message, index) in messages" 
            :key="index"
            class="flex flex-col max-w-[80%] rounded-lg p-3 mb-2"
            :class="[
              message.sender === 'me' 
                ? 'ml-auto bg-primary text-primary-foreground' 
                : 'bg-muted text-muted-foreground'
            ]"
          >
            <span class="text-sm">{{ message.content }}</span>
            <span class="text-xs opacity-70 mt-1">{{ message.time }}</span>
          </div>
        </template>
      </div>
      
      <!-- Message input -->
      <div class="border-t border-border p-4">
        <div class="flex items-center space-x-2">
          <input
            v-model="newMessage"
            type="text"
            placeholder="Type your message..."
            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            @keyup.enter="sendMessage"
            :disabled="!selectedPlayer"
          />
          <button 
            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
            @click="sendMessage"
            :disabled="!selectedPlayer || !newMessage.trim()"
          >
            <SendIcon class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>