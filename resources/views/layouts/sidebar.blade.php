<!-- Sidebar Backdrop for Mobile/Tablet -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Sidebar Component -->
<aside class="sidebar" id="sidebar">
    @php
        $satkernama = session('satkernama', 'Nama Satker');
        $idSatker = session('id_satker', 'ID Satker');
        $levelSakip = session('id_sakip_level', 0);
        $tahun = session('tahun_ui', date('Y'));

        // Format satkernama for Kejati/Kejari bidang users in sidebar
        $sidebarSatkerNama = $satkernama;
        $upperSatker = strtoupper(trim($satkernama));
        if (str_starts_with($upperSatker, 'ASISTEN ')) {
            $bidang = trim(substr($satkernama, 8));
            $sidebarSatkerNama = 'Bidang ' . ucwords(strtolower($bidang));
        } elseif (str_starts_with($upperSatker, 'KEPALA SEKSI ')) {
            $bidang = trim(substr($satkernama, 13));
            $sidebarSatkerNama = 'Bidang ' . ucwords(strtolower($bidang));
        } elseif (str_starts_with($upperSatker, 'KASI ')) {
            $bidang = trim(substr($satkernama, 5));
            $sidebarSatkerNama = 'Bidang ' . ucwords(strtolower($bidang));
        }

        // Cek apakah submenu harus dibuka
        $submenuActive =
            request()->is('kep') ||
            request()->is('perencanaan*') ||
            request()->is('pengukuran*') ||
            request()->is('pelaporan*') ||
            request()->is('evaluasi*') ||
            request()->is('lke/*');
    @endphp

    <!-- Sidebar Brand Header -->
    <div class="sidebar-header d-flex align-items-center justify-content-between px-3 py-3 border-bottom" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
        <div class="d-flex align-items-center gap-2 overflow-hidden">
            <div class="brand-logo-wrapper p-1 rounded-3 bg-white shadow-sm border" style="border-color: #e2e8f0 !important;">
                <img src="{{ asset('gambar/kejaksaan.png') }}" alt="Logo Kejaksaan" class="sidebar-logo" style="width: 34px; height: 34px; object-fit: contain;">
            </div>
            <div class="sidebar-brand-text overflow-hidden">
                <div class="d-flex align-items-center gap-1">
                    <span class="fw-bold text-dark text-truncate" style="font-size: 0.98rem; letter-spacing: 0.5px;">PROSAKIP</span>
                    <span class="badge bg-warning text-dark px-1 py-0" style="font-size: 0.65rem; font-weight: 700;">RI</span>
                </div>
                <small class="text-muted d-block text-truncate" style="font-size: 0.72rem; margin-top: -2px;">Kejaksaan Agung RI</small>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-close d-lg-none" id="sidebarCloseBtn" aria-label="Tutup Menu"></button>
    </div>

    <!-- User Information Card -->
    <div id="user-info" class="user-info-box px-3 py-3 text-center border-bottom" style="background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);">
        <div class="position-relative d-inline-block">
            <img src="{{ asset('gambar/kejaksaan.png') }}" alt="Profile Picture" class="profile-pic">
            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1" title="Online" style="width: 12px; height: 12px;"></span>
        </div>
        <div class="user-title mt-1">
            <div class="fw-bold text-dark text-truncate-2" title="{{ $sidebarSatkerNama }}" style="font-size: 0.85rem; line-height: 1.35;">
                {{ $sidebarSatkerNama }}
            </div>
            <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.72rem; font-weight: 600;">
                <i class="bi bi-shield-check text-success me-1"></i>ID: {{ $idSatker }}
            </span>
        </div>
    </div>

    <!-- Navigation Menu Items -->
    <div class="sidebar-nav-wrapper py-2">
        <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}" title="Beranda">
            <i class="bi bi-grid-fill"></i> <span class="sidebar-text">Beranda</span>
        </a>

        @if (auth()->check() || in_array($levelSakip, [99, 1, 2, 3, 4, 0]))
            <a href="#" id="toggle-submenu" class="toggle-btn {{ $submenuActive ? 'active' : '' }}" title="Tata Kelola AKIP">
                <i class="bi bi-kanban-fill"></i> <span class="sidebar-text">Tata Kelola AKIP</span>
                <i class="fas {{ $submenuActive ? 'fa-chevron-down' : 'fa-chevron-right' }} arrow-icon ms-auto"></i>
            </a>

            <div class="submenu" id="submenu" style="display: {{ $submenuActive ? 'block' : 'none' }};">
                <a href="{{ route('perencanaan') }}" class="{{ request()->is('perencanaan*') ? 'active' : '' }}" title="Perencanaan">
                    <i class="bi bi-calendar2-range-fill"></i> <span class="sidebar-text">Perencanaan</span>
                </a>

                {{-- Pengukuran untuk Admin, Kejati, Kejari, Cabjari, atau Kejagung (Level 1) --}}
                @if ($tahun != 2024 && ($levelSakip == 99 || in_array((int)$levelSakip, [1, 2, 3, 4]) || in_array($idSatker, ['admin', '999999'])))
                    @php
                        $pengukuranRoute = ((int)$levelSakip == 1) ? route('pengukuran-ikp') : route('pengukuran');
                    @endphp
                    <a href="{{ $pengukuranRoute }}" class="{{ request()->is('pengukuran*') ? 'active' : '' }}" title="Pengukuran">
                        <i class="bi bi-graph-up-arrow"></i> <span class="sidebar-text">Pengukuran</span>
                    </a>
                @endif

                @if ($levelSakip == 99 || $levelSakip == 1 || $levelSakip == 2 || $levelSakip == 3 || $levelSakip == 4)
                    <a href="{{ route('pelaporan') }}" class="{{ request()->is('pelaporan*') ? 'active' : '' }}" title="Pelaporan">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i> <span class="sidebar-text">Pelaporan</span>
                    </a>
                @endif

                <a href="{{ route('evaluasi') }}" class="{{ request()->is('evaluasi') ? 'active' : '' }}" title="Evaluasi">
                    <i class="bi bi-clipboard2-check-fill"></i> <span class="sidebar-text">Evaluasi</span>
                </a>

                {{-- Menu Penilaian LKE (Untuk Kejati/PM, JAMWAS, dan ADMIN) --}}
                @php
                    $isLkeAdmin = in_array((string)$idSatker, ['admin', '999999', '888881'])
                        || $levelSakip == 99
                        || str_contains(strtolower((string)$idSatker), 'admin');

                    $isLkeJamwas = (string)$idSatker === '419346'
                        || in_array((string)$idSatker, ['888882', 'Pengawasan', 'Panev'])
                        || str_contains(strtolower($satkernama), 'jam_bidang_pengawasan')
                        || str_contains(strtolower($satkernama), 'jamwas')
                        || (str_contains(strtolower($satkernama), 'pengawasan') && !str_contains(strtolower($satkernama), 'kejati') && !str_starts_with(strtolower((string)$idSatker), 'was'));

                    $centralIds = ['005016', '419345', '417023', '418326', '419344', '677111', '691270', '666405'];

                    $isLkeKejati = ((int)$levelSakip == 2 || str_contains(strtolower($satkernama), 'kejati')) && !$isLkeJamwas;

                    $isLkePm = in_array((int)$levelSakip, [3, 4])
                        || in_array((string)$idSatker, $centralIds)
                        || str_contains(strtolower($satkernama), 'kejari')
                        || str_contains(strtolower($satkernama), 'cabjari');

                    $canEvaluateLke = $isLkeAdmin || $isLkeJamwas || $isLkeKejati || $isLkePm;
                @endphp
                @if ($canEvaluateLke)
                    @php
                        if ($isLkeAdmin || $isLkeJamwas) {
                            $lkeMenuTitle = 'Evaluasi LKE';
                        } elseif ($isLkeKejati) {
                            $lkeMenuTitle = 'Penilaian Mandiri & Evaluasi';
                        } else {
                            $lkeMenuTitle = 'Penilaian Mandiri (PM)';
                        }
                    @endphp
                    <a href="{{ route('lke.evaluasi.index') }}" class="{{ request()->is('lke/evaluasi*') ? 'active' : '' }}" title="{{ $lkeMenuTitle }}">
                        <i class="bi bi-check2-all text-warning"></i> 
                        <span class="sidebar-text">
                            {{ $lkeMenuTitle }}
                        </span>
                    </a>
                @endif

                @if ($levelSakip == 99 || in_array($idSatker, ['admin', '999999']))
                    @php
                        $isMappingActive = request()->is('lke/evidence-mapping*') || request()->is('lke/master-bukti*');
                    @endphp
                    <a href="#mappingBuktiMenu" data-bs-toggle="collapse" class="dropdown-toggle {{ $isMappingActive ? '' : 'collapsed' }}" title="Mapping Bukti LKE (Admin)">
                        <i class="bi bi-diagram-3-fill text-info"></i> <span class="sidebar-text">Mapping Bukti (Admin)</span>
                    </a>
                    <div class="collapse {{ $isMappingActive ? 'show' : '' }}" id="mappingBuktiMenu">
                        <a href="{{ route('lke.evidence_mapping.index') }}" class="{{ request()->routeIs('lke.evidence_mapping.index') ? 'active' : '' }} ps-4">
                            <i class="bi bi-link"></i> <span class="sidebar-text">Mapping LKE</span>
                        </a>
                        <a href="{{ route('lke.master_bukti.index') }}" class="{{ request()->routeIs('lke.master_bukti.index') ? 'active' : '' }} ps-4">
                            <i class="bi bi-file-earmark-text"></i> <span class="sidebar-text">Master Bukti Dukung</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif

        @if ($levelSakip == 99 || $levelSakip == 0 || $levelSakip == 2)
            <a href="{{ route('sakipwil') }}" class="{{ request()->is('sakipwil') ? 'active' : '' }}" title="SAKIP Wilayah">
                <i class="bi bi-globe2"></i> <span class="sidebar-text">SAKIP Wilayah</span>
            </a>
            <a href="{{ route('monitoring') }}" class="{{ request()->is('monitoring') ? 'active' : '' }}" title="Monitoring">
                <i class="bi bi-display-fill"></i> <span class="sidebar-text">Monitoring</span>
            </a>
        @endif

        @if ($levelSakip == 99)
            <a href="{{ route('kep') }}" class="{{ request()->is('kep') ? 'active' : '' }}" title="Kep Tim SAKIP">
                <i class="bi bi-people-fill"></i> <span class="sidebar-text">Kep Tim SAKIP</span>
            </a>
            <a href="{{ route('sakipvalidasi') }}" class="{{ request()->is('sakipvalidasi') ? 'active' : '' }}" title="SAKIP Validasi">
                <i class="bi bi-patch-check-fill"></i> <span class="sidebar-text">SAKIP Validasi</span>
            </a>
            <a href="{{ route('chatsupport') }}" class="{{ request()->is('chatsupport') ? 'active' : '' }}" title="Chat Support">
                <i class="bi bi-chat-dots-fill"></i> <span class="sidebar-text">Chat Support</span>
            </a>
            <a href="{{ route('pengumuman') }}" class="{{ request()->is('pengumuman*') ? 'active' : '' }}" title="Pengumuman">
                <i class="bi bi-megaphone-fill"></i> <span class="sidebar-text">Pengumuman</span>
            </a>
            <a href="{{ route('keloladata') }}" class="{{ request()->is('keloladata') ? 'active' : '' }}" title="Kelola Data">
                <i class="bi bi-database-fill-gear"></i> <span class="sidebar-text">Kelola Data</span>
            </a>
            @if (in_array($idSatker, ['admin', '999999']) || $levelSakip == 99)
                <a href="{{ route('dokumen-sakip.index') }}" class="{{ request()->is('keloladata/dokumen-sakip*') ? 'active' : '' }}" title="Dokumen SAKIP">
                    <i class="bi bi-file-earmark-text-fill text-info"></i> <span class="sidebar-text text-info fw-semibold">Dokumen SAKIP</span>
                </a>
                <a href="{{ Route::has('hapustahun.index') ? route('hapustahun.index') : url('/keloladata/hapus-tahun') }}" class="{{ request()->is('keloladata/hapus-tahun*') ? 'active' : '' }}" title="Hapus Data Tahun">
                    <i class="bi bi-trash3-fill text-danger"></i> <span class="sidebar-text text-danger fw-semibold">Hapus Data Tahun</span>
                </a>
                <a href="{{ Route::has('restoretahun.index') ? route('restoretahun.index') : url('/keloladata/restore-tahun') }}" class="{{ request()->is('keloladata/restore-tahun*') ? 'active' : '' }}" title="Restore File Tahun">
                    <i class="bi bi-cloud-arrow-up-fill text-success"></i> <span class="sidebar-text text-success fw-semibold">Restore File Tahun</span>
                </a>
                <a href="{{ Route::has('backuptahun.index') ? route('backuptahun.index') : url('/keloladata/backup-tahun') }}" class="{{ request()->is('keloladata/backup-tahun*') ? 'active' : '' }}" title="Backup File Tahun">
                    <i class="bi bi-cloud-arrow-down-fill text-primary"></i> <span class="sidebar-text text-primary fw-semibold">Backup File Tahun</span>
                </a>
            @endif
            <a href="{{ route('ubahpassword') }}" class="{{ request()->is('ubahpassword') ? 'active' : '' }}" title="Ubah Password">
                <i class="bi bi-key-fill"></i> <span class="sidebar-text">Ubah Password</span>
            </a>
        @endif

        @if ($levelSakip == 99 || $levelSakip == 1 || $levelSakip == 2 || $levelSakip == 3 || $levelSakip == 4)
            <a href="{{ route('aturan') }}" class="{{ request()->is('aturan*') ? 'active' : '' }}" title="Sumber Aturan">
                <i class="bi bi-book-half"></i> <span class="sidebar-text">Sumber Aturan</span>
            </a>
            <a href="{{ route('faq') }}" class="{{ request()->is('faq') ? 'active' : '' }}" title="FAQ">
                <i class="bi bi-question-circle-fill"></i> <span class="sidebar-text">FAQ</span>
            </a>
        @endif
    </div>
