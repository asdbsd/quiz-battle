<script setup lang="js">
import { computed, onUnmounted, ref } from 'vue';
import { useEchoPresence } from "@laravel/echo-vue";
import { usePage, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const { user } = usePage().props.auth;
const awaitingUsers = ref([]);
const dropdownOpen = ref(false);


const { quizRoom } = defineProps({
  quizRoom: Object,
  // roomTeams: Array,
  // players: Array,
  // playerRoles: Array
});

useEchoPresence(
  `quizRooms.${quizRoom.id}`,
  'RoomActiveUsersWereUpdated',
  (event) => {
    // If player has not joined a team in the room add it to the list of awaiting players
    const isPlayerInRoom = quizRoom.players.find(player => player.id === event.user.id);
    if (isPlayerInRoom) return;

    awaitingUsers.value.push(event.user);
  }
);

  const toggleReady = (player) => {
    player.is_ready = !player.is_ready;
  };


// mounted(() => {
//   console.log('Component mounted.');
//   console.log('User: ' + user);
//   console.log('Room: ' + players);
//   console.log('PlayerRoles:')
// })
</script>
<template>
<div class="flex flex-col space-y-6 p-6 bg-gray-50">
  <!-- Awaiting Players -->
  <section class="space-y-4">
    <h2 class="text-xl font-semibold text-gray-900">Awaiting Players</h2>

    <div class="bg-yellow-50 p-4 rounded-2xl border border-yellow-200 shadow-sm">
      <ul class="flex flex-wrap gap-3">
        <li
          v-for="awaitingUser in awaitingUsers"
          :key="awaitingUser.id"
          class="flex items-center space-x-2 bg-white border border-gray-200 rounded-full px-4 py-2 shadow-sm hover:shadow-md transition relative"
        >
          <!-- Avatar + Name -->
          <div class="flex items-center space-x-2">
            <div
              class="h-8 w-8 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white text-sm font-semibold"
            >
              {{ awaitingUser.name[0] }}
            </div>
            <span class="text-gray-800 text-sm font-medium">{{ awaitingUser.name }}</span>
          </div>

          <!-- Menu -->
          <div class="relative">
            <button
              @click="dropdownOpen = dropdownOpen === awaitingUser.id ? null : awaitingUser.id"
              class="p-1 rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-800 transition"
            >
              <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <circle cx="10" cy="4" r="1" />
                <circle cx="10" cy="10" r="1" />
                <circle cx="10" cy="16" r="1" />
              </svg>
            </button>

            <!-- Dropdown -->
            <div
              v-show="dropdownOpen === awaitingUser.id"
              class="absolute right-0 mt-2 w-32 rounded-lg shadow-lg border border-gray-100 bg-white z-10"
            >
              <ul class="py-1">
                <li>
                  <a
                    href="#"
                    @click.prevent="kickPlayer(awaitingUser)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600 transition"
                  >
                    Kick
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </section>

  <!-- Teams -->
  <section class="flex flex-col md:flex-row gap-6">
    <!-- Team A -->
    <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
      <h3 class="text-lg font-semibold mb-3 text-gray-900">Team A</h3>
      <ul class="divide-y divide-gray-200">
        <li
          v-for="player in quizRoom.players.filter((player) => player.pivot.team === 0)"
          :key="player.id"
          class="flex items-center justify-between py-3"
        >
          <div class="flex items-center space-x-3">
            <!-- Avatar placeholder -->
            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold">
              {{ player.name[0] }}
            </div>
            <span class="text-gray-800 font-medium">{{ player.name }}</span>
          </div>
          <button
            @click="toggleReady(player)"
            :class="player.is_ready
              ? 'bg-green-500 hover:bg-green-600 text-white'
              : 'bg-red-500 hover:bg-red-600 text-white'"
            class="font-bold py-1 px-4 rounded-full transition"
          >
            {{ player.is_ready ? "Ready" : "Not Ready" }}
          </button>
        </li>
      </ul>
    </div>

    <!-- Team B -->
    <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
      <h3 class="text-lg font-semibold mb-3 text-gray-900">Team B</h3>
      <ul class="divide-y divide-gray-200">
        <li
          v-for="player in quizRoom.players.filter((player) => player.pivot.team === 1)"
          :key="player.id"
          class="flex items-center justify-between py-3"
        >
          <div class="flex items-center space-x-3">
            <!-- Avatar placeholder -->
            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold">
              {{ player.name[0] }}
            </div>
            <span class="text-gray-800 font-medium">{{ player.name }}</span>
          </div>
          <button
            @click="toggleReady(player)"
            :class="player.is_ready
              ? 'bg-green-500 hover:bg-green-600 text-white'
              : 'bg-red-500 hover:bg-red-600 text-white'"
            class="font-bold py-1 px-4 rounded-full transition"
          >
            {{ player.is_ready ? "Ready" : "Not Ready" }}
          </button>
        </li>
      </ul>
    </div>
  </section>
</div>
</template>
