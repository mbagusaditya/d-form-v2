<script setup lang="ts">
import Navbar from '@/components/modules/landing/Navbar.vue';
import Footer from '@/components/modules/landing/Footer.vue';
</script>

<template>
    <div class="relative min-h-screen bg-background font-sans antialiased">
        <Navbar />
        <main class="relative">
            <slot />
        </main>
        <Footer />
    </div>
</template>
