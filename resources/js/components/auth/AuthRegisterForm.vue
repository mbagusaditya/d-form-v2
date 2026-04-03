<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const emit = defineEmits<{
    (e: 'toast', type: 'success' | 'error', message: string): void;
    (e: 'switch-mode'): void;
}>();

const page = usePage();
const errors = computed(() => (page.props as Record<string, any>).errors || {});

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirm = ref(false);
const isSubmitting = ref(false);

// Password strength
const rules = computed(() => [
    { label: 'At least 8 characters', met: form.value.password.length >= 8 },
    { label: 'Contains a number', met: /\d/.test(form.value.password) },
    { label: 'Contains uppercase', met: /[A-Z]/.test(form.value.password) },
    { label: 'Passwords match', met: form.value.password.length > 0 && form.value.password === form.value.password_confirmation },
]);

const strengthPercent = computed(() => {
    const met = rules.value.filter(r => r.met).length;
    return Math.round((met / rules.value.length) * 100);
});

const strengthColor = computed(() => {
    if (strengthPercent.value <= 25) return '#EF4444';
    if (strengthPercent.value <= 50) return '#F59E0B';
    if (strengthPercent.value <= 75) return '#3B82F6';
    return '#10B981';
});

const strengthLabel = computed(() => {
    if (strengthPercent.value <= 25) return 'Weak';
    if (strengthPercent.value <= 50) return 'Fair';
    if (strengthPercent.value <= 75) return 'Good';
    return 'Strong';
});

function submit() {
    if (form.value.password.length < 8) {
        emit('toast', 'error', 'Password must be at least 8 characters.');
        return;
    }
    if (form.value.password !== form.value.password_confirmation) {
        emit('toast', 'error', 'Passwords do not match.');
        return;
    }

    isSubmitting.value = true;
    emit('toast', 'success', 'Creating your account...');
    router.post('/auth/register', form.value, {
        onFinish: () => { isSubmitting.value = false; },
    });
}
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold tracking-tight text-[#111827]">Create account</h1>
            <p class="mt-1.5 text-sm text-[#6B7280]">
                Already have an account?
                <button @click="emit('switch-mode')" class="font-semibold text-[#0A84DC] hover:underline">Sign in</button>
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Full Name -->
            <div>
                <label for="reg-name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-[#6B7280]">Full Name</label>
                <input
                    id="reg-name"
                    v-model="form.name"
                    type="text"
                    required
                    autocomplete="name"
                    placeholder="John Doe"
                    :class="errors.name ? 'border-red-400 bg-red-50/50' : 'border-[#E5E7EB] bg-[#F9FAFB]'"
                    class="w-full rounded-xl border px-4 py-3 text-sm text-[#111827] outline-none transition-all duration-200 placeholder:text-[#9CA3AF] focus:border-[#0A84DC] focus:bg-white focus:ring-2 focus:ring-[#0A84DC]/10"
                />
                <p v-if="errors.name" class="mt-1.5 text-xs text-red-500">{{ errors.name }}</p>
            </div>

            <!-- Email -->
            <div>
                <label for="reg-email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-[#6B7280]">Email</label>
                <input
                    id="reg-email"
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
                <label for="reg-password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-[#6B7280]">Password</label>
                <div class="relative">
                    <input
                        id="reg-password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        placeholder="Min. 8 characters"
                        :class="errors.password ? 'border-red-400 bg-red-50/50' : 'border-[#E5E7EB] bg-[#F9FAFB]'"
                        class="w-full rounded-xl border px-4 py-3 pr-11 text-sm text-[#111827] outline-none transition-all duration-200 placeholder:text-[#9CA3AF] focus:border-[#0A84DC] focus:bg-white focus:ring-2 focus:ring-[#0A84DC]/10"
                    />
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#6B7280] transition-colors">
                        <svg v-if="!showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
                <p v-if="errors.password" class="mt-1.5 text-xs text-red-500">{{ errors.password }}</p>

                <!-- Password Strength -->
                <div v-if="form.password.length > 0" class="mt-3 space-y-2">
                    <!-- Progress bar -->
                    <div class="flex items-center gap-3">
                        <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-[#F3F4F6]">
                            <div
                                class="h-full rounded-full transition-all duration-500 ease-out"
                                :style="{ width: strengthPercent + '%', backgroundColor: strengthColor }"
                            />
                        </div>
                        <span class="text-xs font-semibold" :style="{ color: strengthColor }">{{ strengthLabel }}</span>
                    </div>
                    <!-- Rules checklist -->
                    <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                        <div v-for="rule in rules" :key="rule.label" class="flex items-center gap-1.5">
                            <svg v-if="rule.met" class="h-3.5 w-3.5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m5 12 5 5L20 7"/></svg>
                            <svg v-else class="h-3.5 w-3.5 text-[#D1D5DB]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="8"/></svg>
                            <span class="text-[11px]" :class="rule.met ? 'text-emerald-600 font-medium' : 'text-[#9CA3AF]'">{{ rule.label }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="reg-confirm" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-[#6B7280]">Confirm Password</label>
                <div class="relative">
                    <input
                        id="reg-confirm"
                        v-model="form.password_confirmation"
                        :type="showConfirm ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                        :class="form.password_confirmation.length > 0 && form.password !== form.password_confirmation ? 'border-red-400 bg-red-50/50' : 'border-[#E5E7EB] bg-[#F9FAFB]'"
                        class="w-full rounded-xl border px-4 py-3 pr-11 text-sm text-[#111827] outline-none transition-all duration-200 placeholder:text-[#9CA3AF] focus:border-[#0A84DC] focus:bg-white focus:ring-2 focus:ring-[#0A84DC]/10"
                    />
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#6B7280] transition-colors">
                        <svg v-if="!showConfirm" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
                <p v-if="form.password_confirmation.length > 0 && form.password !== form.password_confirmation" class="mt-1.5 text-xs text-red-500">Passwords do not match</p>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                :disabled="isSubmitting"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#0A84DC] px-6 py-3.5 text-sm font-semibold text-white shadow-[0_1px_3px_rgba(10,132,220,0.3)] transition-all duration-200 hover:bg-[#0972c0] hover:shadow-[0_4px_12px_rgba(10,132,220,0.25)] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
            >
                <svg v-if="isSubmitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="50" stroke-linecap="round" class="opacity-25" /><path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round" /></svg>
                {{ isSubmitting ? 'Creating account...' : 'Create account' }}
            </button>
        </form>
    </div>
</template>
