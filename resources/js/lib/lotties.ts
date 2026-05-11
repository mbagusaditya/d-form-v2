import type { LottieRegistry } from '@/types/lottie'

/**
 * Registri Lottie — berkas JSON di `public/lotties/` (diperbarui untuk set ilustrasi baru).
 */
export const lotties = {
    heroProduct: {
        src: '/lotties/hero-product.json',
        label: 'Ilustrasi workspace formulir',
    },
    stepsFlow: {
        src: '/lotties/steps-flow.json',
        label: 'Ilustrasi alur kerja langkah demi langkah',
    },
    featureModules: {
        src: '/lotties/feature-modules.json',
        label: 'Ilustrasi modul fitur',
    },
    docsFlow: {
        src: '/lotties/docs-flow.json',
        label: 'Ilustrasi dokumentasi',
    },
    authSecure: {
        src: '/lotties/auth-secure.json',
        label: 'Ilustrasi keamanan masuk',
    },
    builderEmpty: {
        src: '/lotties/builder-empty.json',
        label: 'Ilustrasi kanvas builder kosong',
    },
    fieldSelected: {
        src: '/lotties/field-selected.json',
        label: 'Ilustrasi field terpilih',
    },
    comingSoon: {
        src: '/lotties/coming-soon.json',
        label: 'Ilustrasi segera hadir',
    },
    errorState: {
        src: '/lotties/error-state.json',
        label: 'Ilustrasi kesalahan',
    },
    /** Error 404 Page (loop) — Artem Peretrukhin, via LottieFiles (Simple License). */
    error404Page: {
        src: '/lotties/error-404-page.json',
        label: 'Ilustrasi halaman tidak ditemukan',
    },
    emptyData: {
        src: '/lotties/empty-data.json',
        label: 'Ilustrasi data kosong',
    },
    /** Waiting / empty roster — asset from LottieFiles (robot idle). */
    registrantsWaiting: {
        src: '/lotties/registrants-waiting.json',
        label: 'Ilustrasi menunggu pendaftar',
    },
    successCheck: {
        src: '/lotties/success-check.json',
        label: 'Ilustrasi berhasil',
    },
    loadingDots: {
        src: '/lotties/loading-dots.json',
        label: 'Indikator memuat',
    },
    eventsLive: {
        src: '/lotties/events-live.json',
        label: 'Ilustrasi acara aktif',
    },
    analyticsPulse: {
        src: '/lotties/analytics-pulse.json',
        label: 'Ilustrasi analitik',
    },
    landingHero: {
        src: '/lotties/landing-hero.json',
        label: 'Ilustrasi pengisian checklist formulir',
    },
    landingStepCreate: {
        src: '/lotties/landing-step-create.json',
        label: 'Ilustrasi kalender dan penjadwalan acara',
    },
    landingStepBuild: {
        src: '/lotties/landing-step-build.json',
        label: 'Ilustrasi pembuatan formulir pendaftaran',
    },
    landingStepMonitor: {
        src: '/lotties/landing-step-monitor.json',
        label: 'Ilustrasi grafik analitik data',
    },
    landingShowcase: {
        src: '/lotties/landing-showcase.json',
        label: 'Ilustrasi dasbor pengelolaan data',
    },
    landingCta: {
        src: '/lotties/landing-cta.json',
        label: 'Ilustrasi peluncuran roket',
    },
    featuresHero: {
        src: '/lotties/features-hero.json',
        label: 'Ilustrasi alur kerja otomasi',
    },
    featuresIntegrations: {
        src: '/lotties/features-integrations.json',
        label: 'Ilustrasi analitik dan laporan meeting',
    },
    featuresWorkflow: {
        src: '/lotties/features-workflow.json',
        label: 'Ilustrasi keamanan dan proteksi data',
    },
    eventsHero: {
        src: '/lotties/events-hero.json',
        label: 'Ilustrasi konferensi dan diskusi acara',
    },
} as const satisfies LottieRegistry

export type LottieName = keyof typeof lotties