</aside>

<!-- Sidebar Toggler Floating Button (Desktop Only) -->
<button class="btn btn-yellow toggler-btn shadow-sm d-none d-lg-flex align-items-center justify-content-center" id="toggler-btn" type="button" title="Kecilkan / Perbesar Sidebar">
    <i class="fas fa-chevron-left"></i>
</button>

<!-- Top Navbar -->
<nav class="navbar navbar-light bg-white border-bottom sticky-top" id="navbar" style="box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
    <div class="container-fluid px-3 d-flex align-items-center justify-content-between">
        
        <!-- Left: Hamburger Toggle & Satker Name Pill -->
        <div class="d-flex align-items-center gap-2 overflow-hidden flex-grow-1 me-2">
            <button class="btn btn-light border d-inline-flex align-items-center justify-content-center flex-shrink-0" 
                    id="navbarToggleBtn" 
                    type="button" 
                    title="Buka / Tutup Menu"
                    style="width: 38px; height: 38px; border-radius: 9px;">
                <i class="bi bi-list fs-5 text-dark"></i>
            </button>

            <div class="satker-info-pill d-flex align-items-center overflow-hidden">
                <span class="badge bg-light text-dark border px-2 py-1_5 text-truncate" 
                      style="font-size: 0.82rem; font-weight: 600; max-width: 380px; border-radius: 8px;" 
                      title="{{ $satkernama }}">
                    <i class="bi bi-building-fill text-warning me-1"></i>
                    <span class="satker-label-text">{{ $satkernama }}</span>
                </span>
            </div>
        </div>

        <!-- Right: Year Selector & Logout Button -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <!-- Year Selector Form -->
            <form action="{{ route('pilih.tahun') }}" method="POST" id="tahunForm" class="d-flex align-items-center m-0">
                @csrf
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted border-end-0 d-none d-sm-flex py-1 px-2" style="font-size: 0.82rem;">
                        <i class="bi bi-calendar3"></i>
                    </span>
                    <select name="tahun" id="tahun" class="form-select form-select-sm" 
                            style="font-size: 0.82rem; font-weight: 600; min-width: 95px; border-radius: 0.375rem;" 
                            onchange="document.getElementById('tahunForm').submit();"
                            title="Pilih Tahun Anggaran">
                        @for ($i = 2024; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ session('tahun_ui') == (string)$i ? 'selected' : '' }}>
                                Tahun {{ $i }}
                            </option>
                            @if ($i == 2025)
                                <option value="20254" {{ session('tahun_ui') == '20254' ? 'selected' : '' }}>
                                    2025 TW IV
                                </option>
                            @endif
                        @endfor
                    </select>
                </div>
            </form>

            <!-- Logout Button -->
            <form action="{{ url('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 px-2 px-sm-3" 
                        title="Keluar dari Aplikasi" 
                        style="height: 33px; border-radius: 8px; font-weight: 600;">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-sm-inline">Logout</span>
                </button>
            </form>
        </div>
    </div>
