<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Woi - Selamat Datang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .slide-track {
            transition: transform 0.5s ease-in-out;
        }
        .brand-shadow {
            box-shadow: 0px 4px 20px rgba(26, 54, 93, 0.08);
        }
        /* Mobile slider scroll */
        .slide-container {
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .slide-container::-webkit-scrollbar {
            display: none;
        }
        .slide-mobile {
            scroll-snap-align: start;
        }
        .dot {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-surface-bright text-on-background font-sans antialiased h-screen w-screen overflow-hidden">

    {{-- Splash Screen Awal --}}
    <div id="splash-screen" class="absolute inset-0 z-50 flex flex-col items-center justify-center bg-primary transition-opacity duration-700">
        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuD7Nura0KkuJucNw0OuenfKq8H28FXi1sBkts348ya91kKKUSJBsOCkRz63YmLU6yo4DZBg5PmyfNsmsReXneO_ukN5PAO3TT46ugpzPBOTHQxVNa9_wxHcXr-hGY7UzADg4iSa11W5pcNtiTefLCwSe2fq88d3slHtiQKW07dc3pOiDv46-fsA0QhjVwW51e8HooTnqw4IDGIezl5O50s2KvAiYk246Xpzw4G1RuKNlvsrOTLGHJkU"
             alt="Barber Woi Logo" class="w-48 h-48 rounded-full shadow-lg object-cover mb-4">
        <h1 class="font-display-lg text-display-lg text-on-primary">Barber Woi</h1>
        <p class="font-body-lg text-body-lg text-primary-fixed-dim mt-2">Premium Grooming Experience</p>
    </div>

    {{-- Main Content --}}
    <div id="main-content" class="h-full w-full opacity-0 transition-opacity duration-700">

        {{-- MOBILE VERSION (visible on small screens) --}}
        <div class="md:hidden h-full flex flex-col">
            <!-- Header Skip -->
            <header class="flex justify-end p-margin-mobile absolute top-0 left-0 right-0 z-10">
                <button id="skip-btn-mobile" class="text-on-surface-variant font-body-sm hover:text-brand-gold transition-colors py-2 px-4 rounded-full bg-surface-container-low/80 backdrop-blur-sm">
                    Lewati
                </button>
            </header>

            <!-- Slider Container -->
            <div id="slider-mobile" class="slide-container flex-grow flex overflow-x-auto w-full h-full">
                <!-- Slide 1 -->
                <div class="slide-mobile flex-none w-full h-full flex flex-col items-center justify-center p-margin-mobile">
                    <div class="w-full max-w-sm aspect-square mb-stack-lg rounded-2xl shadow-sm border border-outline-variant/30 overflow-hidden">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDfrGCEqAhno07DAH8oWJPVCZHeIpWNMWbrYkHbtyXdeXqKLje3jFvfVeLS7OJp5sE5SAQoK7KmH5zItAlkxPz9Mfmp9QpTzb8sp481BEnc3NzOau2vt6iV5P3DCTpYF8MyU3tgwpnW8qfwoWyBAeIOuCoaewMDbR3A5omp8fMoBOWcPz5oDrCtwzktpd8FoKH9TW4OD8cjKVgoAzvvy2ijHcinhDnF_MuKanAT42-UPwmv6NqtZT6a"
                             alt="Booking Mudah" class="w-full h-full object-cover">
                    </div>
                    <div class="text-center max-w-xs mt-auto mb-stack-lg">
                        <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-brand-navy mb-stack-sm">Booking Mudah</h2>
                        <p class="font-body-md text-body-md text-on-surface-variant">Jadwalkan potong rambut Anda hanya dengan beberapa ketukan. Pilih layanan, barber favorit, dan waktu yang sesuai.</p>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="slide-mobile flex-none w-full h-full flex flex-col items-center justify-center p-margin-mobile">
                    <div class="w-full max-w-sm aspect-square mb-stack-lg rounded-2xl shadow-sm border border-outline-variant/30 overflow-hidden">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDH7ALku1h0_vzoUQ_roqjEQCqT_ZcA7IxfswRNwYlWr-EeyPco-AqjwqtVs8mcqNGXjhUVazEl6Iz2NGyeMDQAcRcaIyAzcnXaMhdWFv8wsTkaWUgSgCk4WvXeKS-PFfEUnROGX0MPqasT0NYiRF9z4D_MUem4oMShILxixTWGct89iUz7EeArCnKbym4KhydZ0R0-EwZJwAKA8zHv7VLATmhMnxjkTiCifQ0wcNOuiCyfcHMi8ilk"
                             alt="Antrean Real-Time" class="w-full h-full object-cover">
                    </div>
                    <div class="text-center max-w-xs mt-auto mb-stack-lg">
                        <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-brand-navy mb-stack-sm">Antrean Real-Time</h2>
                        <p class="font-body-md text-body-md text-on-surface-variant">Tidak perlu lagi menunggu lama. Pantau status antrean Anda secara langsung dan datang tepat waktu.</p>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="slide-mobile flex-none w-full h-full flex flex-col items-center justify-center p-margin-mobile">
                    <div class="w-full max-w-sm aspect-square mb-stack-lg rounded-2xl shadow-sm border border-outline-variant/30 overflow-hidden">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBNCmaSCVe-kNcaoPv5gC622eFJddL-Maz1Ohaa8zCTCWwB6lW31DQNRqn1flNWpJDhmmXuW_ELe0f2A6iRoBBd_agBV2O5I44bTg2t_rI1eT3Yx0sEJRGX3Nrw5rbQZGIpebUR2OQoOH0MPxTH1Adfz4s_H8adrdFBb4nWmurgAt67gZdZGBJ7dRNinszzWr2TF_iJHs5tzTYWua-YiJLhovspAjzs9H8G0poZIFQR7XEwKYGZedkS"
                             alt="Pembayaran Online" class="w-full h-full object-cover">
                    </div>
                    <div class="text-center max-w-xs mt-auto mb-stack-lg">
                        <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-brand-navy mb-stack-sm">Pembayaran Online</h2>
                        <p class="font-body-md text-body-md text-on-surface-variant">Transaksi aman dan praktis. Nikmati kemudahan pembayaran tanpa uang tunai langsung dari aplikasi.</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Controls -->
            <div class="w-full px-margin-mobile pb-stack-lg pt-stack-sm flex flex-col items-center gap-stack-md shrink-0 bg-surface">
                <div class="flex space-x-2" id="dots-mobile">
                    <div class="dot h-2 w-6 rounded-full bg-brand-gold" data-index="0"></div>
                    <div class="dot h-2 w-2 rounded-full bg-outline-variant" data-index="1"></div>
                    <div class="dot h-2 w-2 rounded-full bg-outline-variant" data-index="2"></div>
                </div>
                <button id="action-btn-mobile" class="w-full max-w-xs bg-brand-gold text-brand-navy font-headline-sm text-headline-sm py-4 rounded-xl brand-shadow hover:brightness-110 transition-all flex items-center justify-center gap-2">
                    <span>SELANJUTNYA</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>
        </div>

        {{-- DESKTOP VERSION (visible on md and up) --}}
        <div class="hidden md:block h-full w-full">
            <main class="relative w-full h-full bg-surface overflow-hidden">
                {{-- Background Image Fullscreen --}}
                <div class="absolute inset-0 z-0"
                     style="background-image: url('https://lh3.googleusercontent.com/aida/AP1WRLsomBgzFWg_25Nen7gKYB70Osr2tj384pP6CUd3hjQrUG8oaSD5vAabcV-kMOolV2etl7WHbSp4VgC6_E5mEcwThrZWA73m5tRIjhA4YVE3pKh_PfrSryIxFyXHKQ0Qq2mg13NxmTunZ73b_sdNesYp1I_pkr8mcbVdrr1CAifFTUrnbh34-ygrtgRBsxq0QVvPT85hkry8kUnDcAMxW3EmPfPD0uecMbdeX_5TPxY55cozP4-u7PPU5A');
                      background-size: cover;
                      background-position: center;">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/40 to-primary/20 backdrop-blur-[1px]"></div>
                </div>

                {{-- Konten di atas background --}}
                <section class="relative z-10 w-full h-full flex flex-col justify-center px-margin-mobile md:px-margin-desktop py-stack-lg">
                    {{-- Header: Logo + Skip --}}
                    <header class="mb-stack-lg shrink-0 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-on-primary">content_cut</span>
                            <h1 class="font-headline-md text-headline-md font-bold tracking-tight text-on-primary">Barber Woi</h1>
                        </div>
                        <button id="skip-btn-desktop" class="text-on-primary/80 font-body-sm hover:text-brand-gold transition-colors">
                            Lewati
                        </button>
                    </header>

                    {{-- Slider Area --}}
                    <div class="flex-1 flex flex-col justify-center max-w-md w-full overflow-hidden">
                        {{-- Slides --}}
                        <div class="flex slide-track w-full" id="slider-desktop">
                            <article class="w-full flex-none shrink-0 pr-4">
                                <h2 class="font-display-lg text-display-lg text-on-primary mb-stack-md">Booking Mudah.</h2>
                                <p class="font-body-lg text-body-lg text-primary-fixed-dim leading-relaxed">
                                    Jadwalkan potong rambut Anda hanya dengan beberapa ketukan. Pilih layanan, barber favorit, dan waktu yang sesuai.
                                </p>
                            </article>
                            <article class="w-full flex-none shrink-0 pr-4">
                                <h2 class="font-display-lg text-display-lg text-on-primary mb-stack-md">Antrean Real-Time.</h2>
                                <p class="font-body-lg text-body-lg text-primary-fixed-dim leading-relaxed">
                                    Tidak perlu lagi menunggu lama. Pantau status antrean Anda secara langsung dan datang tepat waktu.
                                </p>
                            </article>
                            <article class="w-full flex-none shrink-0 pr-4">
                                <h2 class="font-display-lg text-display-lg text-on-primary mb-stack-md">Pembayaran Online.</h2>
                                <p class="font-body-lg text-body-lg text-primary-fixed-dim leading-relaxed">
                                    Transaksi aman dan praktis. Nikmati kemudahan pembayaran tanpa uang tunai langsung dari aplikasi.
                                </p>
                            </article>
                        </div>

                        {{-- Dots --}}
                        <div class="flex items-center gap-2 mt-stack-lg mb-stack-md" id="dots-desktop">
                            <button class="dot h-2 w-6 rounded-full bg-brand-gold transition-all" data-index="0"></button>
                            <button class="dot h-2 w-2 rounded-full bg-outline-variant transition-all" data-index="1"></button>
                            <button class="dot h-2 w-2 rounded-full bg-outline-variant transition-all" data-index="2"></button>
                        </div>

                        {{-- CTA Button --}}
                        <button id="action-btn-desktop"
                                class="w-full py-4 px-6 bg-brand-gold text-brand-navy font-headline-sm text-headline-sm rounded-xl brand-shadow hover:brightness-110 transition-all duration-300 transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <span>Selanjutnya</span>
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </button>

                        {{-- Link Masuk --}}
                        <div class="flex items-center justify-center gap-2 mt-stack-md">
                            <span class="font-body-sm text-body-sm text-primary-fixed-dim">Sudah punya akun?</span>
                            <a href="{{ route('login') }}" class="font-headline-sm text-headline-sm text-brand-gold hover:text-secondary-fixed transition-colors">Masuk</a>
                        </div>
                    </div>
                </section>
            </main>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Splash screen
            const splashScreen = document.getElementById('splash-screen');
            const mainContent = document.getElementById('main-content');

            setTimeout(() => {
                splashScreen.classList.add('opacity-0');
                setTimeout(() => {
                    splashScreen.classList.add('hidden');
                    mainContent.classList.remove('opacity-0');
                }, 700);
            }, 2000);

            // Mobile elements
            const sliderMobile = document.getElementById('slider-mobile');
            const dotsMobile = document.querySelectorAll('#dots-mobile .dot');
            const actionBtnMobile = document.getElementById('action-btn-mobile');
            const skipBtnMobile = document.getElementById('skip-btn-mobile');
            const btnTextMobile = actionBtnMobile?.querySelector('span:first-child');

            // Desktop elements
            const sliderDesktop = document.getElementById('slider-desktop');
            const dotsDesktop = document.querySelectorAll('#dots-desktop .dot');
            const actionBtnDesktop = document.getElementById('action-btn-desktop');
            const skipBtnDesktop = document.getElementById('skip-btn-desktop');
            const btnTextDesktop = actionBtnDesktop?.querySelector('span:first-child');

            let currentSlideMobile = 0;
            let currentSlideDesktop = 0;
            const totalSlides = 3;

            // --- Mobile Slider Logic ---
            if (sliderMobile) {
                const updateMobile = (index) => {
                    currentSlideMobile = index;
                    dotsMobile.forEach((dot, i) => {
                        if (i === index) {
                            dot.classList.replace('bg-outline-variant', 'bg-brand-gold');
                            dot.classList.replace('w-2', 'w-6');
                        } else {
                            dot.classList.replace('bg-brand-gold', 'bg-outline-variant');
                            dot.classList.replace('w-6', 'w-2');
                        }
                    });
                    if (btnTextMobile) {
                        btnTextMobile.textContent = index === totalSlides - 1 ? 'MULAI SEKARANG' : 'SELANJUTNYA';
                    }
                };

                sliderMobile.addEventListener('scroll', () => {
                    const scrollLeft = sliderMobile.scrollLeft;
                    const slideWidth = sliderMobile.clientWidth;
                    const newIndex = Math.round(scrollLeft / slideWidth);
                    if (newIndex !== currentSlideMobile) {
                        updateMobile(newIndex);
                    }
                });

                actionBtnMobile.addEventListener('click', () => {
                    if (currentSlideMobile < totalSlides - 1) {
                        sliderMobile.scrollTo({ left: (currentSlideMobile + 1) * sliderMobile.clientWidth, behavior: 'smooth' });
                    } else {
                        window.location.href = '{{ route("login") }}';
                    }
                });

                skipBtnMobile.addEventListener('click', () => window.location.href = '{{ route("login") }}');

                dotsMobile.forEach((dot, i) => {
                    dot.addEventListener('click', () => {
                        sliderMobile.scrollTo({ left: i * sliderMobile.clientWidth, behavior: 'smooth' });
                    });
                    dot.style.cursor = 'pointer';
                });
            }

            // --- Desktop Slider Logic ---
            if (sliderDesktop) {
                const updateDesktop = (index) => {
                    currentSlideDesktop = index;
                    sliderDesktop.style.transform = `translateX(-${index * 100}%)`;
                    dotsDesktop.forEach((dot, i) => {
                        if (i === index) {
                            dot.classList.replace('bg-outline-variant', 'bg-brand-gold');
                            dot.classList.replace('w-2', 'w-6');
                        } else {
                            dot.classList.replace('bg-brand-gold', 'bg-outline-variant');
                            dot.classList.replace('w-6', 'w-2');
                        }
                    });
                    if (btnTextDesktop) {
                        btnTextDesktop.textContent = index === totalSlides - 1 ? 'Mulai Sekarang' : 'Selanjutnya';
                    }
                };

                actionBtnDesktop.addEventListener('click', () => {
                    if (currentSlideDesktop < totalSlides - 1) {
                        updateDesktop(currentSlideDesktop + 1);
                    } else {
                        window.location.href = '{{ route("login") }}';
                    }
                });

                skipBtnDesktop.addEventListener('click', () => window.location.href = '{{ route("login") }}');

                dotsDesktop.forEach((dot, i) => {
                    dot.addEventListener('click', () => {
                        updateDesktop(i);
                    });
                    dot.style.cursor = 'pointer';
                });
            }
        });
    </script>
</body>
</html>