<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Search } from 'lucide-vue-next'
import { REGISTRANTS_TAB_ITEMS } from '@/lib/registrantsUi'

const searchQuery = defineModel<string>('searchQuery', { required: true })
const activeStatusTab = defineModel<'all' | 'pending' | 'accepted' | 'rejected'>('activeStatusTab', {
    required: true,
})
const activeFormFilter = defineModel<string>('activeFormFilter', { required: true })

defineProps<{
    statusCounts: Record<'all' | 'pending' | 'accepted' | 'rejected', number>
    forms: { id: string; title: string }[]
}>()
</script>

<template>
    <Card class="rounded-2xl border-border/70 shadow-sm ring-1 ring-black/[0.03] dark:ring-white/[0.06]">
        <CardHeader class="pb-3">
            <CardTitle class="text-base font-medium">Filter dan pencarian</CardTitle>
            <CardDescription class="text-sm">
                Pilih status review, batasi ke satu formulir, atau cari berdasarkan nama, email, kode registrasi, atau judul form.
            </CardDescription>
        </CardHeader>
        <CardContent class="flex flex-col gap-5 pt-0">
            <Tabs v-model="activeStatusTab" class="w-full" aria-label="Filter status pendaftar">
                <TabsList class="flex h-auto min-h-10 w-full flex-wrap gap-1 rounded-xl bg-muted/50 p-1 sm:inline-flex sm:w-auto">
                    <TabsTrigger
                        v-for="t in REGISTRANTS_TAB_ITEMS"
                        :key="t.value"
                        :value="t.value"
                        class="rounded-lg px-3 py-2 text-xs font-medium data-[state=active]:bg-card data-[state=active]:shadow-sm sm:px-3.5"
                    >
                        {{ t.label }}
                        <span
                            :class="[
                                'ml-1.5 rounded-full px-1.5 py-0.5 text-[10px] font-semibold tabular-nums',
                                t.value === 'pending' && 'bg-warning/20 text-warning-foreground',
                                t.value === 'accepted' && 'bg-success/15 text-success',
                                t.value === 'rejected' && 'bg-destructive/12 text-destructive',
                                t.value === 'all' && 'bg-muted text-muted-foreground',
                            ]"
                        >
                            {{ statusCounts[t.value] }}
                        </span>
                    </TabsTrigger>
                </TabsList>
            </Tabs>

            <div class="flex flex-col gap-4 border-t border-border/60 pt-4 sm:flex-row sm:items-end sm:gap-4">
                <div v-if="forms.length > 0" class="w-full shrink-0 sm:max-w-xs">
                    <Label for="registrants-form-filter" class="text-xs font-semibold text-muted-foreground">
                        Formulir
                    </Label>
                    <Select v-model="activeFormFilter">
                        <SelectTrigger
                            id="registrants-form-filter"
                            class="mt-1.5 h-10 w-full rounded-xl"
                        >
                            <SelectValue placeholder="Semua formulir" />
                        </SelectTrigger>
                        <SelectContent class="rounded-xl">
                            <SelectItem value="all" class="rounded-lg">Semua formulir</SelectItem>
                            <SelectItem
                                v-for="f in forms"
                                :key="f.id"
                                :value="f.id"
                                class="rounded-lg"
                            >
                                {{ f.title }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="relative min-w-0 flex-1">
                    <Label for="registrants-search" class="text-xs font-semibold text-muted-foreground">
                        Cari
                    </Label>
                    <div class="relative mt-1.5">
                        <Search
                            class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <Input
                            id="registrants-search"
                            v-model="searchQuery"
                            type="search"
                            placeholder="Contoh: nama peserta, email, kode registrasi, judul form…"
                            class="h-10 w-full pl-9"
                            autocomplete="off"
                        />
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
