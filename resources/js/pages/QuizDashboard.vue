<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import Badge from "@/components/ui/badge/Badge.vue";
import { LoaderCircle } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import CreateQuiz from '../components/CreateQuiz.vue';
import { ref, onMounted } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Quiz Battle',
        href: '/quiz-battle',
    },
];

const { rooms, auth } = defineProps({ rooms: Array, auth: Object});

onMounted(() => {
    console.log(auth);
})

</script>

<template>
    <Head title="Quiz Battle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative w-[68%] overflow-auto mx-auto">
                <CreateQuiz />
                <table class="w-full caption-bottom text-sm">
                    <caption class="mt-4 text-sm text-muted-foreground">
                    A list of available rooms.
                    </caption>
                    <thead class="[&_tr]:border-b">
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                            Room Name
                            </th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                            Players In Room
                            </th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                            Allowed
                            </th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                            Question Count
                            </th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                            Status
                            </th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                            Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <tr 
                            v-for="room in rooms" 
                            :key="room.id"
                            class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                        >
                            <td class="p-4 align-middle font-medium">{{ room.name }}</td>
                            <td class="p-4 align-middle">{{ room.players.length }}</td>
                            <td class="p-4 align-middle">{{ room.allowed_players_count }}</td>
                            <td class="p-4 align-middle">{{ room.questions_count }}</td>
                            <td class="p-4 align-middle"> <Badge variant="destructive">{{ room.status }}</Badge></td>
                            <td class="p-4 align-middle">
                                <form :action="route('join-room', room.id)" method="GET"></form>
                                <Button class="w-full bg-green-500 hover:bg-green-600">
                                    <!-- <LoaderCircle class="h-4 w-4 animate-spin" /> -->
                                    Join Room
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>  
        </div>
    </AppLayout>
</template>