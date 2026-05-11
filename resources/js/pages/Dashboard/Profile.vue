<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import { toast } from 'vue-sonner'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import PageHeader from '@/components/modules/dashboard/PageHeader.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import UserAvatarFallback from '@/components/modules/user/UserAvatarFallback.vue'
import { userAvatarSeed } from '@/lib/userAvatarFallback'
import { Separator } from '@/components/ui/separator'
import useAuth from '@/utils/composables/useAuth'
import { User as UserIcon, Shield, CalendarDays, Save, Lock, MailWarning, ImagePlus, Trash2 } from 'lucide-vue-next'
import {
    DASHBOARD_PROFILE_AVATAR_URL,
    DASHBOARD_PROFILE_PASSWORD_URL,
    DASHBOARD_PROFILE_UPDATE_URL,
} from '@/lib/dashboardProfileRoutes'

defineOptions({ layout: DashboardLayout })

const page = usePage()
const user = useAuth(page.props)

const hasLocalPassword = computed<boolean>(() => user.value?.has_local_password !== false)

const emailUnverified = computed<boolean>(() => !user.value?.email_verified_at)

const memberSince = computed<string>(() => {
    if (!user.value?.created_at) return '—'
    return new Date(user.value.created_at).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
})

const avatarFileInputRef = ref<HTMLInputElement | null>(null)
const pendingPreviewUrl = ref<string | null>(null)
const pendingFile = ref<File | null>(null)

const avatarDisplaySrc = computed<string | null>(() => pendingPreviewUrl.value ?? user.value?.avatar ?? null)

const hasAvatarOrPreview = computed<boolean>(() => Boolean(avatarDisplaySrc.value))

function clearPendingAvatar(): void {
    if (pendingPreviewUrl.value) {
        URL.revokeObjectURL(pendingPreviewUrl.value)
        pendingPreviewUrl.value = null
    }
    pendingFile.value = null
    if (avatarFileInputRef.value) {
        avatarFileInputRef.value.value = ''
    }
}

function onAvatarFileChange(ev: Event): void {
    const input = ev.target as HTMLInputElement
    const file = input.files?.[0]
    if (!file) {
        clearPendingAvatar()
        return
    }
    clearPendingAvatar()
    pendingFile.value = file
    pendingPreviewUrl.value = URL.createObjectURL(file)
}

const avatarUploadForm = useForm<{ avatar: File | null }>({
    avatar: null,
})

const avatarDeleteForm = useForm({})

const profileForm = useForm<{ name: string; email: string }>({
    name: user.value?.name ?? '',
    email: user.value?.email ?? '',
})

watch(
    user,
    (next) => {
        if (!next) return
        profileForm.defaults({
            name: next.name,
            email: next.email,
        })
        profileForm.reset()
        clearPendingAvatar()
    },
    { deep: true },
)

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
}).dontRemember('current_password', 'password', 'password_confirmation')

function flashToastFromPage(): void {
    const t = page.flash?.toast
    if (!t) return
    if (t.type === 'success') toast.success(t.message)
    else toast.error(t.message)
}

function openAvatarPicker(): void {
    avatarFileInputRef.value?.click()
}

function submitAvatarUpload(): void {
    if (!pendingFile.value) {
        toast.error('Pilih berkas gambar terlebih dahulu.')
        return
    }
    avatarUploadForm.avatar = pendingFile.value
    avatarUploadForm.post(DASHBOARD_PROFILE_AVATAR_URL, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            clearPendingAvatar()
        },
        onFinish: flashToastFromPage,
    })
}

function submitAvatarRemove(): void {
    avatarDeleteForm.delete(DASHBOARD_PROFILE_AVATAR_URL, {
        preserveScroll: true,
        onFinish: flashToastFromPage,
    })
}

function updateProfileSubmit(): void {
    profileForm.patch(DASHBOARD_PROFILE_UPDATE_URL, {
        preserveScroll: true,
        onFinish: flashToastFromPage,
    })
}

