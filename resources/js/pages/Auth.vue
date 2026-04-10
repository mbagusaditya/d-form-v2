<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthToast from '@/components/modules/auth/AuthToast.vue';
import AuthIllustration from '@/components/modules/auth/AuthIllustration.vue';
import AuthLoginForm from '@/components/modules/auth/AuthLoginForm.vue';
import AuthRegisterForm from '@/components/modules/auth/AuthRegisterForm.vue';
import AuthOAuthButtons from '@/components/modules/auth/AuthOAuthButtons.vue';

const mode = ref<'login' | 'register'>('login');

// Toast system
const toast = ref<{ type: 'success' | 'error'; message: string } | null>(null);
let toastTimer: ReturnType<typeof setTimeout> | null = null;

function showToast(type: 'success' | 'error', message: string) {
    if (toastTimer) clearTimeout(toastTimer);
    toast.value = { type, message };
    toastTimer = setTimeout(() => {
        toast.value = null;
    }, 4000);
}

function dismissToast() {
    toast.value = null;
}

// Watch for server errors
const page = usePage();
const errors = computed(() => (page.props as Record<string, any>).errors || {});
watch(
    errors,
    (newErrors) => {
        const keys = Object.keys(newErrors);
        if (keys.length > 0) showToast('error', newErrors[keys[0]]);
    },
    { deep: true }
);
</script>

<template>
    <!-- Toast -->
    <AuthToast :toast="toast" @dismiss="dismissToast" />

    <div class="grid min-h-dvh w-full grid-cols-1 lg:grid-cols-2">
        <!-- Left: Illustration -->
        <AuthIllustration />

        <!-- Right: Auth Forms -->
        <div class="flex min-h-dvh flex-col items-center justify-center overflow-y-auto bg-white px-6 py-12 sm:px-12">
            <div class="w-full max-w-sm">
                <!-- Logo -->
                <a href="/" class="mb-10 flex items-center gap-2.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0A84DC]">
                        <svg
                            width="22"
                            height="22"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="white"
                            stroke-width="2.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                            <path d="M14 2v6h6" />
                            <path d="M16 13H8" />
                            <path d="M16 17H8" />
                            <path d="M10 9H8" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-[#111827]">
                        D<span class="text-[#0A84DC]">Form</span>
                    </span>
                </a>

                <!-- Forms -->
                <AuthLoginForm v-if="mode === 'login'" @toast="showToast" @switch-mode="mode = 'register'" />
                <AuthRegisterForm v-else @toast="showToast" @switch-mode="mode = 'login'" />

                <!-- OAuth -->
                <AuthOAuthButtons />

                <!-- Mobile footer -->
                <p class="mt-10 text-center text-xs text-[#9CA3AF] lg:hidden">Created by Dinus Open Source Community</p>
            </div>
        </div>
    </div>
</template>
