<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
    toast: { type: 'success' | 'error'; message: string } | null;
}>();

const emit = defineEmits<{
    (e: 'dismiss'): void;
}>();
</script>

<template>
    <Transition name="toast">
        <div
            v-if="toast"
            class="fixed right-4 top-4 z-50 flex items-center gap-3 rounded-xl px-5 py-3.5 shadow-lg"
            :class="toast.type === 'success' ? 'bg-[#111827] text-white' : 'bg-red-500 text-white'"
        >
            <svg v-if="toast.type === 'success'" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
            <svg v-else class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
            <span class="text-sm font-medium">{{ toast.message }}</span>
            <button @click="emit('dismiss')" class="ml-2 -mr-1 rounded-lg p-1 opacity-60 hover:opacity-100">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
    </Transition>
</template>

<style scoped>
.toast-enter-active { animation: toastIn 0.35s ease-out; }
.toast-leave-active { animation: toastOut 0.25s ease-in; }
@keyframes toastIn { from { opacity: 0; transform: translateX(100px); } to { opacity: 1; transform: translateX(0); } }
@keyframes toastOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100px); } }
</style>
