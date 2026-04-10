<script setup lang="ts">
import { useForm, Link, usePage } from '@inertiajs/vue3';
import { AuthSubmitButton } from '@/components/core/button';
import { AuthField } from '@/components/core/field';
import { store as login } from '@/actions/App/Http/Controllers/Auth/LoginController';
import { index as registerPage } from '@/actions/App/Http/Controllers/Auth/RegisterController';
import { toast } from 'vue-sonner';

const page = usePage();

const form = useForm({ email: '', password: '' }).dontRemember('password');

function submit() {
    form.submit(login(), {
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
            <h1 class="text-2xl font-extrabold tracking-tight text-[#111827]">Welcome back</h1>
            <p class="mt-1.5 text-sm text-[#6B7280]">
                Don't have an account?
                <Link :href="registerPage()" class="text-primary font-semibold hover:underline"> Sign Up </Link>
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <AuthField
                type="email"
                :error="form.errors.email"
                label="E-mail"
                id="login-email"
                v-model="form.email"
                required
                autocomplete="email"
                placeholder="you@example.com"
            />
            <AuthField
                type="password"
                :error="form.errors.password"
                label="Password"
                id="login-password"
                v-model="form.password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            />

            <AuthSubmitButton :form="form">
                <template #processing> Loading </template>

                Submit
            </AuthSubmitButton>
        </form>
    </div>
</template>
