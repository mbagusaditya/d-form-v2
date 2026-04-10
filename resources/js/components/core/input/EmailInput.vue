<script setup lang="ts">
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { MailIcon } from 'lucide-vue-next';

type Props = {
    id: string;
    icon?: boolean;
};

const { id, icon = false } = defineProps<Props>();
</script>

<template>
    <InputGroup>
        <InputGroupAddon v-if="icon">
            <slot name="icon">
                <MailIcon />
            </slot>
        </InputGroupAddon>
        <InputGroupInput type="email" :id="id" v-bind="$attrs" />
    </InputGroup>
</template>
