<script setup lang="js">
import { ref, watch, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import QuizPreGame from '../components/QuizPreGame.vue';
import QuizInGame from '../components/QuizInGame.vue';

let isGameOngoing = false;

const props = defineProps({
  quizRoom: Object
});

</script>
<template>
  <div>
    <QuizPreGame v-if="!isGameOngoing" :quizRoom="quizRoom" :playersInRoom="playersInRoom" />
    <QuizInGame v-else />
  </div>
</template>
  