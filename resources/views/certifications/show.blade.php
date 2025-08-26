<x-layouts.app :title="$title" :description="$description">
    @push('head')
        <link rel="canonical" href="{{ url()->current() }}">
        <meta property="og:title" content="{{ $service->title }}">
        <meta property="og:description" content="{{ $description }}">
        @if($service->image_url)<meta property="og:image" content="{{ $service->image_url }}">@endif
    @endpush>

    <!-- Hero Section with Gradient Background -->
    <section class="gradient-bg py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="max-w-4xl mx-auto px-4 relative z-10">
            <!-- Breadcrumb Navigation -->
            <nav class="mb-8 text-sm anim-in">
                <a href="{{ route('certifications.page') }}" class="text-white/80 hover:text-white transition-colors flex items-center gap-2">
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

            <!-- Hero Content -->
            <div class="text-center text-white anim-in">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    {{ $service->title }}
                </h1>
                <p class="text-xl md:text-2xl text-white/90 max-w-3xl mx-auto leading-relaxed">
                    {{ $description }}
                </p>

                <!-- CTA Button -->
                <div class="mt-8">
                    <button class="btn-white hover-lift inline-flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Daftar Sekarang
                    </button>
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-10 right-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
        <div class="absolute bottom-10 left-10 w-24 h-24 bg-white/10 rounded-full blur-lg"></div>
    </section>

    <!-- Main Content Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Quick Info Cards -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12 anim-in">
                <div class="glass-morphism rounded-2xl p-6 hover-lift">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
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

                <div class="glass-morphism rounded-2xl p-6 hover-lift">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-slate-500 font-medium">Akreditasi</div>
                            <div class="text-lg font-bold text-slate-900">{{ $service->metadata['nama-akreditasi'] ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                <div class="glass-morphism rounded-2xl p-6 hover-lift sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
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
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    @if($service->benefits)
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover-lift anim-in">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900">Manfaat & Keuntungan</h3>
                        </div>
                        <div class="space-y-4">
                            @foreach($service->benefits as $index => $benefit)
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-primary-50 hover:bg-primary-100 transition-colors">
                                <div class="w-6 h-6 bg-primary-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-white text-xs font-bold">{{ $index + 1 }}</span>
                                </div>
                                <p class="text-slate-700 leading-relaxed">{{ $benefit }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($service->requirements)
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover-lift anim-in">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-accent-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900">Persyaratan</h3>
                        </div>
                        <div class="space-y-4">
                            @foreach($service->requirements as $requirement)
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-accent-50 hover:bg-accent-100 transition-colors">
                                <svg class="w-5 h-5 text-accent-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <p class="text-slate-700 leading-relaxed">{{ $requirement }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Action Card -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg sticky top-8 anim-in">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold mb-2 text-slate-900">Siap Memulai?</h4>
                            <p class="text-slate-600 mb-6 text-sm">Dapatkan sertifikasi profesional yang diakui industri</p>
                            <button class="btn-primary w-full mb-3 hover-lift">
                                Daftar Sekarang
                            </button>
                            <button class="btn-ghost w-full text-sm">
                                Konsultasi Gratis
                            </button>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="bg-gradient-to-br from-primary-50 to-accent-50 rounded-2xl p-6 anim-in">
                        <h4 class="font-bold mb-4 text-slate-900">Informasi Tambahan</h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-slate-600">Dukungan 24/7</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-slate-600">Garansi Sertifikat</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span class="text-slate-600">Akses Seumur Hidup</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="gradient-bg py-16">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <div class="bg-white/10 backdrop-filter backdrop-blur-lg rounded-3xl p-8 lg:p-12 anim-in">
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">
                    Tingkatkan Karir Anda Hari Ini
                </h2>
                <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
                    Bergabunglah dengan ribuan profesional yang telah meningkatkan karir mereka melalui program sertifikasi kami
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button class="btn-white hover-lift">
                        Mulai Sekarang
                    </button>
                    <button class="btn-outline-white hover-lift">
                        Pelajari Lebih Lanjut
                    </button>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
