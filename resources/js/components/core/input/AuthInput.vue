<script setup lang="ts">
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';

type Props = {
    type: 'text' | 'email';
    id: string;
};

const { type } = defineProps<Props>();
</script>

<template>
    <InputGroup>
        <InputGroupAddon>
            <slot name="icon"></slot>
        </InputGroupAddon>
        <InputGroupInput :type="type" :id="id" v-bind="$attrs" />
    </InputGroup>
</template>
