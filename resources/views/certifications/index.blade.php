<x-layouts.app :title="'ISO Certification Hub - Platform Sertifikasi ISO Terpercaya'">
    {{-- NAV --}}
    <nav class="fixed top-0 w-full z-50 glass-morphism">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-primary-500 to-accent-500 rounded-lg grid place-items-center">
                        <i class="fas fa-certificate text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-800">ISO Certification Hub</span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="nav-link">Beranda</a>
                    <a href="#certifications" class="nav-link">Sertifikasi</a>
                    <a href="#categories" class="nav-link">Kategori</a>
                    <a href="#about" class="nav-link">Tentang</a>
                    <button class="btn-primary">
                        Konsultasi
                    </button>
                </div>

                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-700" aria-label="Toggle mobile menu">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Simple mobile dropdown (toggle via JS) --}}
        <div id="mobile-menu" class="md:hidden hidden border-t bg-white/95 backdrop-blur">
            <div class="px-4 py-3 space-y-2">
                <a href="#home" class="block nav-link">Beranda</a>
                <a href="#certifications" class="block nav-link">Sertifikasi</a>
                <a href="#categories" class="block nav-link">Kategori</a>
                <a href="#about" class="block nav-link">Tentang</a>
                <button class="btn-primary w-full">Konsultasi</button>
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <section id="home" class="pt-16 gradient-bg min-h-screen grid place-items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center anim-in">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                    Platform Sertifikasi ISO
                    <span class="block text-accent-200">Terpercaya Indonesia</span>
                </h1>
                <p class="text-xl text-white/90 mb-8 max-w-3xl mx-auto">
                    Temukan dan dapatkan sertifikasi ISO yang sesuai dengan kebutuhan bisnis Anda.
                    Ribuan pilihan sertifikasi dengan standar internasional terbaik.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button data-scroll="#certifications" class="btn-white">
                        <i class="fas fa-search mr-2"></i>
                        Cari Sertifikasi
                    </button>
                    <button class="btn-outline-white">
                        <i class="fas fa-play mr-2"></i>
                        Pelajari Lebih Lanjut
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- SEARCH & FILTER --}}
    <section id="certifications" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Search --}}
            <div class="max-w-4xl mx-auto mb-12">
            <div class="relative">
                <input id="searchInput" type="search" placeholder="Cari ISO 9001, KAN, IAF, ..."
                class="w-full px-5 py-3 pl-12 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:outline-none">
                <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none">
                <path d="M21 21l-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            </div>

            {{-- Panel filter --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 sticky top-4 z-20">
            <div class="grid gap-5">
                <div>
                <div class="text-xs font-semibold text-slate-500 mb-2">Filter Aktif</div>
                <div id="activeFilters" class="flex flex-wrap gap-2"></div>
                </div>

                <div>
                <div class="text-xs font-semibold text-slate-500 mb-2">Kategori</div>
                <div id="catWrap"></div>
                </div>

                <div>
                <div class="text-xs font-semibold text-slate-500 mb-2">Detail</div>
                <div id="subWrap"></div>
                </div>

                <div class="flex items-center gap-3">
                <div id="resultCount" class="text-sm text-slate-600"></div>
                <button id="resetFiltersBtn" class="ml-auto px-3 py-2 text-sm rounded-md border border-slate-300 hover:border-slate-400">Reset</button>
                </div>
            </div>
            </div>

            {{-- GRID (SSR page-1 → crawlable). JS akan meng-overwrite saat interaksi) --}}
            <div id="certificationGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            @foreach($initial as $c)
                <article class="group bg-white rounded-xl border border-slate-200 p-5">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-blue-700">
                    {{ $c->metadata['jenis-iso'] ?? 'ISO' }}
                </div>
                <h3 class="mt-1 text-lg md:text-xl font-bold text-slate-900 leading-snug">
                    <a href="{{ route('certifications.show', $c) }}" class="hover:underline">{{ $c->title }}</a>
                </h3>
                <p class="mt-2 text-sm text-slate-600 line-clamp-3">{{ $c->short_description ?? \Illuminate\Support\Str::limit($c->description, 140) }}</p>
                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                    @if($c->category)<span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700">#{{ $c->category }}</span>@endif
                    @if(($c->metadata['nama-akreditasi'] ?? '') !== '')
                    <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700">{{ $c->metadata['nama-akreditasi'] }}</span>
                    @endif
                </div>
                <div class="mt-5 flex items-center justify-end">
                    <a href="{{ route('certifications.show', $c) }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Detail</a>
                </div>
                </article>
            @endforeach
            </div>

            {{-- PAGINATION (SSR fallback) --}}
            <div class="text-center mt-10">
            {{ $initial->onEachSide(1)->links() }}
            </div>

            {{-- Data untuk bootstrap JS (opsional) --}}
            <script>
            window.__CATS__ = @json($categories ?? []);
            </script>
        </div>
        </section>

    {{-- CATEGORIES --}}
    <section id="categories" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Kategori Sertifikasi Populer</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Jelajahi kategori berdasarkan industri dan kebutuhan spesifik.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @php
                    $cats = [
                        ['icon'=>'fa-cogs','color'=>'blue','title'=>'Quality Management','desc'=>'Standar manajemen kualitas untuk efisiensi operasional','count'=>'25+ Sertifikasi'],
                        ['icon'=>'fa-leaf','color'=>'green','title'=>'Environmental','desc'=>'Manajemen lingkungan & keberlanjutan','count'=>'18+ Sertifikasi'],
                        ['icon'=>'fa-shield-alt','color'=>'red','title'=>'Safety & Health','desc'=>'K3 internasional untuk keselamatan kerja','count'=>'22+ Sertifikasi'],
                        ['icon'=>'fa-lock','color'=>'purple','title'=>'Information Security','desc'=>'Keamanan informasi & manajemen data','count'=>'15+ Sertifikasi'],
                    ];
                @endphp
                @foreach($cats as $c)
                <div class="category-card bg-white p-8 rounded-xl shadow-lg hover-lift text-center">
                    <div class="w-16 h-16 bg-{{ $c['color'] }}-100 rounded-full grid place-items-center mx-auto mb-6">
                        <i class="fas {{ $c['icon'] }} text-{{ $c['color'] }}-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">{{ $c['title'] }}</h3>
                    <p class="text-gray-600 mb-5">{{ $c['desc'] }}</p>
                    <div class="text-2xl font-bold text-{{ $c['color'] }}-600 mb-4">{{ $c['count'] }}</div>
                    <button class="text-{{ $c['color'] }}-600 font-semibold hover:text-{{ $c['color'] }}-700 transition-colors">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- STATS --}}
    <section class="py-20 bg-primary-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
                <div><div class="text-4xl font-bold mb-2">1000+</div><div class="text-primary-200">Sertifikasi Tersedia</div></div>
                <div><div class="text-4xl font-bold mb-2">50K+</div><div class="text-primary-200">Perusahaan Terdaftar</div></div>
                <div><div class="text-4xl font-bold mb-2">25+</div><div class="text-primary-200">Kategori Industri</div></div>
                <div><div class="text-4xl font-bold mb-2">99%</div><div class="text-primary-200">Tingkat Kepuasan</div></div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-gray-800 text-white py-16" id="about">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-primary-500 to-accent-500 rounded-lg grid place-items-center">
                            <i class="fas fa-certificate text-white"></i>
                        </div>
                        <span class="text-xl font-bold">ISO Certification Hub</span>
                    </div>
                    <p class="text-gray-300 mb-6">Platform terpercaya untuk sertifikasi ISO standar internasional.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f text-xl"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter text-xl"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in text-xl"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram text-xl"></i></a>
                    </div>
                </div>

                <div>
                    <h3 class="footer-title">Sertifikasi Populer</h3>
                    <ul class="footer-list">
                        <li><a href="#" class="footer-link">ISO 9001 - Quality Management</a></li>
                        <li><a href="#" class="footer-link">ISO 14001 - Environmental</a></li>
                        <li><a href="#" class="footer-link">ISO 45001 - Safety</a></li>
                        <li><a href="#" class="footer-link">ISO 27001 - Information Security</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="footer-title">Layanan</h3>
                    <ul class="footer-list">
                        <li><a href="#" class="footer-link">Konsultasi Gratis</a></li>
                        <li><a href="#" class="footer-link">Training & Workshop</a></li>
                        <li><a href="#" class="footer-link">Assessment</a></li>
                        <li><a href="#" class="footer-link">Sertifikasi</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="footer-title">Kontak</h3>
                    <div class="space-y-3">
                        <div class="footer-contact"><i class="fas fa-map-marker-alt text-primary-400"></i><span>Jakarta, Indonesia</span></div>
                        <div class="footer-contact"><i class="fas fa-phone text-primary-400"></i><span>+62 21 1234 5678</span></div>
                        <div class="footer-contact"><i class="fas fa-envelope text-primary-400"></i><span>info@isohub.id</span></div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-12 pt-8 text-center">
                <p class="text-gray-300">© {{ date('Y') }} ISO Certification Hub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- MODAL --}}
    <div id="certificationModal" class="fixed inset-0 bg-black/50 hidden z-50 p-4 md:p-8">
        <div class="relative max-w-3xl mx-auto bg-white rounded-2xl p-6 md:p-8">
        <button class="absolute top-3 right-3 p-2 rounded hover:bg-slate-100" onclick="closeModal()" aria-label="Tutup">
            ✕
        </button>
        <div id="modalContent"></div>
        </div>
    </div>
</x-layouts.app>
