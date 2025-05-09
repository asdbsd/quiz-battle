<script setup lang="js">
import { ref, watch, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const { user } = usePage().props.auth;

const { quizRoom } = defineProps({
  quizRoom: Object
});

const playersInRoom = ref([]);
const currentUserId = user.id;

const canGameStart = computed(() => {
  return playersInRoom.value.length >= quizRoom.allowed_players_count && playersInRoom.value.every(p => p.is_ready);
})

const channel = Echo.join(`quizRooms.${quizRoom.id}`)
  .here((users) => playersInRoom.value = users.map(obj => {
    obj.user.is_ready = false;
    return obj.user;
  }))
  .joining((e) => {
    e.user.is_ready = false;
    playersInRoom.value.push(e.user);
  })
  .leaving((e) => {
    playersInRoom.value = playersInRoom.value.filter(p => p.id !== e.user.id);
  })
  .listenForWhisper('PlayerChangedStatus', (data) => {
    playersInRoom.value.find(p => p.id === data.user_id).is_ready = data.is_ready;
  });

function toggleReady(index) {
  playersInRoom.value[index].is_ready = !playersInRoom.value[index].is_ready;
  updateOpponent();
}

function updateOpponent() {
  channel.whisper('PlayerChangedStatus', {
    user_id: currentUserId,
    is_ready: playersInRoom.value.find(p => p.id === currentUserId).is_ready
  })
}
</script>
<template>
      <div class="max-w-4xl mx-auto p-6">
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
      <!-- Room Name -->
      <h1 class="text-2xl font-bold mb-8 w-full text-center">{{ quizRoom.name }}</h1>

      <!-- Two-column layout for all other content -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Players Section -->
        <div>
          <h2 class="text-xl font-semibold mb-3">Players</h2>
          <div class="flex flex-col gap-2">
            <div
              v-for="(player, index) in playersInRoom"
              :key="player.id"
              class="flex items-center gap-1"
            >
              <span class="text-blue-800 font-medium text-xl">{{ player.name }}</span>
              <button
                @click="toggleReady(index)"
                :disabled="player.id !== currentUserId"
                :class="[
                  'flex items-center justify-center p-1 rounded border-2 transition-colors',
                  player.is_ready ? 'bg-green-500' : 'bg-gray-100',
                  player.id !== currentUserId ? 'cursor-not-allowed opacity-60' : ''
                ]"
                :aria-pressed="player.is_ready"
                :title="player.is_ready ? 'Ready' : 'Not Ready'"
                type="button"
              >
                <div v-if="player.is_ready" class="inline">
                  Ready
                </div>
                <div v-else class="inline">
                  Not Ready
                </div>
              </button>
            </div>
            <div v-if="playersInRoom.length < quizRoom.allowed_players_count" class="text-center">
              <p class="text-gray-600 mb-4">
                Waiting for {{ quizRoom.allowed_players_count - playersInRoom.length }} more
                players to join...
              </p>
            </div>
            <button
              :disabled="!canGameStart"
              class="bg-blue-500 text-white px-6 py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Start Game
            </button>
          </div>
        </div>

        <!-- Right column: Waiting state and Start Game button -->
        <div class="flex flex-col justify-start items-center gap-6">

        </div>
      </div>
    </div>
  </div>
</template>