function updatePasswordSubmit(): void {
    passwordForm.put(DASHBOARD_PROFILE_PASSWORD_URL, {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset()
        },
        onFinish: flashToastFromPage,
    })
}
</script>

<template>
    <Head title="Profil" />

    <div class="flex flex-col gap-8 md:gap-10">
        <PageHeader
            eyebrow="Akun"
            title="Profil"
            subtitle="Ubah foto profil, nama, email, dan kata sandi sesuai yang didukung akun Anda."
        />

        <div
            v-if="emailUnverified"
            role="status"
            class="border-amber-500/40 bg-amber-500/10 text-amber-950 dark:text-amber-50 flex gap-3 rounded-xl border p-4 dark:bg-amber-500/5"
        >
            <MailWarning class="mt-0.5 size-5 shrink-0 text-amber-700 dark:text-amber-400" aria-hidden="true" />
            <div class="min-w-0 space-y-1">
                <p class="text-sm font-semibold text-amber-950 dark:text-amber-100">Email belum diverifikasi</p>
                <p class="text-amber-900/90 text-sm leading-relaxed dark:text-amber-50/90">
                    Jika Anda baru mengubah email, verifikasi ulang bila tim kami mengirim tautan verifikasi. Sementara itu
                    Anda tetap dapat memakai akun ini.
                </p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <Card class="rounded-2xl border-border/70 shadow-sm ring-1 ring-black/[0.03] dark:ring-white/[0.06]">
                <CardContent class="flex flex-col items-center gap-4 p-6">
                    <UserAvatarFallback
                        :src="avatarDisplaySrc"
                        :seed="userAvatarSeed(user)"
                        avatar-class="size-24"
                    />
                    <div class="text-center">
                        <h2 class="text-lg font-semibold">{{ user?.name ?? 'Pengguna' }}</h2>
                        <p class="text-sm text-muted-foreground">{{ user?.email ?? '' }}</p>
                    </div>
                    <div v-if="user?.roles?.length" class="flex flex-wrap justify-center gap-1.5">
                        <Badge v-for="role in user.roles" :key="role" variant="secondary" class="text-[10px] capitalize">
                            <Shield class="mr-1 size-3" />{{ role }}
                        </Badge>
                    </div>
                    <Separator />
                    <input
                        ref="avatarFileInputRef"
                        type="file"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        class="sr-only"
                        aria-hidden="true"
                        tabindex="-1"
                        @change="onAvatarFileChange"
                    />
                    <div class="flex w-full max-w-[240px] flex-col gap-2">
                        <Button type="button" variant="outline" size="sm" class="w-full gap-2" @click="openAvatarPicker">
                            <ImagePlus class="size-4 shrink-0" />
                            Pilih foto
                        </Button>
                        <Button
                            v-if="pendingFile"
                            type="button"
                            size="sm"
                            class="w-full"
                            :disabled="avatarUploadForm.processing"
                            @click="submitAvatarUpload"
                        >
                            <Save class="mr-1.5 size-4" />Simpan foto
                        </Button>
                        <Button
                            v-if="pendingFile"
                            type="button"
                            variant="ghost"
                            size="sm"
                            class="w-full text-muted-foreground"
                            :disabled="avatarUploadForm.processing"
                            @click="clearPendingAvatar"
                        >
                            Batal
                        </Button>
                        <Button
                            v-if="hasAvatarOrPreview && !pendingFile && user?.avatar"
                            type="button"
                            variant="outline"
                            size="sm"
                            class="text-destructive hover:bg-destructive/10 w-full gap-2 border-destructive/30"
                            :disabled="avatarDeleteForm.processing"
                            @click="submitAvatarRemove"
                        >
                            <Trash2 class="size-4 shrink-0" />
                            Hapus foto profil
                        </Button>
                    </div>
                    <p v-if="avatarUploadForm.errors.avatar" class="text-destructive text-center text-xs">
                        {{ avatarUploadForm.errors.avatar }}
                    </p>
                    <Separator />
                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                        <CalendarDays class="size-3.5" />
                        <span>Bergabung sejak {{ memberSince }}</span>
                    </div>
                </CardContent>
            </Card>

            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card class="rounded-2xl border-border/70 shadow-sm ring-1 ring-black/[0.03] dark:ring-white/[0.06]">
                    <CardHeader class="pb-3">
                        <CardTitle class="flex items-center gap-2 text-base font-medium">
                            <UserIcon class="size-4" />Data diri
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4 pt-0">
                        <p class="text-muted-foreground text-sm leading-relaxed">
                            Nama dan email disimpan lewat formulir ini; foto profil diatur dari kartu sebelah kiri.
                        </p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="flex flex-col gap-1.5">
                                <Label for="profile-name" class="text-xs">Nama lengkap <span class="text-destructive">*</span></Label>
                                <Input
                                    id="profile-name"
                                    v-model="profileForm.name"
                                    maxlength="150"
                                    autocomplete="name"
                                    placeholder="Nama Anda"
                                />
                                <p v-if="profileForm.errors.name" class="text-xs text-destructive">{{ profileForm.errors.name }}</p>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <Label for="profile-email" class="text-xs">Email <span class="text-destructive">*</span></Label>
                                <Input
                                    id="profile-email"
                                    v-model="profileForm.email"
                                    type="email"
                                    autocomplete="email"
                                    placeholder="nama@email.com"
                                />
                                <p v-if="profileForm.errors.email" class="text-xs text-destructive">{{ profileForm.errors.email }}</p>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <Button :disabled="profileForm.processing" @click="updateProfileSubmit">
                                <Save class="mr-1.5 size-4" />Simpan perubahan
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card class="rounded-2xl border-border/70 shadow-sm ring-1 ring-black/[0.03] dark:ring-white/[0.06]">
                    <CardHeader class="pb-3">
                        <CardTitle class="flex items-center gap-2 text-base font-medium">
                            <Lock class="size-4" />{{ hasLocalPassword ? 'Ubah kata sandi' : 'Atur kata sandi' }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4 pt-0">
                        <p v-if="!hasLocalPassword" class="text-muted-foreground text-sm leading-relaxed">
                            Anda masuk lewat OAuth. Anda bisa menambahkan kata sandi agar bisa masuk juga dengan email
                            dan kata sandi.
                        </p>
                        <div v-if="hasLocalPassword" class="flex flex-col gap-1.5">
                            <Label for="current_password" class="text-xs">Kata sandi saat ini <span class="text-destructive">*</span></Label>
                            <Input
                                id="current_password"
                                v-model="passwordForm.current_password"
                                type="password"
                                placeholder="Kata sandi saat ini"
                                autocomplete="current-password"
                            />
                            <p v-if="passwordForm.errors.current_password" class="text-xs text-destructive">
                                {{ passwordForm.errors.current_password }}
                            </p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="flex flex-col gap-1.5">
                                <Label for="new_password" class="text-xs"
                                    >{{ hasLocalPassword ? 'Kata sandi baru' : 'Kata sandi' }} <span class="text-destructive">*</span></Label
                                >
                                <Input
                                    id="new_password"
                                    v-model="passwordForm.password"
                                    type="password"
                                    placeholder="Minimal 8 karakter"
                                    autocomplete="new-password"
                                />
                                <p v-if="passwordForm.errors.password" class="text-xs text-destructive">{{ passwordForm.errors.password }}</p>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <Label for="confirm_password" class="text-xs">Konfirmasi kata sandi <span class="text-destructive">*</span></Label>
                                <Input
                                    id="confirm_password"
                                    v-model="passwordForm.password_confirmation"
                                    type="password"
                                    placeholder="Ulangi kata sandi"
                                    autocomplete="new-password"
                                />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <Button variant="outline" :disabled="passwordForm.processing" @click="updatePasswordSubmit">
                                <Lock class="mr-1.5 size-4" />{{ hasLocalPassword ? 'Perbarui kata sandi' : 'Simpan kata sandi' }}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
