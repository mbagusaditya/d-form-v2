<script setup lang="ts">
import LandingLayout from '@/layouts/LandingLayout.vue';
import HomeHero from '@/components/modules/landing/home/HomeHero.vue';
import HomeSteps from '@/components/modules/landing/home/HomeSteps.vue';
import HomeFeatures from '@/components/modules/landing/home/HomeFeatures.vue';
import HomeShowcase from '@/components/modules/landing/home/HomeShowcase.vue';
import HomeFAQ from '@/components/modules/landing/home/HomeFAQ.vue';
import HomeCTA from '@/components/modules/landing/home/HomeCTA.vue';
</script>

<template>
    <LandingLayout>
        <HomeHero />
        <HomeSteps />
        <HomeFeatures />
        <HomeShowcase />
        <HomeFAQ />
        <HomeCTA />
    </LandingLayout>
</template>
