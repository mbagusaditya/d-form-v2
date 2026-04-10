<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { MailIcon, UserIcon } from 'lucide-vue-next';
import { TextInput, EmailInput, PasswordInput } from '@/components/core/input';

type Props = {
    label: string;
    error?: string;
    type: 'name' | 'email' | 'password';
    id: string;
};

const { error } = defineProps<Props>();
</script>

<template>
    <div class="flex flex-col gap-2">
        <Label :for="id">
            {{ label }}
        </Label>

        <TextInput type="text" :id="id" v-if="type === 'name'" v-bind="$attrs" />
        <EmailInput :id="id" v-else-if="type === 'email'" v-bind="$attrs" />
        <PasswordInput v-else :id="id" v-bind="$attrs" />

        <p v-if="error" class="text-destructive mt-1.5 text-xs">{{ error }}</p>
    </div>
</template>
