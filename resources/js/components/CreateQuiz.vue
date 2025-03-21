<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue-inertia';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm('post', 'quiz-battle', {
    name: '',
    allowed_players_count: 4,
    questions_count: 10,
});


const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});
</script>
<template>
<form @submit.prevent="submit" class="flex flex-col gap-2 px-2">
    <div class="space-y-3">
        <div class="grid grid-cols-3 gap-2">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input :class="form.errors.name ? 'border-red-500' : ''" id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Room name"  @input="form.validate('name')"/>
            </div>
            <div class="grid gap-2">
                <Label for="allowed_players_count">Allowed players count</Label>
                <Input :class="form.errors.allowed_players_count ? 'border-red-500' : ''" id="allowed_players_count" type="number" required autofocus :tabindex="2" autocomplete="allowed_players_count" v-model="form.allowed_players_count" placeholder="4" @input="form.validate('allowed_players_count')" />
            </div>
            <div class="grid gap-2">
                <Label for="questions_count">Questions Count</Label>
                <Input :class="form.errors.questions_count ? 'border-red-500' : ''" id="questions_count" type="number" required autofocus :tabindex="3" autocomplete="questions_count" v-model="form.questions_count" placeholder="4" @input="form.validate('questions_count')" />
            </div>
        </div>
    </div>
    <InputError v-for="(error, index) in form.errors" :key="index" :message="error" />

    <div>
        <Button type="submit" :disabled="form.processing" :tabindex="4" class="w-full">
            <LoaderCircle class="h-4 w-4 animate-spin" v-if="form.processing" />
            Create Room
        </Button>
    </div>
</form>
<Separator class="my-12"/>
</template>

