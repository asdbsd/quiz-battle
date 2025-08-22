<script setup lang="js">
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import QuizPreGame from '../components/QuizPreGame.vue';
import QuizInGame from '../components/QuizInGame.vue';

const props = defineProps({
  quizRoom: Object,
  // roomTeams: Array,
  // players: Array,
  // playerRoles: Array,
  // roomStatuses: Array
});

const isGameOngoing = computed(() => {
  return props.quizRoom.status === 'In Progress';
});


</script>
<template>
  <AppLayout>
      <QuizPreGame ref="quizPreGame" v-if="!isGameOngoing" :quiz-room="props.quizRoom" />
      <QuizInGame v-else :quiz-room="quizRoom"  /> 
  </AppLayout>
</template>
  