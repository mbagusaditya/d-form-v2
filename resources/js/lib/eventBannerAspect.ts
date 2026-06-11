import { cn } from '@/lib/utils'

/** Rasio tetap 4:3 untuk gambar acara di kartu grid dan thumbnail daftar. */
export const EVENT_CARD_BANNER_ASPECT = 'aspect-[4/3]'

/** Rasio tetap 16:10 untuk banner hero di kartu detail / registrasi. */
export const EVENT_HERO_BANNER_ASPECT = 'aspect-[16/10]'

export function eventCardBannerContainerClass(className?: string): string {
    return cn('relative w-full overflow-hidden bg-muted', EVENT_CARD_BANNER_ASPECT, className)
}

export function eventHeroBannerContainerClass(className?: string): string {
    return cn('relative w-full overflow-hidden bg-muted', EVENT_HERO_BANNER_ASPECT, className)
}

export function eventListThumbnailContainerClass(className?: string): string {
    return cn(
        'relative shrink-0 overflow-hidden rounded-xl bg-muted',
        EVENT_CARD_BANNER_ASPECT,
        'w-full sm:w-24',
        className,
    )
}
