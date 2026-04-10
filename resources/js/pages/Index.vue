<script setup lang="ts">
import LandingLayout from '@/layouts/LandingLayout.vue';
import HomeHero from '@/components/modules/landing/home/HomeHero.vue';
import HomeStats from '@/components/modules/landing/home/HomeStats.vue';
import HomePreview from '@/components/modules/landing/home/HomePreview.vue';
import HomeSteps from '@/components/modules/landing/home/HomeSteps.vue';
import HomeCTA from '@/components/modules/landing/home/HomeCTA.vue';
</script>

<template>
    <LandingLayout>
        <HomeHero />
        <HomeStats />
        <HomePreview />
        <HomeSteps />
        <HomeCTA />
    </LandingLayout>
</template>
