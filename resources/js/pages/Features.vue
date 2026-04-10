<script setup lang="ts">
import LandingLayout from '@/layouts/LandingLayout.vue';
import FeaturesHero from '@/components/modules/landing/features/FeaturesHero.vue';
import FeaturesGrid from '@/components/modules/landing/features/FeaturesGrid.vue';
import FeaturesHowItWorks from '@/components/modules/landing/features/FeaturesHowItWorks.vue';
import FeaturesIntegrations from '@/components/modules/landing/features/FeaturesIntegrations.vue';
import FeaturesComparison from '@/components/modules/landing/features/FeaturesComparison.vue';
import HomeCTA from '@/components/modules/landing/home/HomeCTA.vue';
</script>

<template>
    <LandingLayout>
        <FeaturesHero />
        <FeaturesGrid />
        <FeaturesHowItWorks />
        <FeaturesIntegrations />
        <FeaturesComparison />
        <HomeCTA />
    </LandingLayout>
</template>
