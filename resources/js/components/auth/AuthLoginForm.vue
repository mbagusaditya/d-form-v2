<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const emit = defineEmits<{
    (e: 'toast', type: 'success' | 'error', message: string): void;
    (e: 'switch-mode'): void;
}>();

const page = usePage();
const errors = computed(() => (page.props as Record<string, any>).errors || {});

const form = ref({ email: '', password: '' });
const showPassword = ref(false);
const isSubmitting = ref(false);

function submit() {
    isSubmitting.value = true;
    emit('toast', 'success', 'Signing in...');
    router.post('/auth/login', form.value, {
        onFinish: () => { isSubmitting.value = false; },
    });
}
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold tracking-tight text-[#111827]">Welcome back</h1>
            <p class="mt-1.5 text-sm text-[#6B7280]">
                Don't have an account?
                <button @click="emit('switch-mode')" class="font-semibold text-[#0A84DC] hover:underline">Sign up</button>
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Email -->
            <div>
                <label for="login-email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-[#6B7280]">Email</label>
                <input
                    id="login-email"
                    v-model="form.email"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="you@example.com"
                    :class="errors.email ? 'border-red-400 bg-red-50/50' : 'border-[#E5E7EB] bg-[#F9FAFB]'"
                    class="w-full rounded-xl border px-4 py-3 text-sm text-[#111827] outline-none transition-all duration-200 placeholder:text-[#9CA3AF] focus:border-[#0A84DC] focus:bg-white focus:ring-2 focus:ring-[#0A84DC]/10"
                />
                <p v-if="errors.email" class="mt-1.5 text-xs text-red-500">{{ errors.email }}</p>
            </div>

            <!-- Password -->
            <div>
                <label for="login-password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-[#6B7280]">Password</label>
                <div class="relative">
                    <input
                        id="login-password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        :class="errors.password ? 'border-red-400 bg-red-50/50' : 'border-[#E5E7EB] bg-[#F9FAFB]'"
                        class="w-full rounded-xl border px-4 py-3 pr-11 text-sm text-[#111827] outline-none transition-all duration-200 placeholder:text-[#9CA3AF] focus:border-[#0A84DC] focus:bg-white focus:ring-2 focus:ring-[#0A84DC]/10"
                    />
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#6B7280] transition-colors">
                        <svg v-if="!showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
                <p v-if="errors.password" class="mt-1.5 text-xs text-red-500">{{ errors.password }}</p>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                :disabled="isSubmitting"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#0A84DC] px-6 py-3.5 text-sm font-semibold text-white shadow-[0_1px_3px_rgba(10,132,220,0.3)] transition-all duration-200 hover:bg-[#0972c0] hover:shadow-[0_4px_12px_rgba(10,132,220,0.25)] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
            >
                <svg v-if="isSubmitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="50" stroke-linecap="round" class="opacity-25" /><path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round" /></svg>
                {{ isSubmitting ? 'Signing in...' : 'Sign in' }}
            </button>
        </form>
    </div>
</template>
