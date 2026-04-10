<script setup lang="ts">
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { UserIcon } from 'lucide-vue-next';

type Props = {
    type: 'text' | 'tel' | 'number';
    id: string;
    icon?: boolean;
};

const { type, id, icon = false } = defineProps<Props>();
</script>

<template>
    <InputGroup>
        <InputGroupAddon v-if="icon">
            <slot name="icon">
                <UserIcon />
            </slot>
        </InputGroupAddon>
        <InputGroupInput :type="type" :id="id" v-bind="$attrs" />
    </InputGroup>
</template>
