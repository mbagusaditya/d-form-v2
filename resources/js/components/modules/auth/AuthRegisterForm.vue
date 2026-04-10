<script setup lang="ts">
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { AuthField } from '@/components/core/field';
import { AuthSubmitButton } from '@/components/core/button';
import { index as loginPage } from '@/actions/App/Http/Controllers/Auth/LoginController';
import { store as register } from '@/actions/App/Http/Controllers/Auth/RegisterController';
import { toast } from 'vue-sonner';

const page = usePage();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
}).dontRemember('password', 'password_confirmation');

// Password strength
const rules = computed(() => [
    { label: 'At least 8 characters', met: form.password.length >= 8 },
    { label: 'Contains a number', met: /\d/.test(form.password) },
    { label: 'Contains uppercase', met: /[A-Z]/.test(form.password) },
    {
        label: 'Passwords match',
        met: form.password.length > 0 && form.password === form.password_confirmation,
    },
]);

const strengthPercent = computed(() => {
    const met = rules.value.filter((r) => r.met).length;
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
    if (form.password.length < 8) {
        toast.error('Password must be at least 8 characters.');
        return;
    }
    if (form.password !== form.password_confirmation) {
        toast.error('Password does not match');
        return;
    }

    form.submit(register(), {
        onFinish: () => {
            if (!page.flash.toast) return;

            (toast as Record<string, any>)[page.flash.toast.type](page.flash.toast.message);
        },
    });
}
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold tracking-tight text-[#111827]">Create account</h1>
            <p class="mt-1.5 text-sm text-[#6B7280]">
                Already have an account?
                <Link :href="loginPage()" class="text-primary font-semibold hover:underline"> Sign In </Link>
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <AuthField
                label="Name"
                type="name"
                :error="form.errors.name"
                id="register-name"
                v-model="form.name"
                required
                autocomplete="name"
                placeholder="Your name"
            />
            <AuthField
                label="E-mail"
                type="email"
                :error="form.errors.email"
                id="register-email"
                v-model="form.email"
                required
                autocomplete="email"
                placeholder="you@gmail.com"
            />
            <AuthField
                label="Password"
                type="password"
                :error="form.errors.password"
                id="register-password"
                v-model="form.password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
            />

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
                        <svg
                            v-if="rule.met"
                            class="h-3.5 w-3.5 text-emerald-500"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linecap="round"
                        >
                            <path d="m5 12 5 5L20 7" />
                        </svg>
                        <svg
                            v-else
                            class="h-3.5 w-3.5 text-[#D1D5DB]"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        >
                            <circle cx="12" cy="12" r="8" />
                        </svg>
                        <span
                            class="text-[11px]"
                            :class="rule.met ? 'font-medium text-emerald-600' : 'text-[#9CA3AF]'"
                            >{{ rule.label }}</span
                        >
                    </div>
                </div>
            </div>

            <AuthField
                label="Password Confirmation"
                type="password"
                :error="form.errors.password_confirmation"
                id="register-password-confirmation"
                v-model="form.password_confirmation"
                required
                autocomplete="new-password"
                placeholder="••••••••"
            />

            <AuthSubmitButton :form="form">
                <template #processing> Loading </template>

                Submit
            </AuthSubmitButton>
        </form>
    </div>
</template>
