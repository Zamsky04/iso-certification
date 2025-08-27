<x-layouts.app :title="$title" :description="$description">
    @push('head')
        <link rel="canonical" href="{{ url()->current() }}">
        <meta property="og:title" content="{{ $service->title }}">
        <meta property="og:description" content="{{ $description }}">
    @endpush>

    <!-- HERO -->
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-16 md:py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/20 to-cyan-900/20"></div>

        <!-- gunakan container responsif lebar -->
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumb -->
            <nav class="mb-6 md:mb-8 text-sm anim-in">
                <a href="{{ route('certifications.page') }}"
                   class="text-white/80 hover:text-white transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                    </svg>
                    Kembali ke Sertifikasi
                </a>
                <div class="flex items-center mt-2 text-white/60">
                    <span>Sertifikasi</span>
                    <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-white">{{ $service->title }}</span>
                </div>
            </nav>

            <!-- Headline -->
            <div class="text-center text-white anim-in">
                <h1 class="text-balance text-3xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight">
                    {{ $service->title }}
                </h1>
                <p class="text-pretty text-base md:text-xl text-white/90 max-w-3xl mx-auto leading-relaxed">
                    {{ $description }}
                </p>

                <!-- CTA Button -->
                <div class="mt-8">
                <a href="#pendaftaran" data-scroll class="btn-white hover-lift inline-flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Daftar Sekarang
                </a>
                </div>
            </div>
        </div>

        <!-- dekorasi dimatikan di mobile agar tidak ganggu -->
        <div class="hidden sm:block absolute top-10 right-10 w-32 h-32 bg-blue-500/10 rounded-full blur-xl animate-pulse"></div>
        <div class="hidden sm:block absolute bottom-10 left-10 w-24 h-24 bg-cyan-500/10 rounded-full blur-lg animate-pulse delay-300"></div>
        <div class="hidden sm:block absolute top-1/2 left-1/4 w-16 h-16 bg-white/5 rounded-full blur-md animate-bounce delay-500 motion-reduce:animate-none"></div>
    </section>

    <!-- MAIN -->
    <section class="py-14 md:py-16 bg-gray-50">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Quick Info Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10 md:mb-12 anim-in items-stretch">
                <div class="glass-morphism rounded-2xl p-6 hover-lift h-full">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-slate-500 font-medium">Kategori</div>
                            <div class="text-lg font-bold text-slate-900">{{ $service->category ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                <div class="glass-morphism rounded-2xl p-6 hover-lift h-full">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-accent-100 rounded-xl grid place-items-center">
                            <!-- ganti icon yang path-nya tidak terpotong -->
                            <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-slate-500 font-medium">Akreditasi</div>
                            <div class="text-lg font-bold text-slate-900">
                                {{ data_get($service, 'metadata.nama-akreditasi', '—') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-morphism rounded-2xl p-6 hover-lift h-full">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-slate-500 font-medium">Status</div>
                            <div class="text-lg font-bold text-green-600">Tersedia</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main -->
                <div class="lg:col-span-2 space-y-8">
                    @if(!empty($service->benefits))
                    <section class="bg-white rounded-2xl p-6 md:p-8 shadow-lg hover-lift anim-in">
                        <div class="flex items-center gap-3 mb-5 md:mb-6">
                            <div class="w-10 h-10 bg-primary-100 rounded-xl grid place-items-center">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl md:text-2xl font-bold text-slate-900">Manfaat &amp; Keuntungan</h3>
                        </div>

                        <!-- 2 kolom >= sm -->
                        <ul class="grid gap-3 md:gap-4 sm:grid-cols-2 anim-stagger">
                            @foreach((array) $service->benefits as $index => $benefit)
                                <li class="flex items-start gap-3 p-4 rounded-xl bg-primary-50 hover:bg-primary-100 transition-colors">
                                    <div class="w-6 h-6 bg-primary-600 rounded-full grid place-items-center flex-shrink-0 mt-0.5">
                                        <span class="text-white text-xs font-bold">{{ $index + 1 }}</span>
                                    </div>
                                    <p class="text-slate-700 leading-relaxed">{{ $benefit }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                    @endif

                    @if(!empty($service->requirements))
                    <section class="bg-white rounded-2xl p-6 md:p-8 shadow-lg hover-lift anim-in">
                        <div class="flex items-center gap-3 mb-5 md:mb-6">
                            <div class="w-10 h-10 bg-accent-100 rounded-xl grid place-items-center">
                                <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            <h3 class="text-xl md:text-2xl font-bold text-slate-900">Persyaratan</h3>
                        </div>

                        <ul class="grid gap-3 md:gap-4 sm:grid-cols-2">
                            @foreach((array) $service->requirements as $requirement)
                                <li class="flex items-start gap-3 p-4 rounded-xl bg-accent-50 hover:bg-accent-100 transition-colors">
                                    <svg class="w-5 h-5 text-accent-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <p class="text-slate-700 leading-relaxed">{{ $requirement }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                    @endif
                </div>

                <!-- Sidebar (non-sticky, full responsive) -->
                <aside id="pendaftaran" class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-lg anim-in border border-slate-100">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-2xl grid place-items-center mx-auto mb-4 shadow-lg">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <h4 class="text-lg md:text-xl font-bold mb-2 text-slate-900">Siap Memulai?</h4>
                            <p class="text-slate-600 mb-6 text-sm leading-relaxed">Dapatkan sertifikasi profesional yang diakui industri</p>

                            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-4 mb-4 border border-blue-100">
                                <div class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Investasi Terbaik</div>
                                <div class="text-2xl font-bold text-slate-900">Hubungi Kami</div>
                                <div class="text-xs text-slate-500">untuk informasi harga</div>
                            </div>

                            @php
                            $waNumber = '6285311065944'; // 62 = kode negara
                            $waMsg = rawurlencode(
                                "Halo admin,\n".
                                "Saya ingin mendaftar program: {$service->title}\n".
                                "Mohon informasi harga, jadwal terdekat, dan langkah pendaftaran.\n".
                                "Terima kasih."
                            );
                            $waUrl = "https://wa.me/{$waNumber}?text={$waMsg}";
                            @endphp

                            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                            class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 py-3 rounded-xl
                                    font-semibold hover:from-blue-700 hover:to-cyan-700 transition-all duration-300
                                    hover:shadow-lg hover:-translate-y-0.5 mb-3 inline-flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Daftar via WhatsApp
                            </a>

                            <a href="#mengapa" data-scroll class="w-full bg-slate-100 text-slate-700 px-6 py-3 rounded-xl font-semibold
                                    hover:bg-slate-200 transition-all duration-300 text-sm inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Konsultasi Gratis
                            </a>

                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-slate-50 to-blue-50 rounded-2xl p-6 anim-in border border-blue-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-xl grid place-items-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-slate-900">Mengapa Memilih Kami?</h4>
                        </div>

                        <div class="space-y-4 text-sm">
                            <div class="flex items-start gap-3 p-3 bg-white/70 rounded-lg">
                                <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-slate-700 font-medium">Instruktur Bersertifikat Internasional</span>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white/70 rounded-lg">
                                <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-slate-700 font-medium">Fleksibel &amp; Support 24/7</span>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white/70 rounded-lg">
                                <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-slate-700 font-medium">Garansi Sertifikat Resmi</span>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white/70 rounded-lg">
                                <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span class="text-slate-700 font-medium">Akses Materi Seumur Hidup</span>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-blue-200">
                            <div class="flex items-center justify-between text-xs text-slate-600">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="font-semibold">4.9/5</span>
                                </div>
                                <span>1000+ Alumni</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-gradient-to-r from-slate-900 via-blue-900 to-slate-900 py-14 md:py-16 relative overflow-hidden">
        <div id="mengapa" class="absolute inset-0 bg-gradient-to-br from-blue-600/10 to-cyan-600/10"></div>

        <!-- lebar responsif -->
        <div class="max-w-screen-lg mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-6 md:p-10 lg:p-12 anim-in border border-white/20 shadow-2xl">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-400/20 to-cyan-400/20 rounded-full grid place-items-center mx-auto mb-5 md:mb-6">
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>

                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-3 md:mb-4 text-balance">
                    Transformasi Karir Dimulai dari Sini
                </h2>
                <p class="text-white/90 text-base md:text-lg mb-6 md:mb-8 max-w-2xl mx-auto leading-relaxed text-pretty">
                    Bergabunglah dengan ribuan profesional yang telah meningkatkan karir dan penghasilan mereka melalui sertifikasi berkualitas internasional
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold text-white mb-1">1000+</div>
                        <div class="text-white/80 text-sm">Alumni Sukses</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold text-white mb-1">98%</div>
                        <div class="text-white/80 text-sm">Tingkat Kepuasan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl md:text-3xl font-bold text-white mb-1">24/7</div>
                        <div class="text-white/80 text-sm">Support System</div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center">
                    <a href="#pendaftaran" data-scroll
                    class="bg-white text-slate-900 px-6 md:px-8 py-3 md:py-4 rounded-xl font-semibold
                            hover:bg-gray-100 transition-all hover:scale-105 shadow-lg inline-flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                        Mulai Perjalanan Anda
                    </a>

                    @php
                    $waNumber = '6285311065944';
                    $waMsg = rawurlencode("Halo admin,\nSaya ingin konsultasi gratis terkait sertifikasi: {$service->title}\nMohon informasinya, terima kasih.");
                    $waUrl = "https://wa.me/{$waNumber}?text={$waMsg}";
                    @endphp

                    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                    class="border-2 border-white/30 text-white px-6 md:px-8 py-3 md:py-4 rounded-xl font-semibold
                            hover:bg-white/10 hover:border-white/50 transition-all backdrop-blur-sm inline-flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Konsultasi Gratis
                    </a>
                </div>
            </div>
        </div>

        <!-- dekorasi non-mobile -->
        <div class="hidden sm:block absolute top-20 right-20 w-40 h-40 bg-blue-400/10 rounded-full blur-2xl animate-pulse"></div>
        <div class="hidden sm:block absolute bottom-20 left-20 w-32 h-32 bg-cyan-400/10 rounded-full blur-xl animate-pulse delay-500"></div>
    </section>
</x-layouts.app>
