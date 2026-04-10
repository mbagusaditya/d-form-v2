<script setup lang="ts">
import 'vue-sonner/style.css';
import AuthIllustration from '@/components/modules/auth/AuthIllustration.vue';
import AuthOAuthButtons from '@/components/modules/auth/AuthOAuthButtons.vue';
import { Toaster } from '@/components/ui/sonner';
</script>

<template>
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
                <slot></slot>

                <!-- OAuth -->
                <AuthOAuthButtons />

                <!-- Mobile footer -->
                <p class="mt-10 text-center text-xs text-[#9CA3AF] lg:hidden">Created by Dinus Open Source Community</p>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <Toaster position="top-right" richColors />
</template>
