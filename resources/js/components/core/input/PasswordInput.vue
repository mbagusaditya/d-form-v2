<script setup lang="ts">
import { InputGroup, InputGroupAddon, InputGroupInput, InputGroupButton } from '@/components/ui/input-group';
import { LockKeyholeIcon, EyeIcon, EyeClosedIcon } from 'lucide-vue-next';
import { ref } from 'vue';

type Props = {
    id: string;
};

const { id } = defineProps<Props>();

let showPassword = ref(false);
</script>

<template>
    <InputGroup>
        <InputGroupAddon>
            <LockKeyholeIcon />
        </InputGroupAddon>
        <InputGroupInput :type="showPassword ? 'text' : 'password'" :id="id" v-bind="$attrs" />
        <InputGroupAddon align="inline-end">
            <InputGroupButton @click="showPassword = !showPassword" class="cursor-pointer" type="button">
                <EyeIcon v-if="showPassword" />
                <EyeClosedIcon v-else />
            </InputGroupButton>
        </InputGroupAddon>
    </InputGroup>
</template>
