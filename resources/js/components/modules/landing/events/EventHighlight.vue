<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'

const props = defineProps<{
    events: IEvent[]
}>()

const visible = ref(false)
onMounted(() => {
    const obs = new IntersectionObserver(
        ([e]) => { if (e?.isIntersecting) { visible.value = true; obs.disconnect() } },
        { threshold: 0.1 },
    )
    const el = document.getElementById('event-highlight')
    if (el) obs.observe(el)
})

const featured = props.events.slice(0, 3)

const statusLabel = (s: string) => {
    if (s === 'open') return 'Dibuka'
    if (s === 'full') return 'Penuh'
    if (s === 'closed') return 'Ditutup'
    return 'Segera'
}

const statusColor = (s: string) =>
    s === 'open' ? 'bg-green-500/10 text-green-600 border-green-500/20' : 'bg-muted text-muted-foreground border-border'
</script>

<template>
    <section id="event-highlight" class="border-t border-border/30 py-16 md:py-24">
        <div class="mx-auto max-w-5xl px-5 lg:px-8">
            <div :class="['mb-10 transition-all duration-500', visible ? 'opacity-100' : 'opacity-0']">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-primary">Sorotan</p>
                <h2 class="mt-2 text-[1.5rem] font-semibold tracking-tight text-foreground sm:text-[1.75rem]">Acara terbaru</h2>
            </div>

            <div v-if="featured.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="(ev, i) in featured"
                    :key="ev.id"
                    :class="['group overflow-hidden border-border/50 transition-all duration-500 hover:border-primary/25', visible ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0']"
                    :style="{ transitionDelay: `${100 + i * 80}ms` }"
                >
                    <div v-if="ev.banner_url" class="h-36 w-full overflow-hidden bg-muted">
                        <img :src="ev.banner_url" :alt="ev.title" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                    </div>
                    <CardContent class="p-5">
                        <div class="mb-2 flex items-center gap-2">
                            <Badge variant="outline" :class="['text-[10px]', statusColor(ev.registration_status)]">
                                {{ statusLabel(ev.registration_status) }}
                            </Badge>
                        </div>
                        <h3 class="text-[14px] font-semibold text-foreground line-clamp-2">{{ ev.title }}</h3>
                        <p class="mt-1 text-[12px] text-muted-foreground">{{ ev.location }}</p>
                        <div class="mt-3 flex items-center justify-between text-[11px] text-muted-foreground">
                            <span>{{ ev.registered_count }}/{{ ev.quota }} pendaftar</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
            <p v-else class="text-[14px] text-muted-foreground">Belum ada acara yang tersedia.</p>
        </div>
    </section>
</template>
