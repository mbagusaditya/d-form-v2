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
    emptyData: {
        src: '/lotties/empty-data.json',
        label: 'Ilustrasi data kosong',
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
} as const satisfies LottieRegistry

export type LottieName = keyof typeof lotties
