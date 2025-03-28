<script setup lang="ts">
import { ref, watch, defineProps, computed } from 'vue';
import { router } from '@inertiajs/vue3';

interface Player {
  id: number;
  name: string;
}

interface Question {
  id: number;
  question: string;
  options: string[];
  order: number;
}

interface QuizRoom {
  id: number;
  name: string;
  status: number;
  allowed_players_count: number;
  questions_count: number;
}

const props = defineProps<{
  quizRoom: QuizRoom;
  players: Player[];
  currentQuestion: Question | null;
}>();

const selectedAnswer = ref<string | null>(null);
const feedback = ref<{ correct: boolean; points: number } | null>(null);
const isSubmitting = ref(false);

const canStartGame = computed(() => {
  return props.players.length >= 2 && props.quizRoom.status === 0;
});

async function startGame() {
  try {
    const response = await fetch(`/quiz/${props.quizRoom.id}/start`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    });

    if (!response.ok) {
      throw new Error('Failed to start game');
    }

    const data = await response.json();
    router.reload();
  } catch (error) {
    console.error('Error starting game:', error);
  }
}

async function submitAnswer() {
  if (!selectedAnswer || !props.currentQuestion || isSubmitting.value) return;

  isSubmitting.value = true;
  feedback.value = null;

  try {
    const response = await fetch(`/quiz/${props.quizRoom.id}/questions/${props.currentQuestion.id}/answer`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({ answer: selectedAnswer })
    });

    if (!response.ok) {
      throw new Error('Failed to submit answer');
    }

    const data = await response.json();
    feedback.value = { correct: data.correct, points: data.points };

    if (data.nextQuestion) {
      setTimeout(() => {
        router.reload();
      }, 2000);
    } else {
      // Game completed
      setTimeout(() => {
        router.reload();
      }, 2000);
    }
  } catch (error) {
    console.error('Error submitting answer:', error);
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto p-6">
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
      <h1 class="text-2xl font-bold mb-4">{{ quizRoom.name }}</h1>

      <!-- Players Section -->
      <div class="mb-6">
        <h2 class="text-xl font-semibold mb-3">Players</h2>
        <div class="flex flex-wrap gap-2">
          <div
            v-for="player in players"
            :key="player.id"
            class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full"
          >
            {{ player.name }}
          </div>
        </div>
      </div>

      <!-- Waiting State -->
      <div v-if="quizRoom.status === 0" class="text-center">
        <p class="text-gray-600 mb-4">
          Waiting for {{ quizRoom.allowed_players_count - players.length }} more
          players to join...
        </p>
        <button
          @click="startGame"
          :disabled="!canStartGame"
          class="bg-blue-500 text-white px-6 py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Start Game
        </button>
      </div>

      <!-- Question Display -->
      <div v-else-if="quizRoom.status === 1 && currentQuestion" class="mt-6">
        <div class="bg-gray-50 p-6 rounded-lg">
          <h3 class="text-xl font-semibold mb-4">{{ currentQuestion.question }}</h3>
          <div class="space-y-3">
            <label
              v-for="option in currentQuestion.options"
              :key="option"
              class="block"
            >
              <input
                type="radio"
                :value="option"
                v-model="selectedAnswer"
                :disabled="isSubmitting || feedback"
                class="mr-2"
              />
              {{ option }}
            </label>
          </div>

          <!-- Answer Feedback -->
          <div v-if="feedback" class="mt-4">
            <p
              :class="{
                'text-green-600': feedback.correct,
                'text-red-600': !feedback.correct,
              }"
            >
              {{ feedback.correct ? 'Correct!' : 'Incorrect!' }}
              {{ feedback.correct ? `+${feedback.points} points` : '' }}
            </p>
          </div>

          <button
            @click="submitAnswer"
            :disabled="!selectedAnswer || isSubmitting || feedback"
            class="mt-4 bg-blue-500 text-white px-6 py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isSubmitting ? 'Submitting...' : 'Submit Answer' }}
          </button>
        </div>
      </div>

      <!-- Game Completed -->
      <div v-else-if="quizRoom.status === 2" class="text-center">
        <h3 class="text-xl font-semibold">Game Completed!</h3>
      </div>
    </div>
  </div>
</template>
  