<script setup lang="js">
  import { ref, computed } from 'vue';
  import { usePage, useForm } from '@inertiajs/vue3';

  const { user } = usePage().props.auth;

  const { quizRoom, roomTeams, players } = defineProps({
    quizRoom: Object,
    roomTeams: Array,
    players: Array
  });

  const playersInRoom = ref(players);

  const canGameStart = computed(() => {
    return playersInRoom.value.length >= quizRoom.allowed_players_count && playersInRoom.value.every(p => p.is_ready);
  });

  const channel = Echo.join(`quizRooms.${quizRoom.id}`)
    .here((players) => playersInRoom.value = players.map(obj => {
      obj.player.is_ready = false;
      return obj.player;
    }))
    .joining((e) => {
      e.player.is_ready = false;
      playersInRoom.value.push(e.player);
      console.log(playersInRoom);
    })
    .leaving((e) => {
      playersInRoom.value = playersInRoom.value.filter(p => p.id !== e.player.id);
    })
    .listenForWhisper('PlayerChangedStatus', (data) => {
      playersInRoom.value.find(p => p.id === data.user_id).is_ready = data.is_ready;
    })
    .listenForWhisper('PlayerJoinedTeam', (e) => {
      console.log(e);
      playersInRoom.value.find(p => p.id === e.user_id).pivot.team = e.team;
    });

  const joinedPlayers = computed(() => {
    return playersInRoom.value.filter(p => p.pivot.team !== null);
  });

  const unjoinedPlayers = computed(() => {
    return playersInRoom.value.filter(p => p.pivot.team == null);
  })

  function toggleReady(index) {
    playersInRoom.value[index].is_ready = !playersInRoom.value[index].is_ready;
    updateOpponent();
  }

  function updateOpponent() {
    channel.whisper('PlayerChangedStatus', {
      user_id: user.id,
      is_ready: playersInRoom.value.find(p => p.id === user.id).is_ready
    })
  }
  console.log(joinedPlayers, unjoinedPlayers);
  function playerCanJoinTeam($team) {
    const isPlayerInTeam = playersInRoom.value.find(p => p.id === user.id).pivot.team !== null;
    const isTeamFull = playersInRoom.value.filter(p => p.pivot.team === $team).length >= quizRoom.max_per_team;

    return !isPlayerInTeam && !isTeamFull;
  }

  const form = useForm(route('quiz-battle.join-team', quizRoom.id), {
    team: '',
  });

  function joinTeam($team) {
    form.team = $team;

    form.patch(route('quiz-battle.join-team', quizRoom.id),
  {
    onSuccess: () => {
        playersInRoom.value.find(p => p.id === user.id).pivot.team = $team;
        channel.whisper('PlayerJoinedTeam', {
          user_id: user.id,
          team: $team
        })
    }
  });

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
        <div
          v-for="team in roomTeams"
          :key="team.id"
          class="border-2 border-gray-200 rounded-lg"
        >
          <h2
            class="text-xl font-semibold text-center bg-gray-100 p-4 border-b border-gray-800 shadow dark:text-gray-900"
          >
            {{ team.name }}
            <div>
              <form @submit.prevent="joinTeam(team.id)" v-if="playerCanJoinTeam(team.id)">
                <button v-if="playerCanJoinTeam(team.id)" class="bg-blue-500 text-white mt-2 py-1 rounded-lg w-[50%]" type="submit" >Join Team</button>
              </form>
            </div>
          </h2>

          <div class="flex flex-col gap-2">
            <div
              v-for="(player, index) in joinedPlayers"
              :key="player.id"
              class="flex items-center gap-1 p-4"
            >

                <span class="text-blue-800 font-medium text-xl col-span-1" v-if="player.pivot.team === team.id">{{
                  player.name
                }}</span>
                <button v-if="player.pivot.team === team.id"
                  @click="toggleReady(index)"
                  :disabled="player.id !== user.id"
                  :class="[
                    'flexjustify-center p-1 rounded border-2 transition-colors col-span-1',
                    player.is_ready ? 'bg-green-500' : 'bg-gray-100 dark:bg-gray-800 shadow',
                    player.id !== user.id
                      ? 'cursor-not-allowed opacity-60 text-gray-900 dark:text-white'
                      : '',
                  ]"
                  :aria-pressed="player.is_ready"
                  :title="player.is_ready ? 'Ready' : 'Not Ready'"
                  type="button"
                >
                  <div v-if="player.is_ready" class="inline">Ready</div>
                  <div v-else class="inline">Not Ready</div>
                </button>

            </div>
          </div>
        </div>
      </div>

      <div class="my-2">
        <div
          v-if="unjoinedPlayers.length"
          class="relative bg-blue-100 p-4 border-t border-gray-200 text-center rounded-xl"
        >
          <div
            v-for="(player, index) in unjoinedPlayers"
            :key="player.id"
            class="flex items-center gap-1 p-4"
          >
            <span class="text-blue-800 font-medium text-xl col-span-1">{{
              player.name
            }} : </span>
            <div class="inline  dark:text-gray-900">Selecting Team</div>
          </div>
        </div>
      </div>

      <button
        :disabled="!canGameStart"
        class="bg-blue-500 text-white px-6 py-2 rounded-lg w-full disabled:opacity-50 disabled:cursor-not-allowed mt-2"
      >
        Start Game
      </button>
    </div>
  </div>
</template>
