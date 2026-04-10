<script setup lang="ts">
import logout from '@/actions/App/Http/Controllers/Auth/LogoutController';
import useAuth from '@/utils/composables/useAuth';
import { Form, usePage } from '@inertiajs/vue3';

const page = usePage();

const user = useAuth(page.props);
</script>

<template>
    hello, ini dashboard

    {{ user?.name }}

    <Form method="post" :action="logout()">
        <button>Logout</button>
    </Form>
</template>
