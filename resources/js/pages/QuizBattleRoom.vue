<script setup lang="ts">
  import { ref, watch, defineProps, onMounted } from 'vue';

  const props = defineProps({ players: Array});

  onMounted(() => {
    console.log(props)
  })
  interface QuizRoom {
    id: number;
    players: { id: number; name: string }[];
    allowed_players_count: number;
  }

  const quizRoom = ref<QuizRoom>({
    id: 1,
    players: [],
    allowed_players_count: 4,
  });

  watch(
    () => quizRoom.value.players.length,
    (newLength) => {
      if (newLength === quizRoom.value.allowed_players_count) {
        startGame();
      }
    }
  );

  function startGame() {
    // logic to start the game goes here
    console.log('Game started!');
  }
</script>
<template>
  <div>
    <h2>Joined Players:</h2>
    <ul>
      <li v-for="player in quizRoom.players" :key="player.id">{{ player.name }}</li>
    </ul>
    <p v-if="quizRoom.players.length < quizRoom.allowed_players_count">
      Waiting for {{ quizRoom.allowed_players_count - quizRoom.players.length }} more players to join...
    </p>
    <button v-else @click="startGame">Start Game</button>
  </div>
</template>
  