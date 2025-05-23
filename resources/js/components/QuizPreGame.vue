<script setup lang="js">
  import { ref, computed, onUnmounted } from 'vue';
  
  import { usePage, useForm, router } from '@inertiajs/vue3';
  import { route } from 'ziggy-js';

  const { user } = usePage().props.auth;

  const { quizRoom, roomTeams, players, playerRoles } = defineProps({
    quizRoom: Object,
    roomTeams: Array,
    players: Array,
    playerRoles: Array
  });

  const updatePlayer = (playerData) => {
    const player = players.find(p => p.id === user.id);
    console.log(player, user.id);
    router.patch(route('quiz-battle.update', quizRoom.id), {
      'is_ready': playerData.isReady !== null ? playerData.inRoom : (player.pivot.is_ready ? true : false),
      'team': playerData.team !== null ? playerData.team : player.pivot.team,
      'in_room': playerData.inRoom !== null ? playerData.inRoom : (player.pivot.in_room ? true : false)
    });

    updateOpponent();
  }

  const canGameStart = computed(() => {
    return players.length >= quizRoom.allowed_players_count && players.every(p => p.is_ready);
  });
  
  const channel = Echo.join(`quizRooms.${quizRoom.id}`)
    .here((e) => {
      updatePlayer({ isReady: null, team: null, inRoom: true });
      // router.reload({ only: [ 'players' ] });
    })
    .joining((e) => {
      updatePlayer({ isReady: null, team: null, inRoom: true });
      // router.reload({ only: [ 'players' ] });
    })
    .leaving((e) => {
      console.log('here');
      updatePlayer({ isReady: null, team: null, inRoom: false });
      router.reload({ only: [ 'players' ] });
    })
  const unjoinedPlayers = computed(() => {
    return players.filter(p => p.pivot.team == null && p.pivot.in_room == true);
  })

  const playerIsHost = computed(() => {
    if(!playerRoles) {
      return false;
    }

    const hostRoleId = playerRoles.find(r => r.name === 'Host').id;
    const player = players.find(p => p.id === user.id);

    return player.pivot.role === hostRoleId;
  });

  function updateOpponent() {
    channel.whisper('PlayerChangedStatus');
    channel.whisper('PlayerJoinedTeam');
  }
  
  function playerCanJoinTeam($team) {
    const isPlayerInTeam = players.find(p => p.id === user.id).pivot.team !== null;
    const isTeamFull = players.filter(p => p.pivot.team === $team).length >= quizRoom.max_per_team;

    return !isPlayerInTeam && !isTeamFull;
  }

  function joinTeam($team) {
    router.patch(route('quiz-battle.join-team', quizRoom.id),
      {
        onSuccess: () => {
          players.find(p => p.id === user.id).pivot.team = $team;

        }
      });
  }

  function getPlayersInTeam($team) {
    return players.filter(p => p.pivot.team === $team);
  }


  function startGame() {
    router.patch(route('quiz-battle.start', quizRoom.id));
    channel.whisper('GameStarted');
  }

  onUnmounted(() => {
    Echo.leave(`quizRooms.${quizRoom.id}`);
  })

</script>
<template>
    <div class="shadow-lg rounded-lg p-6 mb-6 m-4">
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
            class="text-xl font-semibold text-center bg-gray-100 dark:bg-slate-800 dark:text-white p-4 border-b border-gray-800 shadow dark:text-gray-900"
          >
            {{ team.name }}
            <div>
              <form @submit.prevent="updatePlayer({ isReady: null, team: team.id, inRoom: null })" v-if="playerCanJoinTeam(team.id)">
                <button v-if="playerCanJoinTeam(team.id)" class="bg-blue-500 text-white mt-2 py-1 rounded-lg w-[50%]" type="submit" >Join Team</button>
              </form>
            </div>
          </h2>

          <div class="flex flex-col gap-2">
            <div
              v-for="(player, index) in getPlayersInTeam(team.id)"
              :key="player.id"
              class="flex items-center gap-1 p-4"
            >
                <span class="text-blue-800 dark:text-yellow-400 font-medium text-xl col-span-1">
                {{ player.name }}
                </span>
                <button
                  @click="updatePlayer({ isReady: null, team: team.id, inRoom: null })"
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
            <span v-if="user.id !== player.id" class="text-blue-800 font-medium text-xl col-span-1">{{
              player.name
            }} : </span>
            <span v-else class="text-blue-800 font-medium text-xl col-span-1">You : </span>
            <div class="inline  dark:text-gray-900">Selecting Team</div>
          </div>
        </div>
      </div>

      <button
        :disabled="!playerIsHost || !canGameStart"
        class="bg-blue-500 text-white px-6 py-2 rounded-lg w-full disabled:opacity-50 disabled:cursor-not-allowed mt-2"
        @click="startGame()"
      >
        Start Game
      </button>
    </div>

</template>
