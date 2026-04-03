<template>
    <div class="relative hidden overflow-hidden bg-[#0A84DC] lg:flex lg:flex-col lg:items-center lg:justify-center">
        <!-- Dot grid -->
        <div class="pointer-events-none absolute inset-0 opacity-[0.06]">
            <svg width="100%" height="100%">
                <defs>
                    <pattern id="auth-dots" width="24" height="24" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="0.8" fill="white" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#auth-dots)" />
            </svg>
        </div>

        <!-- Illustration SVG -->
        <div class="relative z-10 mx-auto w-full max-w-md px-12">
            <svg viewBox="0 0 400 360" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <defs>
                    <filter id="as" x="-8%" y="-8%" width="116%" height="124%">
                        <feDropShadow dx="0" dy="4" stdDeviation="12" flood-color="#000" flood-opacity="0.15" />
                    </filter>
                </defs>

                <!-- Main card -->
                <g filter="url(#as)">
                    <rect x="40" y="30" width="320" height="300" rx="20" fill="white" opacity="0.97" />
                    <rect x="40" y="30" width="320" height="50" rx="20" fill="#F9FAFB" />
                    <rect x="40" y="72" width="320" height="8" fill="#F9FAFB" />
                    <circle cx="66" cy="56" r="4" fill="#E5E7EB" />
                    <circle cx="80" cy="56" r="4" fill="#E5E7EB" />
                    <circle cx="94" cy="56" r="4" fill="#E5E7EB" />

                    <!-- Lock icon -->
                    <circle cx="200" cy="115" r="20" fill="#0A84DC" opacity="0.08" />
                    <rect x="190" y="108" width="20" height="16" rx="3" fill="none" stroke="#0A84DC" stroke-width="1.8" />
                    <path d="M194 108v-4a6 6 0 0 1 12 0v4" fill="none" stroke="#0A84DC" stroke-width="1.8" stroke-linecap="round" />
                    <circle cx="200" cy="116" r="2" fill="#0A84DC" />

                    <!-- Form fields -->
                    <rect x="80" y="150" width="240" height="20" rx="6" fill="#F9FAFB" stroke="#E5E7EB" stroke-width="0.6" />
                    <rect x="92" y="157" width="60" height="6" rx="3" fill="#E5E7EB" />
                    <rect x="80" y="180" width="240" height="20" rx="6" fill="#F9FAFB" stroke="#E5E7EB" stroke-width="0.6" />
                    <rect x="92" y="187" width="40" height="6" rx="3" fill="#E5E7EB" />
                    <circle cx="304" cy="190" r="4" fill="none" stroke="#E5E7EB" stroke-width="0.8" />

                    <!-- Button -->
                    <rect x="80" y="216" width="240" height="28" rx="8" fill="#0A84DC" class="auth-btn" />
                    <rect x="165" y="226" width="70" height="8" rx="4" fill="white" opacity="0.8" />

                    <!-- Divider -->
                    <line x1="80" y1="260" x2="150" y2="260" stroke="#E5E7EB" stroke-width="0.6" />
                    <text x="170" y="264" font-size="7" fill="#9CA3AF" font-family="system-ui" text-anchor="middle">or</text>
                    <line x1="190" y1="260" x2="320" y2="260" stroke="#E5E7EB" stroke-width="0.6" />

                    <!-- Social buttons -->
                    <rect x="80" y="276" width="115" height="26" rx="7" fill="#F9FAFB" stroke="#E5E7EB" stroke-width="0.6" />
                    <circle cx="100" cy="289" r="5" fill="#EA4335" opacity="0.15" />
                    <text x="98" y="292" font-size="6" fill="#EA4335" font-weight="800" font-family="system-ui">G</text>
                    <rect x="112" y="285" width="60" height="6" rx="3" fill="#E5E7EB" />

                    <rect x="205" y="276" width="115" height="26" rx="7" fill="#F9FAFB" stroke="#E5E7EB" stroke-width="0.6" />
                    <circle cx="225" cy="289" r="5" fill="#111827" opacity="0.1" />
                    <text x="222" y="292" font-size="6" fill="#111827" font-weight="800" font-family="system-ui">GH</text>
                    <rect x="237" y="285" width="60" height="6" rx="3" fill="#E5E7EB" />
                </g>

                <!-- Floating elements -->
                <g class="auth-float-1">
                    <rect x="0" y="60" width="50" height="36" rx="10" fill="white" opacity="0.15" />
                    <circle cx="16" cy="78" r="5" fill="white" opacity="0.3" />
                    <rect x="26" y="74" width="18" height="4" rx="2" fill="white" opacity="0.2" />
                </g>
                <g class="auth-float-2">
                    <rect x="350" y="180" width="50" height="36" rx="10" fill="white" opacity="0.15" />
                    <circle cx="382" cy="198" r="8" fill="white" opacity="0.1" />
                    <path d="M378 198l3 3 6-6" stroke="white" stroke-width="1.5" opacity="0.3" stroke-linecap="round" />
                </g>

                <!-- Cursor -->
                <g class="auth-cursor">
                    <circle cx="180" cy="190" r="6" fill="white" opacity="0.15" />
                    <circle cx="180" cy="190" r="2.5" fill="white" opacity="0.4" />
                </g>
            </svg>
        </div>

        <!-- Bottom text -->
        <div class="relative z-10 mt-10 text-center">
            <p class="text-lg font-bold text-white">DForm Platform</p>
            <p class="mt-1 text-sm text-white/50">Created by Dinus Open Source Community</p>
        </div>
    </div>
</template>

<style scoped>
.auth-float-1 { animation: authF1 6s ease-in-out infinite; }
.auth-float-2 { animation: authF2 7s ease-in-out infinite; }
@keyframes authF1 { 0%,100% { transform: translateY(0) translateX(0); } 50% { transform: translateY(-8px) translateX(4px); } }
@keyframes authF2 { 0%,100% { transform: translateY(0) translateX(0); } 50% { transform: translateY(-6px) translateX(-4px); } }

.auth-cursor { animation: authC 8s ease-in-out infinite; }
@keyframes authC { 0%,100% { transform: translate(0, 0); } 25% { transform: translate(60px, -30px); } 50% { transform: translate(100px, 20px); } 75% { transform: translate(-20px, 10px); } }

.auth-btn { animation: authBP 4s ease-in-out infinite; }
@keyframes authBP { 0%,100% { opacity: 1; } 50% { opacity: 0.85; } }
</style>