</nav>

<script>
    (function() {
        // Immediate check to prevent desktop flicker
        try {
            if (window.innerWidth >= 992 && localStorage.getItem('sidebar_collapsed') === '1') {
                document.documentElement.classList.add('sidebar-is-collapsed');
            }
        } catch(e) {}
    })();

    $(document).ready(function() {
        const $sidebar = $('#sidebar');
        const $backdrop = $('#sidebarBackdrop');
        const $navbar = $('#navbar');
        const $content = $('.content, #content');
        const $footer = $('.footer, footer');
        const $togglerBtn = $('#toggler-btn');
        const $togglerIcon = $togglerBtn.find('i');

        // Helper to update desktop collapse state
        function setDesktopCollapsed(isCollapsed) {
            if (isCollapsed) {
                $sidebar.addClass('collapsed');
                $navbar.addClass('collapsed');
                $content.addClass('collapsed');
                $footer.addClass('collapsed');
                $togglerBtn.addClass('collapsed');
                $togglerIcon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
                localStorage.setItem('sidebar_collapsed', '1');
            } else {
                $sidebar.removeClass('collapsed');
                $navbar.removeClass('collapsed');
                $content.removeClass('collapsed');
                $footer.removeClass('collapsed');
                $togglerBtn.removeClass('collapsed');
                $togglerIcon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
                localStorage.setItem('sidebar_collapsed', '0');
            }
        }

        // Initialize desktop state
        if (window.innerWidth >= 992) {
            if (localStorage.getItem('sidebar_collapsed') === '1') {
                setDesktopCollapsed(true);
            }
        }

        // Mobile drawer handlers
        function openMobileDrawer() {
            $sidebar.addClass('mobile-open');
            $backdrop.addClass('show');
            $('body').css('overflow', 'hidden');
        }

        function closeMobileDrawer() {
            $sidebar.removeClass('mobile-open');
            $backdrop.removeClass('show');
            $('body').css('overflow', '');
        }

        // Navbar Hamburger Click
        $('#navbarToggleBtn').on('click', function(e) {
            e.preventDefault();
            if (window.innerWidth < 992) {
                if ($sidebar.hasClass('mobile-open')) {
                    closeMobileDrawer();
                } else {
                    openMobileDrawer();
                }
            } else {
                // Desktop toggle
                const isCollapsed = $sidebar.hasClass('collapsed');
                setDesktopCollapsed(!isCollapsed);
            }
        });

        // Desktop Floating Toggler Click
        $togglerBtn.on('click', function(e) {
            e.preventDefault();
            const isCollapsed = $sidebar.hasClass('collapsed');
            setDesktopCollapsed(!isCollapsed);
        });

        // Close button inside mobile sidebar
        $('#sidebarCloseBtn').on('click', function(e) {
            e.preventDefault();
            closeMobileDrawer();
        });

        // Backdrop click closes mobile drawer
        $backdrop.on('click', function() {
            closeMobileDrawer();
        });

        // Submenu accordion toggle
        $('#toggle-submenu').on('click', function(e) {
            e.preventDefault();
            // If desktop and currently collapsed, expand first
            if (window.innerWidth >= 992 && $sidebar.hasClass('collapsed')) {
                setDesktopCollapsed(false);
            }
            $('#submenu').slideToggle(200);
            $(this).find('.arrow-icon').toggleClass('fa-chevron-right fa-chevron-down');
        });

        // Auto close mobile drawer on window resize to desktop
        $(window).on('resize', function() {
            if (window.innerWidth >= 992) {
                closeMobileDrawer();
                if (localStorage.getItem('sidebar_collapsed') === '1') {
                    setDesktopCollapsed(true);
                } else {
                    setDesktopCollapsed(false);
                }
            }
        });
    });
