<x-layouts.auth>
    <div class="grid h-dvh max-h-dvh w-full grid-cols-1 gap-4 p-0 lg:grid-cols-2">
        <div class="relative z-0 hidden lg:block">
            <div
                class="absolute top-0 left-0 h-full w-full overflow-hidden"
                x-data="{
                    currentImg: 1,
                    totalImgs: 3,
                    imageList: [
                        '/images/banners/image-1.jpg',
                        '/images/banners/image-2.jpg',
                        '/images/banners/image-3.jpg',
                    ],
                    intervalId: null,
                    setCurrentImg() {
                        this.currentImg =
                            this.currentImg + 1 > this.totalImgs ? 1 : this.currentImg + 1
                    },
                }"
                x-init="
                    intervalId = setInterval(() => {
                        setCurrentImg()
                        console.log(currentImg + ' ' + intervalId)
                    }, 3000)
                "
            >
                <template x-for="(image, i) in imageList">
                    <img
                        x-bind:src="image"
                        x-bind:alt="'banner image no ' + (i + 1)"
                        class="absolute top-0 left-0 h-full w-full object-cover object-center"
                        x-show="currentImg === i + 1"
                        x-transition:enter="transition duration-500 ease-out"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="absolute top-0 left-0 h-full w-full transition duration-500 ease-in"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                    />
                </template>
            </div>

            <div
                class="absolute top-0 left-0 z-10 flex h-full w-full flex-col items-start justify-end bg-linear-to-b from-transparent to-neutral-800 px-8 pb-6"
            >
                {{-- <h1 class="mb-4 text-4xl font-bold">D-Form</h1> --}}
                <p class="text-base-300">Created by Dinus Open Source Community</p>
            </div>
        </div>

        <div class="relative max-h-dvh overflow-x-hidden overflow-y-auto pt-20 md:pt-32 xl:pt-40" x-data>
            {{-- Theme toggler --}}
            <div class="absolute top-4 right-4">
                <button
                    class="btn btn-ghost text-base-content aspect-square p-0"
                    x-on:click="$store.themeController.toggle()"
                    x-show="$store.themeController.active === 'light'"
                >
                    @svg('heroicon-o-sun', 'h-6')
                </button>

                <button
                    class="btn btn-ghost text-base-content aspect-square p-0"
                    x-on:click="$store.themeController.toggle()"
                    x-show="$store.themeController.active === 'dark'"
                >
                    @svg('heroicon-o-moon', 'h-6')
                </button>
            </div>
            {{-- End of Theme toggler --}}

            <section class="flex flex-col items-center" x-data="{ mode: 'login' }">
                <picture>
                    <source srcset="/images/logo.webp" />
                    <img src="/images/logo.png" alt="logo doscom" class="mb-4 w-28 md:w-32" />
                </picture>

                <div
                    class="relative mb-3 h-[2em] w-[90%] max-w-md text-3xl sm:mb-4 sm:h-[1.75em] sm:w-[60%] sm:max-w-none sm:text-4xl md:w-[50%] lg:w-[70%] xl:w-[55%]"
                >
                    {{-- Heading for Login --}}
                    <h3
                        class="absolute w-full text-center text-[1em] font-bold"
                        x-show="mode == 'login'"
                        x-transition:enter="transition delay-300 duration-300 ease-out"
                        x-transition:enter-start="translate-x-[-100px] opacity-0"
                        x-transition:enter-end="translate-x-0 opacity-100"
                        x-transition:leave="transition duration-300 ease-in"
                        x-transition:leave-start="translate-x-0 opacity-100"
                        x-transition:leave-end="translate-x-[-100px] opacity-0"
                    >
                        {{ __('auth.sign_in') }}
                    </h3>
                    {{-- End Of Heading for Login --}}

                    {{-- Heading for Registration --}}
                    <h3
                        class="text-center text-[1em] font-bold"
                        x-show="mode == 'register'"
                        x-transition:enter="transition delay-300 duration-300 ease-out"
                        x-transition:enter-start="translate-x-[100px] opacity-0"
                        x-transition:enter-end="translate-x-0 opacity-100"
                        x-transition:leave="transition duration-300 ease-in"
                        x-transition:leave-start="translate-x-0 opacity-100"
                        x-transition:leave-end="translate-x-[100px] opacity-0"
                    >
                        {{ __('auth.sign_up') }}
                    </h3>
                    {{-- End of Heading for Registration --}}

                    {{-- Button for Login --}}
                    <button
                        class="text-md absolute bottom-0 left-0 inline-flex text-base"
                        x-on:click="mode = 'login'"
                        x-show="mode != 'login'"
                        x-transition:enter="transition delay-750 duration-300 ease-out"
                        x-transition:enter-start=" opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition duration-300 ease-in"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                    >
                        @svg('heroicon-o-chevron-left', 'w-[1em]')
                        {{ __('auth.sign_in') }}
                    </button>
                    {{-- End of Button for Login --}}

                    {{-- Button for Registration --}}
                    <button
                        class="text-md absolute right-0 bottom-0 inline-flex text-base"
                        x-on:click="mode = 'register'"
                        x-show="mode != 'register'"
                        x-transition:enter="transition delay-750 duration-300 ease-out"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition duration-300 ease-in"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                    >
                        {{ __('auth.sign_up') }}
                        @svg('heroicon-o-chevron-right', 'w-[1em]')
                    </button>
                    {{-- End of Button for Registration --}}
                </div>

                <div class="relative w-[90%] max-w-md sm:w-[60%] sm:max-w-none md:w-[50%] lg:w-[70%] xl:w-[55%]">
                    {{-- Login Form --}}
                    <div
                        class="absolute top-0 w-full pb-4"
                        x-show="mode == 'login'"
                        x-transition:enter="transition delay-300 duration-300 ease-out"
                        x-transition:enter-start="translate-x-[-100px] opacity-0"
                        x-transition:enter-end="translate-x-0 opacity-100"
                        x-transition:leave="transition duration-300 ease-in"
                        x-transition:leave-start="translate-x-0 opacity-100"
                        x-transition:leave-end="translate-x-[-100px] opacity-0"
                    >
                        @livewire('auth.login-form')

                        <footer class="block py-3 pt-12 text-sm md:hidden">
                            <p class="text-neutral text-center">Created by Dinus Open Source Community</p>
                        </footer>
                    </div>
                    {{-- End of Login Form --}}

                    {{-- Registration Form --}}
                    <div
                        class="absolute top-0 w-full pb-4"
                        x-show="mode == 'register'"
                        x-transition:enter="transition delay-300 duration-300 ease-out"
                        x-transition:enter-start="translate-x-[100px] opacity-0"
                        x-transition:enter-end="translate-x-0 opacity-100"
                        x-transition:leave="transition duration-300 ease-in"
                        x-transition:leave-start="translate-x-0 opacity-100"
                        x-transition:leave-end="translate-x-[100px] opacity-0"
                    >
                        @livewire('auth.register-form')

                        <footer class="mt-12 block pb-8 text-sm md:hidden">
                            <p class="text-neutral text-center">Created by Dinus Open Source Community</p>
                        </footer>
                    </div>
                    {{-- End of Registration Form --}}
                </div>
            </section>
        </div>
    </div>
</x-layouts.auth>