</script>

<style>
    /* ============================================================
       SIDEBAR CORE STYLES & DESIGN TOKENS
       ============================================================ */
    :root {
        --sidebar-width: 260px;
        --sidebar-collapsed-width: 72px;
        --sidebar-bg: #ffffff;
        --sidebar-color: #2c3e50;
    }

    /* Backdrop for Mobile */
    .sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        z-index: 1045;
        opacity: 0;
        transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sidebar-backdrop.show {
        display: block;
        opacity: 1;
    }

    /* Base Sidebar */
    .sidebar {
        background-color: var(--sidebar-bg);
        color: var(--sidebar-color);
        height: 100vh;
        width: var(--sidebar-width);
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1040;
        overflow-y: auto;
        overflow-x: hidden;
        box-shadow: 2px 0 12px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-right: 1px solid var(--kj-border);
    }

    /* Smooth scrollbar for sidebar */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }
    .sidebar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 4px;
    }

    .sidebar-nav-wrapper {
        flex: 1;
        overflow-y: auto;
        padding: 6px 10px;
    }

    /* Sidebar Navigation Links */
    .sidebar a {
        display: flex;
        align-items: center;
        padding: 9px 12px;
        color: #475569;
        text-decoration: none;
        border-radius: 9px;
        margin-bottom: 3px;
        font-size: 0.88rem;
        font-weight: 500;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .sidebar a i {
        font-size: 1.05rem;
        width: 22px;
        text-align: center;
        margin-right: 12px;
        flex-shrink: 0;
        color: #94a3b8;
        transition: color 0.2s ease;
    }

    .sidebar a:hover {
        background-color: #f8fafc;
        color: var(--kj-emerald);
    }

    .sidebar a:hover i {
        color: var(--kj-gold-dark);
    }

    .sidebar a.active {
        background: var(--kj-gold-gradient);
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(212, 165, 23, 0.25);
    }

    .sidebar a.active i {
        color: #ffffff !important;
    }

    /* Submenu */
    .submenu {
        padding-left: 8px;
        margin-top: 2px;
        margin-bottom: 6px;
    }

    .submenu a {
        padding: 7px 12px;
        font-size: 0.84rem;
        background-color: #f8fafc;
        border-left: 3px solid #e2e8f0;
        border-radius: 0 7px 7px 0;
        margin-bottom: 2px;
    }

    .submenu a:hover {
        border-left-color: var(--kj-gold);
        background-color: #fffbeb;
        color: #1e293b;
    }

    .submenu a.active {
        background: var(--kj-gold-gradient) !important;
        border-left-color: var(--kj-gold-dark) !important;
        color: #ffffff !important;
    }

    .arrow-icon {
        font-size: 0.75rem;
        transition: transform 0.2s ease;
    }

    /* Profile Pic with Gold Ring */
    .profile-pic {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto;
        display: block;
        transition: all 0.3s ease;
        border: 2.5px solid var(--kj-gold);
        padding: 2px;
        background: #fff;
        box-shadow: 0 0 10px rgba(230, 191, 62, 0.25);
    }

    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Desktop Floating Toggler Button */
    .toggler-btn {
        position: fixed;
        left: calc(var(--sidebar-width) - 14px);
        top: 14px;
        z-index: 1048;
        width: 28px;
        height: 28px;
        padding: 0;
        border-radius: 50%;
        transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.2s ease;
        border: 1px solid #d4a517;
        font-size: 0.75rem;
    }

    .toggler-btn:hover {
        transform: scale(1.1);
    }

    /* Navbar Alignment */
    .navbar {
        margin-left: var(--sidebar-width);
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 56px;
        z-index: 1030;
    }

    /* Content & Footer Alignment */
    .content, #content {
        margin-left: var(--sidebar-width);
        padding: 24px 24px;
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: calc(100vh - 116px);
    }

    .footer, footer {
        margin-left: var(--sidebar-width);
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ============================================================
       DESKTOP COLLAPSED (MINI) MODE (>= 992px)
       ============================================================ */
    @media (min-width: 992px) {
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width) !important;
        }

        .sidebar.collapsed .sidebar-brand-text,
        .sidebar.collapsed .user-title,
        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .arrow-icon,
        .sidebar.collapsed .submenu {
            display: none !important;
        }

        .sidebar.collapsed #user-info {
            padding: 10px 4px !important;
        }

        .sidebar.collapsed .profile-pic {
            width: 36px;
            height: 36px;
            margin: 0 auto;
        }

        .sidebar.collapsed .sidebar-header {
            justify-content: center !important;
            padding: 12px 4px !important;
        }

        .sidebar.collapsed a {
            justify-content: center !important;
            padding: 11px 0 !important;
            margin-bottom: 5px;
        }

        .sidebar.collapsed a i {
            margin-right: 0 !important;
            font-size: 1.15rem;
        }

        .navbar.collapsed {
            margin-left: var(--sidebar-collapsed-width) !important;
        }

        .content.collapsed, #content.collapsed {
            margin-left: var(--sidebar-collapsed-width) !important;
        }

        .footer.collapsed, footer.collapsed {
            margin-left: var(--sidebar-collapsed-width) !important;
        }

        .toggler-btn.collapsed {
            left: calc(var(--sidebar-collapsed-width) - 14px) !important;
        }
    }

    /* ============================================================
       TABLET & MOBILE OFF-CANVAS MODE (< 992px)
       ============================================================ */
    @media (max-width: 991.98px) {
        .sidebar {
            width: 280px !important;
            max-width: 85vw;
            left: 0;
            top: 0;
            bottom: 0;
            transform: translateX(-100%);
            z-index: 1055;
            box-shadow: none;
        }

        .sidebar.mobile-open {
            transform: translateX(0) !important;
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15) !important;
        }

        .sidebar .sidebar-text,
        .sidebar .user-title,
        .sidebar .arrow-icon {
            display: inline-block !important;
        }
        .sidebar .user-title {
            display: block !important;
        }
        .sidebar a {
            justify-content: flex-start !important;
            padding: 10px 14px !important;
        }
        .sidebar a i {
            margin-right: 12px !important;
        }

        .navbar,
        .navbar.collapsed,
        .content,
        #content,
        .content.collapsed,
        #content.collapsed,
        .footer,
        footer,
        .footer.collapsed,
        footer.collapsed {
            margin-left: 0 !important;
            width: 100% !important;
        }

        .content, #content {
            padding: 16px 14px !important;
        }

        .toggler-btn {
            display: none !important;
        }
    }
</style>
