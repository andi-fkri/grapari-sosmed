<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GraPARI Social Media Monitoring - Telkomsel Northern Manado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,600;1,700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        body {
            background: #EEF2F8;
            color: #1E293B;
        }
        .dashboard-shell {
            background: linear-gradient(145deg, #F6F8FC 0%, #EEF2F8 100%);
            min-height: 100vh;
        }
        .card-glass {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
        }
        .nav-active {
            background: linear-gradient(90deg, #E51C28 0%, #D41420 100%);
            color: #FFFFFF !important;
            box-shadow: 0 8px 18px -4px rgba(229, 28, 40, 0.42);
        }
        .pill-active-red {
            background: linear-gradient(90deg, #E51C28 0%, #D41420 100%);
            color: #FFFFFF;
            box-shadow: 0 4px 10px -2px rgba(229, 28, 40, 0.35);
        }
        .row-rank-1 {
            background: linear-gradient(90deg, #FDECEC 0%, #FFF5F5 50%, #FDECEC 100%);
        }
        .jitu-banner {
            background: radial-gradient(circle at 75% 25%, #EF233C 0%, #B80D1E 55%, #6A040F 100%);
            position: relative;
            overflow: hidden;
        }
        .jitu-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255,255,255,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 14px 14px;
            pointer-events: none;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="antialiased selection:bg-red-600 selection:text-white">

<div class="dashboard-shell flex min-h-screen">
    <!-- ==================== LEFT SIDEBAR ==================== -->
    <aside class="no-print w-[230px] shrink-0 bg-white/95 border-r border-slate-200/80 flex flex-col justify-between relative overflow-hidden select-none">
        <!-- Top Brand + Navigation -->
        <div>
            <!-- Telkomsel Logo Header -->
            <div class="px-7 pt-7 pb-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center group">
                    <span class="text-[27px] font-extrabold tracking-tight text-[#E11D2B] group-hover:opacity-90 transition">Telkomsel</span>
                </a>
            </div>

            <!-- Navigation Menu -->
            <nav class="px-3.5 space-y-1.5 mt-1">
                @if(!empty($isBranchUser) && !empty($branchGrapari))
                    <button type="button" onclick="openGrapariDetail({{ $branchGrapari->id }})" id="nav-branch-dashboard"
                        class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 nav-active cursor-pointer">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                            <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                        </svg>
                        <span>Dashboard {{ $branchGrapari->short_name }}</span>
                    </button>

                    <button type="button" onclick="openAddContentModal({{ $branchGrapari->id }})" id="nav-branch-add"
                        class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 text-slate-600 hover:bg-slate-100/80 cursor-pointer">
                        <svg class="w-5 h-5 shrink-0 text-[#E11D2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Tambah Konten</span>
                    </button>
                @else
                    <button type="button" onclick="switchTab('dashboard')" id="nav-dashboard"
                        class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ $activeTab === 'dashboard' ? 'nav-active' : 'text-slate-600 hover:bg-slate-100/80' }}">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                            <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                        </svg>
                        <span>Dashboard</span>
                    </button>

                    <button type="button" onclick="switchTab('laporan')" id="nav-laporan"
                        class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 border-b border-slate-100 {{ $activeTab === 'laporan' ? 'nav-active' : 'text-slate-600 hover:bg-slate-100/80' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2.5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m7 14 3.5-3.5 2.5 2.5L17 9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 9h3v3" />
                        </svg>
                        <span>Laporan</span>
                    </button>

                    <button type="button" onclick="switchTab('pengaturan')" id="nav-pengaturan"
                        class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ $activeTab === 'pengaturan' ? 'nav-active' : 'text-slate-600 hover:bg-slate-100/80' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <span>Pengaturan</span>
                    </button>
                @endif
            </nav>
        </div>

        <!-- Bottom IndiHome Fiber Branding + Decorative Red Wave -->
        <div class="relative pt-12 pb-28 px-5">
            <!-- IndiHome Fiber Logo Recreation -->
            <div class="relative z-10 text-center">
                <div class="inline-flex flex-col items-end">
                    <div class="flex items-baseline tracking-tight leading-none">
                        <span class="text-[26px] font-extrabold text-[#E11D2B]">IndiH</span>
                        <!-- Special 'o' with house roof accent -->
                        <span class="relative inline-block text-[26px] font-extrabold text-slate-500 mx-[0.5px]">
                            <svg class="w-4 h-2.5 absolute -top-2 left-1/2 -translate-x-1/2 text-[#E11D2B]" viewBox="0 0 20 10" fill="currentColor">
                                <path d="M10 0L0 8h3l7-5.5L17 8h3L10 0z"/>
                            </svg>
                            o
                        </span>
                        <span class="text-[26px] font-extrabold text-slate-500">me</span>
                    </div>
                    <div class="flex items-center gap-1 -mt-0.5">
                        <span class="text-[11px] font-bold text-[#E11D2B] tracking-wide">Fiber</span>
                        <div class="flex items-center gap-[2px] skew-x-[-22deg]">
                            <span class="w-[3px] h-2.5 bg-[#E11D2B] inline-block"></span>
                            <span class="w-[3px] h-2.5 bg-[#E11D2B] inline-block"></span>
                            <span class="w-[3px] h-2.5 bg-slate-400 inline-block"></span>
                        </div>
                    </div>
                </div>

                <p class="mt-3 text-[12.5px] italic font-semibold text-slate-700 leading-tight">
                    Semakin Dekat<br>
                    Dengan yang Anda Cinta
                </p>
            </div>

            <!-- Decorative Red Ribbon Waves at Bottom Left -->
            <div class="absolute bottom-0 left-0 right-0 h-28 pointer-events-none overflow-hidden">
                <svg class="w-full h-full" viewBox="0 0 230 120" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="sideWave1" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#FCA5A5" stop-opacity="0.35"/>
                            <stop offset="100%" stop-color="#EF4444" stop-opacity="0.1"/>
                        </linearGradient>
                        <linearGradient id="sideWave2" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#E11D2B"/>
                            <stop offset="60%" stop-color="#B91C1C"/>
                            <stop offset="100%" stop-color="#7F1D1D"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,15 C75,25 140,85 230,115 L230,120 L0,120 Z" fill="url(#sideWave1)" />
                    <path d="M0,38 C65,55 125,95 225,120 L0,120 Z" fill="#F87171" fill-opacity="0.35" />
                    <path d="M0,48 C65,68 110,102 185,120 L0,120 Z" fill="url(#sideWave2)" />
                    <path d="M0,78 C45,90 85,108 135,120 L0,120 Z" fill="#FCA5A5" fill-opacity="0.25" />
                </svg>
            </div>
        </div>
    </aside>

    <!-- ==================== MAIN CONTENT AREA ==================== -->
    <div class="flex-1 flex flex-col min-w-0 px-6 py-5">

        <!-- TOP HEADER BAR -->
        <header class="flex flex-wrap items-center justify-between gap-4 pb-5">
            <!-- Left: Telkomsel T Hexagon Badge + Title & Subtitle -->
            <div class="flex items-center gap-3.5">
                <!-- Telkomsel Hexagonal Portal 'T' Badge -->
                <div class="w-12 h-12 shrink-0 flex items-center justify-center">
                    <svg class="w-12 h-12 drop-shadow-sm" viewBox="0 0 64 64" fill="none">
                        <defs>
                            <linearGradient id="hexMain" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#DC2626"/>
                                <stop offset="55%" stop-color="#E11D2B"/>
                                <stop offset="100%" stop-color="#F97316"/>
                            </linearGradient>
                            <linearGradient id="hexTop" x1="10" y1="4" x2="40" y2="30" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#991B1B"/>
                                <stop offset="100%" stop-color="#E11D2B"/>
                            </linearGradient>
                            <linearGradient id="hexBottom" x1="20" y1="36" x2="56" y2="60" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#EA580C"/>
                                <stop offset="100%" stop-color="#FACC15"/>
                            </linearGradient>
                        </defs>
                        <!-- Faceted Hexagon Shape -->
                        <path d="M32 3L55 17.5V46.5L32 61L9 46.5V17.5L32 3Z" fill="url(#hexMain)" />
                        <path d="M32 3L55 17.5L32 32L9 17.5L32 3Z" fill="url(#hexTop)" opacity="0.85" />
                        <path d="M32 32L55 46.5L32 61L9 46.5L32 32Z" fill="url(#hexBottom)" opacity="0.75" />
                        <circle cx="32" cy="32" r="19" fill="#E11D2B" />
                        <!-- White Bold T -->
                        <path d="M22 22H42V27.5H35V43H29V27.5H22V22Z" fill="white" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-[23px] font-extrabold text-slate-900 tracking-tight leading-tight">
                        GraPARI Social Media Monitoring
                    </h1>
                    <p class="text-[13.5px] font-medium text-slate-500 mt-0.5">
                        Cluster Northern Manado <span class="mx-1 text-slate-400">|</span> Facebook Marketplace
                    </p>
                </div>
            </div>

            <!-- Right: Auto-Sync Indicator, Date Picker Dropdown & Admin Profile -->
            <div class="flex items-center gap-3.5">
                <!-- Auto-Sync Indicator -->
                <button type="button" id="autoSyncBtn" onclick="runAutoSyncNow()"
                    title="Sinkronisasi otomatis jumlah Like, Komentar, dan Share dari link Facebook"
                    class="flex items-center gap-2 bg-white border border-emerald-200/90 rounded-xl px-3.5 py-2 shadow-xs hover:border-emerald-300 transition cursor-pointer">
                    <span class="relative flex h-2.5 w-2.5">
                        <span id="autoSyncPulse" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <svg id="autoSyncIcon" class="w-3.5 h-3.5 text-emerald-600 shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <span class="text-[12.5px] font-bold text-slate-700">Auto-Sync</span>
                </button>

                <!-- Interactive Date Range Selector -->
                <div class="relative">
                    <div class="flex items-center gap-2.5 bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 shadow-xs hover:border-slate-300 transition">
                        <svg class="w-4 h-4 text-slate-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18" />
                            <rect x="7" y="14" width="3" height="3" rx="0.5" fill="currentColor" />
                        </svg>
                        <select id="periodSelector" onchange="handlePeriodChange(this.value)"
                            class="text-[13px] font-semibold text-slate-700 bg-transparent focus:outline-none cursor-pointer pr-1">
                            <option value="nov-full">01 – {{ $dailyMetrics->last()->label ?? '30 Nov' }} {{ now()->year }}</option>
                            <option value="oct-full">01 – 31 Okt {{ now()->year }}</option>
                            <option value="sep-full">01 – 30 Sep {{ now()->year }}</option>
                        </select>
                    </div>
                </div>

                <!-- Admin Profile Status & Logout -->
                <div class="flex items-center gap-3 pl-1">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-slate-400/80 text-white flex items-center justify-center shadow-inner">
                            <svg class="w-6 h-6 mt-1" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="leading-tight">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[13.5px] font-bold text-slate-800">{{ session('grapari_user', 'Admin') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                <span class="text-[11.5px] font-medium text-slate-500">Online</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="pl-1 border-l border-slate-200">
                        @csrf
                        <button type="submit" title="Keluar dari Dashboard"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-[#E11D2B] text-xs font-bold transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
                <span>✅ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
            </div>
        @endif

        <!-- ==================== TAB 1: DASHBOARD VIEW (DEFAULT 1:1 MOCKUP) ==================== -->
        <main id="tab-dashboard" class="{{ $activeTab === 'dashboard' ? 'block' : 'hidden' }} space-y-4">

            <!-- ROW 1: 5 KPI SUMMARY CARDS -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Card 1: Total GraPARI -->
                <div class="card-glass rounded-2xl p-4 relative overflow-hidden flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-blue-100/90 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5.223 2.25c-.497 0-.974.198-1.325.55l-1.3 1.298A3.75 3.75 0 0 0 7.5 9.75c.627.47 1.406.75 2.25.75.844 0 1.624-.28 2.25-.75.626.47 1.406.75 2.25.75.844 0 1.623-.28 2.25-.75a3.75 3.75 0 0 0 4.902-5.652l-1.3-1.299a1.875 1.875 0 0 0-1.325-.549H5.223Z" />
                            <path fill-rule="evenodd" d="M3 20.25v-8.755c1.42.674 3.08.673 4.5 0A5.234 5.234 0 0 0 9.75 12c.804 0 1.568-.182 2.25-.506a5.234 5.234 0 0 0 2.25.506c.804 0 1.567-.182 2.25-.506 1.42.674 3.08.675 4.5.001v8.755h.75a.75.75 0 0 1 0 1.5H2.25a.75.75 0 0 1 0-1.5H3Zm3-6a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-.75.75h-3a.75.75 0 0 1-.75-.75v-3Zm8.25-.75a.75.75 0 0 0-.75.75v5.25c0 .414.336.75.75.75h3a.75.75 0 0 0 .75-.75v-5.25a.75.75 0 0 0-.75-.75h-3Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[12.5px] font-semibold text-slate-600">Total GraPARI</p>
                        <p id="kpi-grapari" class="text-[28px] font-extrabold text-slate-900 leading-tight mt-0.5">{{ $summary['total_grapari'] }}</p>
                        <div class="h-4"></div>
                    </div>
                    <!-- Bottom Wave Decor -->
                    <svg class="absolute bottom-0 left-0 w-full h-7 pointer-events-none" viewBox="0 0 200 35" preserveAspectRatio="none">
                        <path d="M0,25 C60,35 120,0 200,18 L200,35 L0,35 Z" fill="#DBEAFE" fill-opacity="0.65"/>
                    </svg>
                </div>

                <!-- Card 2: Total Konten -->
                <div class="card-glass rounded-2xl p-4 relative overflow-hidden flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-purple-100/90 text-purple-600 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z" clip-rule="evenodd" />
                            <path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[12.5px] font-semibold text-slate-600">Total Konten</p>
                        <p id="kpi-konten" class="text-[28px] font-extrabold text-slate-900 leading-tight mt-0.5">{{ $summary['total_konten'] }}</p>
                        <p class="text-[12px] font-bold text-emerald-600 flex items-center gap-0.5 mt-0.5">
                            <span>↑</span> <span>{{ $summary['growth_konten'] }}</span>
                        </p>
                    </div>
                    <svg class="absolute bottom-0 left-0 w-full h-7 pointer-events-none" viewBox="0 0 200 35" preserveAspectRatio="none">
                        <path d="M0,22 C70,34 130,2 200,15 L200,35 L0,35 Z" fill="#EDE9FE" fill-opacity="0.75"/>
                    </svg>
                </div>

                <!-- Card 3: Total Likes -->
                <div class="card-glass rounded-2xl p-4 relative overflow-hidden flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-blue-100/90 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V3a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23h-.777ZM2.331 10.727a11.969 11.969 0 0 0-.831 4.398 12 12 0 0 0 .52 3.507C2.28 19.482 3.105 20 3.994 20H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 0 1-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.832 0-1.612.453-1.918 1.227Z" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[12.5px] font-semibold text-slate-600">Total Likes</p>
                        <p id="kpi-likes" class="text-[28px] font-extrabold text-slate-900 leading-tight mt-0.5">{{ number_format($summary['total_likes'], 0, ',', '.') }}</p>
                        <p class="text-[12px] font-bold text-emerald-600 flex items-center gap-0.5 mt-0.5">
                            <span>↑</span> <span>{{ $summary['growth_likes'] }}</span>
                        </p>
                    </div>
                    <svg class="absolute bottom-0 left-0 w-full h-7 pointer-events-none" viewBox="0 0 200 35" preserveAspectRatio="none">
                        <path d="M0,22 C75,32 125,4 200,16 L200,35 L0,35 Z" fill="#DBEAFE" fill-opacity="0.65"/>
                    </svg>
                </div>

                <!-- Card 4: Total Komentar -->
                <div class="card-glass rounded-2xl p-4 relative overflow-hidden flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-orange-100/90 text-orange-500 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.804 21.644A6.707 6.707 0 0 0 6 21.75a6.721 6.721 0 0 0 3.583-1.029c.774.182 1.584.279 2.417.279 5.322 0 9.75-3.97 9.75-9 0-5.03-4.428-9-9.75-9s-9.75 3.97-9.75 9c0 2.409 1.025 4.587 2.674 6.192.232.226.277.428.254.543a3.73 3.73 0 0 1-.814 1.686.75.75 0 0 0 .44 1.223ZM8.25 10.875a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25ZM10.875 12a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Zm4.875-1.125a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[12.5px] font-semibold text-slate-600">Total Komentar</p>
                        <p id="kpi-komentar" class="text-[28px] font-extrabold text-slate-900 leading-tight mt-0.5">{{ number_format($summary['total_komentar'], 0, ',', '.') }}</p>
                        <p class="text-[12px] font-bold text-emerald-600 flex items-center gap-0.5 mt-0.5">
                            <span>↑</span> <span>{{ $summary['growth_komentar'] }}</span>
                        </p>
                    </div>
                    <svg class="absolute bottom-0 left-0 w-full h-7 pointer-events-none" viewBox="0 0 200 35" preserveAspectRatio="none">
                        <path d="M0,22 C75,32 125,4 200,16 L200,35 L0,35 Z" fill="#FFEDD5" fill-opacity="0.8"/>
                    </svg>
                </div>

                <!-- Card 5: Total Share -->
                <div class="card-glass rounded-2xl p-4 relative overflow-hidden flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-100/90 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M15.75 4.5a3 3 0 1 1 .825 2.066l-8.421 4.679a3.002 3.002 0 0 1 0 1.51l8.421 4.679a3 3 0 1 1-.729 1.31l-8.421-4.678a3 3 0 1 1 0-4.132l8.421-4.679a3 3 0 0 1-.096-.755Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[12.5px] font-semibold text-slate-600">Total Share</p>
                        <p id="kpi-chat" class="text-[28px] font-extrabold text-slate-900 leading-tight mt-0.5">{{ number_format($summary['total_share'] ?? $summary['total_direct_chat'], 0, ',', '.') }}</p>
                        <p class="text-[12px] font-bold text-emerald-600 flex items-center gap-0.5 mt-0.5">
                            <span>↑</span> <span>{{ $summary['growth_share'] ?? $summary['growth_direct_chat'] }}</span>
                        </p>
                    </div>
                    <svg class="absolute bottom-0 left-0 w-full h-7 pointer-events-none" viewBox="0 0 200 35" preserveAspectRatio="none">
                        <path d="M0,22 C75,32 125,4 200,16 L200,35 L0,35 Z" fill="#D1FAE5" fill-opacity="0.75"/>
                    </svg>
                </div>
            </section>

            <!-- ROW 2: PERFORMA 10 GRAPARI (BAR CHART) + DETAIL KONTEN TERBARU -->
            <section class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                <!-- Left (8 Cols): Performa 10 GraPARI -->
                <div class="lg:col-span-8 card-glass rounded-2xl p-5 flex flex-col justify-between">
                    <div>
                        <!-- Header + Filter Pills -->
                        <div class="flex flex-wrap items-center justify-between gap-2 pb-2">
                            <div class="flex items-center gap-2.5">
                                <!-- Red Bar Chart Icon -->
                                <svg class="w-5 h-5 text-[#E11D2B]" viewBox="0 0 24 24" fill="currentColor">
                                    <rect x="3" y="13" width="4" height="8" rx="1" />
                                    <rect x="10" y="9" width="4" height="12" rx="1" />
                                    <rect x="17" y="4" width="4" height="17" rx="1" />
                                </svg>
                                <h2 class="text-[16px] font-extrabold text-slate-800">Performa 10 GraPARI</h2>
                            </div>

                            <!-- Interactive Metric Filter Pills -->
                            <div class="flex items-center gap-1.5 bg-slate-100/90 p-1 rounded-xl">
                                <button type="button" id="filter-btn-likes" onclick="filterBarChart('likes')"
                                    class="pill-active-red flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold transition-all">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V3a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23h-.777Z" />
                                    </svg>
                                    <span>Likes</span>
                                </button>

                                <button type="button" id="filter-btn-comments" onclick="filterBarChart('comments')"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold text-slate-600 hover:text-slate-900 transition-all">
                                    <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.804 21.644A6.707 6.707 0 0 0 6 21.75a6.721 6.721 0 0 0 3.583-1.029c.774.182 1.584.279 2.417.279 5.322 0 9.75-3.97 9.75-9 0-5.03-4.428-9-9.75-9s-9.75 3.97-9.75 9c0 2.409 1.025 4.587 2.674 6.192.232.226.277.428.254.543a3.73 3.73 0 0 1-.814 1.686.75.75 0 0 0 .44 1.223Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Komentar</span>
                                </button>

                                <button type="button" id="filter-btn-chats" onclick="filterBarChart('chats')"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold text-slate-600 hover:text-slate-900 transition-all">
                                    <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path fill-rule="evenodd" d="M15.75 4.5a3 3 0 1 1 .825 2.066l-8.421 4.679a3.002 3.002 0 0 1 0 1.51l8.421 4.679a3 3 0 1 1-.729 1.31l-8.421-4.678a3 3 0 1 1 0-4.132l8.421-4.679a3 3 0 0 1-.096-.755Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Share</span>
                                </button>
                            </div>
                        </div>

                        <!-- Legend Row -->
                        <div class="flex items-center justify-center gap-5 py-1.5 text-[12px] font-semibold text-slate-600">
                            <button type="button" onclick="filterBarChart('all')" class="flex items-center gap-1.5 hover:opacity-80">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#2563EB] inline-block"></span>
                                <span>Likes</span>
                            </button>
                            <button type="button" onclick="filterBarChart('all')" class="flex items-center gap-1.5 hover:opacity-80">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#8B5CF6] inline-block"></span>
                                <span>Komentar</span>
                            </button>
                            <button type="button" onclick="filterBarChart('all')" class="flex items-center gap-1.5 hover:opacity-80">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#10B981] inline-block"></span>
                                <span>Share</span>
                            </button>
                        </div>
                    </div>

                    <!-- Bar Chart Canvas -->
                    <div class="relative h-[225px] w-full mt-1">
                        <canvas id="performaBarChart"></canvas>
                    </div>
                </div>

                <!-- Right (4 Cols): Detail Konten Terbaru -->
                <div class="lg:col-span-4 card-glass rounded-2xl p-5 flex flex-col justify-between">
                    <div>
                        <!-- Card Header -->
                        <div class="flex items-center justify-between gap-2 pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <!-- Red Megaphone Icon -->
                                <svg class="w-5 h-5 text-[#E11D2B]" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16.881 4.345A23.112 23.112 0 0 1 8.25 6H7.5a5.25 5.25 0 0 0-.88 10.427 21.593 21.593 0 0 0 1.378 3.94c.464 1.004 1.674 1.32 2.582.796l.657-.379c.88-.508 1.165-1.593.772-2.468a17.116 17.116 0 0 1-.628-1.607c1.918.258 3.76.75 5.5 1.446A21.727 21.727 0 0 0 18 11.25c0-2.414-.393-4.735-1.119-6.905ZM18.26 3.74a23.22 23.22 0 0 1 1.24 7.51 23.22 23.22 0 0 1-1.41 7.992.75.75 0 1 0 1.409.516 24.555 24.555 0 0 0 1.415-6.43 2.992 2.992 0 0 0 .836-2.078c0-.807-.319-1.54-.836-2.078a24.65 24.65 0 0 0-1.415-6.43.75.75 0 1 0-1.409.516c.059.16.116.321.17.483Z" />
                                </svg>
                                <h2 class="text-[15.5px] font-extrabold text-slate-800">Detail Konten Terbaru</h2>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="openAddContentModal()"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 border border-red-200 text-[11px] font-bold text-[#E11D2B] hover:bg-red-100 transition cursor-pointer">
                                    <span>+ Link FB</span>
                                </button>
                                <button type="button" onclick="openGrapariDetail(currentSelectedGrapariId)"
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full border border-slate-200 text-[11.5px] font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition cursor-pointer">
                                    <span>Detail Cabang</span>
                                    <span>→</span>
                                </button>
                            </div>
                        </div>

                        <!-- Featured Post Body -->
                        <div class="mt-4 flex gap-3.5 items-center">
                            <!-- Content of (Nama GraPARI) Thumbnail Box -->
                            <div class="jitu-banner w-[138px] h-[132px] rounded-xl shrink-0 p-3 flex flex-col items-center justify-center text-center text-white shadow-md border border-red-800/30">
                                <div class="relative z-10 space-y-1">
                                    <span class="block text-[11px] font-semibold text-red-100 tracking-wide">Content of</span>
                                    <span id="featured-banner-grapari" class="block text-[15px] font-extrabold text-white leading-snug drop-shadow-xs">
                                        {{ $featuredContent->grapari->name ?? 'GraPARI' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Text Info -->
                            <div class="min-w-0 flex-1">
                                <p id="featured-grapari" class="text-[13.5px] font-bold text-slate-800">
                                    {{ $featuredContent->grapari->name ?? 'Belum Ada Konten' }}
                                </p>
                                <h3 id="featured-title" class="text-[14.5px] font-semibold text-slate-800 leading-snug mt-1 line-clamp-2">
                                    {{ $featuredContent->title ?? 'Klik "+ Link FB" untuk menambahkan konten baru' }}
                                </h3>
                                <div class="flex items-center gap-1.5 text-[11.5px] font-medium text-slate-400 mt-2.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="9" />
                                        <path stroke-linecap="round" d="M12 7v5l3 2" />
                                    </svg>
                                    <span id="featured-date">{{ $featuredContent ? $featuredContent->published_at->format('d M Y • H:i') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom 3 Engagement Metric Boxes -->
                    <div class="grid grid-cols-3 gap-2.5 mt-4 pt-1">
                        <!-- Likes Box -->
                        <div class="bg-[#F0F6FF] border border-blue-100/90 rounded-xl py-2.5 px-2 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V3a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23h-.777Z" />
                                </svg>
                                <span id="featured-likes" class="text-[16px] font-extrabold text-slate-900">{{ $featuredContent->likes ?? 0 }}</span>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Likes</p>
                        </div>

                        <!-- Komentar Box -->
                        <div class="bg-[#F5F3FF] border border-purple-100/90 rounded-xl py-2.5 px-2 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-purple-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.804 21.644A6.707 6.707 0 0 0 6 21.75a6.721 6.721 0 0 0 3.583-1.029c.774.182 1.584.279 2.417.279 5.322 0 9.75-3.97 9.75-9 0-5.03-4.428-9-9.75-9s-9.75 3.97-9.75 9c0 2.409 1.025 4.587 2.674 6.192.232.226.277.428.254.543a3.73 3.73 0 0 1-.814 1.686.75.75 0 0 0 .44 1.223Z" clip-rule="evenodd" />
                                </svg>
                                <span id="featured-comments" class="text-[16px] font-extrabold text-slate-900">{{ $featuredContent->comments ?? 0 }}</span>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Komentar</p>
                        </div>

                        <!-- Share Box -->
                        <div class="bg-[#ECFDF5] border border-emerald-100/90 rounded-xl py-2.5 px-2 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M15.75 4.5a3 3 0 1 1 .825 2.066l-8.421 4.679a3.002 3.002 0 0 1 0 1.51l8.421 4.679a3 3 0 1 1-.729 1.31l-8.421-4.678a3 3 0 1 1 0-4.132l8.421-4.679a3 3 0 0 1-.096-.755Z" clip-rule="evenodd" />
                                </svg>
                                <span id="featured-chats" class="text-[16px] font-extrabold text-slate-900">{{ $featuredContent->direct_chats ?? 0 }}</span>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Share</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ROW 3: RANKING PERFORMA GRAPARI + TREN AKTIVITAS (30 HARI) & INSIGHT -->
            <section class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                <!-- Left (8 Cols): Ranking Performa GraPARI Table -->
                <div class="lg:col-span-8 card-glass rounded-2xl p-5">
                    <div class="flex items-center justify-between gap-2 pb-3.5">
                        <div class="flex items-center gap-2.5">
                            <!-- Red Trophy Icon -->
                            <svg class="w-5 h-5 text-[#E11D2B]" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.166 2.621v.858c-1.035.148-2.059.33-3.071.543a.75.75 0 0 0-.584.859 6.753 6.753 0 0 0 6.138 5.6 6.73 6.73 0 0 0 2.743 1.346A6.707 6.707 0 0 1 9.279 15H8.54c-1.036 0-1.875.84-1.875 1.875V19.5h-.75a2.25 2.25 0 0 0-2.25 2.25c0 .414.336.75.75.75h15a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-2.25-2.25h-.75v-2.625c0-1.036-.84-1.875-1.875-1.875h-.739a6.706 6.706 0 0 1-1.112-3.173 6.73 6.73 0 0 0 2.743-1.347 6.753 6.753 0 0 0 6.139-5.6.75.75 0 0 0-.585-.858 47.077 47.077 0 0 0-3.07-.543V2.62a.75.75 0 0 0-.658-.744 49.22 49.22 0 0 0-6.093-.377c-2.063 0-4.096.128-6.093.377a.75.75 0 0 0-.657.744Zm0 2.629c0 1.196.312 2.32.857 3.294A5.266 5.266 0 0 1 3.16 5.337a45.6 45.6 0 0 1 2.006-.343v.256Zm13.5 0v-.256c.674.1 1.343.214 2.006.343a5.265 5.265 0 0 1-2.863 3.207 6.72 6.72 0 0 0 .857-3.294Z" clip-rule="evenodd" />
                            </svg>
                            <h2 class="text-[16px] font-extrabold text-slate-800">Ranking Performa GraPARI</h2>
                        </div>
                        <span class="text-[11.5px] font-semibold text-[#E11D2B] bg-red-50 px-2.5 py-0.5 rounded-full">Klik nama GraPARI untuk detail konten & grafik line</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/90 border-y border-slate-200/70 text-[12px] font-bold text-slate-700">
                                    <th class="py-2.5 px-3 text-center w-12">No</th>
                                    <th class="py-2.5 px-3">Nama GraPARI</th>
                                    <th class="py-2.5 px-3 text-center">Total Konten</th>
                                    <th class="py-2.5 px-3 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V3a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23h-.777Z" />
                                            </svg>
                                            <span>Likes</span>
                                        </div>
                                    </th>
                                    <th class="py-2.5 px-3 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-purple-600" viewBox="0 0 24 24" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.804 21.644A6.707 6.707 0 0 0 6 21.75a6.721 6.721 0 0 0 3.583-1.029c.774.182 1.584.279 2.417.279 5.322 0 9.75-3.97 9.75-9 0-5.03-4.428-9-9.75-9s-9.75 3.97-9.75 9c0 2.409 1.025 4.587 2.674 6.192.232.226.277.428.254.543a3.73 3.73 0 0 1-.814 1.686.75.75 0 0 0 .44 1.223Z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Komentar</span>
                                        </div>
                                    </th>
                                    <th class="py-2.5 px-3 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 24 24" fill="currentColor">
                                                <path fill-rule="evenodd" d="M15.75 4.5a3 3 0 1 1 .825 2.066l-8.421 4.679a3.002 3.002 0 0 1 0 1.51l8.421 4.679a3 3 0 1 1-.729 1.31l-8.421-4.678a3 3 0 1 1 0-4.132l8.421-4.679a3 3 0 0 1-.096-.755Z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Share</span>
                                        </div>
                                    </th>
                                    <th class="py-2.5 px-3 text-center">Engagement Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-[13px]">
                                @foreach($graparis as $g)
                                    <tr onclick="openGrapariDetail({{ $g->id }})"
                                        data-grapari-row="{{ $g->id }}"
                                        class="group cursor-pointer transition-colors {{ $g->rank === 1 ? 'row-rank-1 font-bold text-slate-900' : 'hover:bg-slate-50/90 text-slate-700 font-medium' }}">
                                        <td class="py-2 px-3 text-center">
                                            @if($g->rank === 1)
                                                <span class="inline-flex items-center justify-center text-base" title="Rank 1">🥇</span>
                                            @elseif($g->rank === 2)
                                                <span class="inline-flex items-center justify-center text-base" title="Rank 2">🥈</span>
                                            @elseif($g->rank === 3)
                                                <span class="inline-flex items-center justify-center text-base" title="Rank 3">🥉</span>
                                            @else
                                                <span class="font-semibold text-slate-600">{{ $g->rank }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-3 {{ $g->rank === 1 ? 'font-extrabold text-slate-900' : 'font-semibold text-slate-800' }}">
                                            <div class="inline-flex items-center gap-1.5 group-hover:text-[#E11D2B] transition-colors">
                                                <span class="underline decoration-slate-300 group-hover:decoration-[#E11D2B] underline-offset-2">{{ $g->name }}</span>
                                                <span class="text-[11px] opacity-0 group-hover:opacity-100 text-[#E11D2B] font-bold transition-opacity">→</span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-3 text-center" data-grapari-contents="{{ $g->id }}">{{ $g->total_contents }}</td>
                                        <td class="py-2 px-3 text-center" data-grapari-likes="{{ $g->id }}">{{ $g->likes }}</td>
                                        <td class="py-2 px-3 text-center" data-grapari-comments="{{ $g->id }}">{{ $g->comments }}</td>
                                        <td class="py-2 px-3 text-center" data-grapari-shares="{{ $g->id }}">{{ $g->direct_chats }}</td>
                                        <td class="py-2 px-3 text-center {{ $g->rank === 1 ? 'font-extrabold text-slate-900' : 'font-bold text-slate-700' }}" data-grapari-rate="{{ $g->id }}">
                                            {{ number_format($g->engagement_rate, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right (4 Cols): Tren Aktivitas (30 Hari Terakhir) + Insight Card -->
                <div class="lg:col-span-4 flex flex-col justify-between gap-4">

                    <!-- Top Card: Tren Aktivitas (30 Hari Terakhir) -->
                    <div class="card-glass rounded-2xl p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <!-- Red Trending Line Icon -->
                                    <svg class="w-5 h-5 text-[#E11D2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                                    </svg>
                                    <h2 class="text-[15.5px] font-extrabold text-slate-800">Tren Aktivitas (30 Hari Terakhir)</h2>
                                </div>
                                <span id="trend-date-range" class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full">
                                    {{ $dailyMetrics->first()->label ?? '' }} – {{ $dailyMetrics->last()->label ?? '' }}
                                </span>
                            </div>

                            <!-- Legend -->
                            <div class="flex items-center justify-center gap-4 mt-2.5 text-[11.5px] font-semibold text-slate-600">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#2563EB] inline-block"></span> Likes
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#8B5CF6] inline-block"></span> Komentar
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#10B981] inline-block"></span> Share
                                </span>
                            </div>
                        </div>

                        <div class="relative h-[175px] w-full mt-2">
                            <canvas id="trenLineChart"></canvas>
                        </div>
                    </div>

                    <!-- Bottom Card: Insight Box -->
                    <div class="rounded-2xl p-4 bg-gradient-to-r from-[#EAF2FF] via-[#E5F0FF] to-[#DBEAFE] border border-blue-200/80 shadow-xs flex items-center justify-between gap-3 relative overflow-hidden">
                        <div class="flex items-start gap-3 relative z-10">
                            <!-- Glowing Lightbulb Icon -->
                            <div class="w-10 h-10 rounded-xl bg-amber-100/90 border border-amber-200/80 flex items-center justify-center shrink-0 shadow-xs">
                                <svg class="w-6 h-6 text-amber-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 .75a8.25 8.25 0 0 0-4.135 15.39c.686.398 1.115 1.008 1.134 1.623a.75.75 0 0 0 .577.706c.352.083.71.148 1.074.195.323.041.6-.218.6-.544v-4.661a6.714 6.714 0 0 1-.937-.171.75.75 0 1 1 .374-1.453 5.261 5.261 0 0 0 2.626 0 .75.75 0 1 1 .374 1.452 6.712 6.712 0 0 1-.937.172v4.66c0 .327.277.586.6.545.364-.047.722-.112 1.074-.195a.75.75 0 0 0 .577-.706c.02-.615.448-1.225 1.134-1.623A8.25 8.25 0 0 0 12 .75Z" />
                                    <path fill-rule="evenodd" d="M9.013 19.9a.75.75 0 0 1 .877-.597 11.319 11.319 0 0 0 4.22 0 .75.75 0 1 1 .28 1.473 12.819 12.819 0 0 1-4.78 0 .75.75 0 0 1-.597-.876ZM9.754 22.344a.75.75 0 0 1 .824-.668 13.682 13.682 0 0 0 2.844 0 .75.75 0 1 1 .156 1.492 15.156 15.156 0 0 1-3.156 0 .75.75 0 0 1-.668-.824Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-[14px] font-extrabold text-slate-800">Insight</h3>
                                <p id="insight-text" class="text-[12.5px] font-medium text-slate-700 leading-relaxed mt-0.5">
                                    @if(($summary['total_konten'] ?? 0) > 0)
                                        <strong class="font-extrabold text-slate-900">{{ $topGrapari->name ?? 'GraPARI Manado' }}</strong> menjadi yang paling aktif dengan engagement rate sebesar <strong class="font-extrabold text-slate-900">{{ number_format($topGrapari->engagement_rate ?? 0, 1) }}%</strong> dari total aktivitas.
                                    @else
                                        Belum ada konten aktif. Klik <strong class="font-extrabold text-slate-900">+ Link FB</strong> untuk menambahkan postingan dan melihat analitik cabang GraPARI.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Right Decorative Rising Bar Chart Graphic -->
                        <div class="shrink-0 self-end">
                            <svg class="w-12 h-12 text-blue-600" viewBox="0 0 48 48" fill="none">
                                <path d="M8 30L18 22L27 25L40 12M40 12H32M40 12V20" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <rect x="10" y="32" width="6" height="12" rx="1.5" fill="#60A5FA"/>
                                <rect x="21" y="25" width="6" height="19" rx="1.5" fill="#3B82F6"/>
                                <rect x="32" y="17" width="6" height="27" rx="1.5" fill="#2563EB"/>
                            </svg>
                        </div>
                    </div>

                </div>
            </section>
        </main>

        <!-- ==================== DRILL-DOWN VIEW: DETAIL KONTEN & GRAFIK LINE PER GRAPARI ==================== -->
        <section id="tab-grapari-detail" class="{{ !empty($isBranchUser) ? 'block' : 'hidden' }} space-y-4">
            <!-- Top Header & Branch Switcher Card -->
            <div class="card-glass rounded-2xl p-5">
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        @if(empty($isBranchUser))
                            <button type="button" onclick="closeGrapariDetail()"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer shadow-2xs">
                                <span>←</span>
                                <span>Kembali ke Dashboard</span>
                            </button>
                        @endif
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 id="detail-grapari-title" class="text-lg font-extrabold text-slate-900">{{ $branchGrapari->name ?? 'GraPARI Manado' }}</h2>
                                <span id="detail-grapari-rank-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-[#E11D2B]">Rank #{{ $branchGrapari->rank ?? 1 }}</span>
                            </div>
                            <p id="detail-grapari-address" class="text-xs text-slate-500 mt-0.5">{{ $branchGrapari->address ?? 'Jl. Piere Tendean (Boulevard), Sario Tumpaan, Kota Manado' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5">
                        @if(empty($isBranchUser))
                            <select id="detailGrapariSwitcher" onchange="openGrapariDetail(Number(this.value))"
                                class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 focus:outline-none focus:border-red-500 cursor-pointer">
                                @foreach($graparis as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <button type="button" onclick="openAddContentModal(currentSelectedGrapariId)"
                            class="pill-active-red px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 cursor-pointer">
                            <span>+ Tambah Konten</span>
                        </button>
                    </div>
                </div>

                <!-- 5 Branch Summary KPI Boxes -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-4">
                    <div class="p-3 rounded-xl bg-purple-50/70 border border-purple-100 text-center">
                        <span class="text-[11px] font-semibold text-slate-500 block">Total Konten</span>
                        <span id="detail-kpi-contents" class="text-xl font-extrabold text-purple-700 mt-0.5 block">0</span>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-50/70 border border-blue-100 text-center">
                        <span class="text-[11px] font-semibold text-slate-500 block">Total Likes</span>
                        <span id="detail-kpi-likes" class="text-xl font-extrabold text-blue-700 mt-0.5 block">0</span>
                    </div>
                    <div class="p-3 rounded-xl bg-orange-50/70 border border-orange-100 text-center">
                        <span class="text-[11px] font-semibold text-slate-500 block">Total Komentar</span>
                        <span id="detail-kpi-comments" class="text-xl font-extrabold text-orange-600 mt-0.5 block">0</span>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-100 text-center">
                        <span class="text-[11px] font-semibold text-slate-500 block">Total Share</span>
                        <span id="detail-kpi-shares" class="text-xl font-extrabold text-emerald-700 mt-0.5 block">0</span>
                    </div>
                    <div class="p-3 rounded-xl bg-red-50/70 border border-red-100 text-center col-span-2 sm:col-span-1">
                        <span class="text-[11px] font-semibold text-slate-500 block">Engagement Rate</span>
                        <span id="detail-kpi-rate" class="text-xl font-extrabold text-[#E11D2B] mt-0.5 block">0.0%</span>
                    </div>
                </div>
            </div>

            <!-- Grafik Line Konten per GraPARI -->
            <div class="card-glass rounded-2xl p-5">
                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#E11D2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">
                                Grafik Line Konten — <span id="chart-grapari-name" class="text-[#E11D2B]">GraPARI Manado</span>
                            </h3>
                            <p class="text-xs text-slate-500">Perkembangan jumlah Likes, Komentar, dan Share</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#2563EB] inline-block"></span> Likes
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8B5CF6] inline-block"></span> Komentar
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#10B981] inline-block"></span> Share
                        </span>
                    </div>
                </div>

                <div class="relative h-[240px] w-full mt-4">
                    <canvas id="grapariDetailLineChart"></canvas>
                </div>
            </div>

            <!-- Daftar Detail Konten Milik GraPARI Tersebut -->
            <div class="card-glass rounded-2xl p-5">
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">
                            Daftar Konten — <span id="list-grapari-name" class="text-[#E11D2B]">GraPARI Manado</span>
                        </h3>
                        <p class="text-xs text-slate-500">Menampilkan seluruh konten Facebook Marketplace yang telah ditambahkan.</p>
                    </div>
                    <input type="text" id="searchContentInput" onkeyup="filterContentCards()" placeholder="Cari judul konten..."
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-red-500">
                </div>

                <div id="contentGridContainer" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mt-4">
                    @foreach($contents as $item)
                        <div class="content-card-item border border-slate-200/80 rounded-2xl p-4 bg-white hover:shadow-md transition flex flex-col justify-between"
                             data-content-grapari="{{ $item->grapari_id }}"
                             data-search="{{ strtolower($item->title . ' ' . ($item->grapari->name ?? '') . ' ' . $item->campaign_tag) }}">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-[#E11D2B]">
                                        {{ $item->grapari->name ?? 'GraPARI Manado' }}
                                    </span>
                                    <span class="text-xs font-medium text-slate-400">
                                        {{ $item->published_at->format('d M Y • H:i') }}
                                    </span>
                                </div>
                                <div class="flex gap-3 items-center">
                                    <div class="jitu-banner w-24 h-20 rounded-xl shrink-0 p-2.5 flex flex-col items-center justify-center text-white text-center shadow-xs">
                                        <span class="relative z-10 text-[10px] font-semibold text-red-100 leading-tight">Content of</span>
                                        <span class="relative z-10 text-[11.5px] font-extrabold leading-snug mt-0.5">{{ $item->grapari->name ?? 'GraPARI Manado' }}</span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-800 text-sm leading-snug">{{ $item->title }}</h3>
                                        <p class="text-xs text-slate-500 mt-1">{{ $item->grapari->name ?? 'GraPARI Manado' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2 mt-4 pt-3 border-t border-slate-100 text-center">
                                <div class="bg-blue-50/70 rounded-lg py-1.5">
                                    <span class="block text-sm font-extrabold text-blue-700" data-content-likes="{{ $item->id }}">{{ $item->likes }}</span>
                                    <span class="text-[10px] text-slate-500">Likes</span>
                                </div>
                                <div class="bg-purple-50/70 rounded-lg py-1.5">
                                    <span class="block text-sm font-extrabold text-purple-700" data-content-comments="{{ $item->id }}">{{ $item->comments }}</span>
                                    <span class="text-[10px] text-slate-500">Komentar</span>
                                </div>
                                <div class="bg-emerald-50/70 rounded-lg py-1.5">
                                    <span class="block text-sm font-extrabold text-emerald-700" data-content-shares="{{ $item->id }}">{{ $item->direct_chats }}</span>
                                    <span class="text-[10px] text-slate-500">Share</span>
                                </div>
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 text-xs">
                                @if($item->post_url)
                                    <a href="{{ $item->post_url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-semibold truncate max-w-[55%]"
                                       title="{{ $item->post_url }}">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                        </svg>
                                        <span class="truncate">Link Postingan FB</span>
                                    </a>
                                @else
                                    <span class="text-slate-400">Tanpa Link</span>
                                @endif
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($item->post_url)
                                        <form action="{{ route('contents.sync', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition cursor-pointer">
                                                🔄 Sync
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('contents.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-[11px] transition cursor-pointer">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Empty State for Selected GraPARI -->
                <div id="grapariEmptyContentState" class="hidden py-10 text-center bg-slate-50/70 rounded-2xl border border-dashed border-slate-200 mt-4">
                    <p id="grapariEmptyTitle" class="text-sm font-bold text-slate-700">Belum ada konten untuk GraPARI ini</p>
                    <p class="text-xs text-slate-500 mt-1">Klik tombol "+ Tambah Konten" untuk menempelkan link postingan Facebook.</p>
                </div>
            </div>
        </section>

        <!-- ==================== TAB 3: LAPORAN VIEW ==================== -->
        <section id="tab-laporan" class="{{ $activeTab === 'laporan' ? 'block' : 'hidden' }} space-y-4">
            <div class="card-glass rounded-2xl p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">Laporan Eksekutif Performa GraPARI</h2>
                        <p class="text-sm text-slate-500">Rekapitulasi performa Cluster Northern Manado (Periode 01 – 30 Nov 2026)</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button type="button" onclick="exportTableToCSV()"
                            class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-bold transition">
                            📥 Download CSV / Excel
                        </button>
                        <button type="button" onclick="window.print()"
                            class="pill-active-red px-4 py-2 rounded-xl text-sm font-bold">
                            🖨️ Cetak Laporan PDF
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto mt-5">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 font-bold">
                                <th class="p-3">Rank</th>
                                <th class="p-3">Nama GraPARI</th>
                                <th class="p-3">Alamat Cabang</th>
                                <th class="p-3 text-center">Konten</th>
                                <th class="p-3 text-center">Likes</th>
                                <th class="p-3 text-center">Komentar</th>
                                <th class="p-3 text-center">Share</th>
                                <th class="p-3 text-center">Total Interaksi</th>
                                <th class="p-3 text-center">Engagement Rate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/70">
                            @foreach($graparis as $g)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 font-bold">#{{ $g->rank }}</td>
                                    <td class="p-3 font-bold text-slate-900">{{ $g->name }}</td>
                                    <td class="p-3 text-slate-500 text-xs">{{ $g->address }}</td>
                                    <td class="p-3 text-center font-semibold">{{ $g->total_contents }}</td>
                                    <td class="p-3 text-center text-blue-600 font-bold">{{ $g->likes }}</td>
                                    <td class="p-3 text-center text-purple-600 font-bold">{{ $g->comments }}</td>
                                    <td class="p-3 text-center text-emerald-600 font-bold">{{ $g->direct_chats }}</td>
                                    <td class="p-3 text-center font-extrabold">{{ $g->likes + $g->comments + $g->direct_chats }}</td>
                                    <td class="p-3 text-center font-extrabold text-[#E11D2B]">{{ number_format($g->engagement_rate, 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ==================== TAB 4: PENGATURAN VIEW ==================== -->
        <section id="tab-pengaturan" class="{{ $activeTab === 'pengaturan' ? 'block' : 'hidden' }} space-y-4">
            <div class="card-glass rounded-2xl p-6 max-w-3xl">
                <h2 class="text-lg font-extrabold text-slate-900">Pengaturan Sistem Monitoring GraPARI</h2>
                <p class="text-sm text-slate-500 mt-1">Konfigurasi region, sinkronisasi data Facebook Marketplace, dan reset data demo.</p>

                <div class="mt-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Wilayah Operasional</label>
                            <input type="text" value="Cluster Northern Manado" readonly class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-700">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Channel Monitoring</label>
                            <input type="text" value="Facebook Marketplace" readonly class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-700">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Reset Database ke Data Default Mockup</h3>
                            <p class="text-xs text-slate-500">Mengembalikan seluruh angka KPI dan 10 GraPARI persis ke kondisi awal.</p>
                        </div>
                        <form action="{{ route('demo.reset') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition">
                                🔄 Reset Data Standar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>

<!-- ==================== MODAL TAMBAH KONTEN BARU ==================== -->
<div id="modalAddContent" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-100 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Tambah Konten dari Link Facebook</h3>
                <p class="text-xs text-slate-500 mt-0.5">Cukup salin & tempel link postingan Facebook Marketplace / Akun Personal untuk membaca Like, Komen, dan Share secara otomatis.</p>
            </div>
            <button type="button" onclick="document.getElementById('modalAddContent').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('contents.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">1. Cabang GraPARI</label>
                @if(!empty($isBranchUser) && !empty($branchGrapari))
                    <input type="hidden" id="modalGrapariSelect" name="grapari_id" value="{{ $branchGrapari->id }}">
                    <div class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-sm font-bold text-slate-800">
                        {{ $branchGrapari->name }}
                    </div>
                @else
                    <select id="modalGrapariSelect" name="grapari_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:outline-none focus:border-red-500">
                        @foreach($graparis as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- STEP 2: PASTE LINK FACEBOOK -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-2.5">
                <div class="flex items-center justify-between">
                    <label for="inputPostUrl" class="block text-xs font-bold text-slate-800">
                        2. Tempel Link Postingan Facebook Marketplace / Akun Personal
                    </label>
                    <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Auto-Read Metrik</span>
                </div>

                <div class="flex gap-2">
                    <input type="url" id="inputPostUrl" name="post_url" required
                        onpaste="setTimeout(() => fetchFacebookLinkMetrics(), 80)"
                        oninput="handlePostUrlInput(this.value)"
                        placeholder="https://www.facebook.com/share/p/... atau link postingan FB publik"
                        class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm focus:outline-none focus:border-red-500">
                    <button type="button" id="btnFetchLink" onclick="fetchFacebookLinkMetrics()"
                        class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shrink-0 transition flex items-center gap-1.5 cursor-pointer">
                        <span id="btnFetchIcon">🔍</span>
                        <span id="btnFetchText">Baca Link</span>
                    </button>
                </div>

                <!-- Optional Facebook Session Cookie for Login-Restricted Posts -->
                <details class="text-xs text-slate-600 pt-0.5">
                    <summary class="cursor-pointer font-semibold text-slate-500 hover:text-blue-600 select-none">
                        🔑 Opsional: Gunakan Cookie Sesi Facebook (Jika postingan personal/grup dikunci login oleh FB)
                    </summary>
                    <div class="mt-2 p-2.5 rounded-xl bg-white border border-slate-200 space-y-1.5">
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Jika Facebook menyembunyikan angka Like/Komen/Share karena privasi akun atau anti-bot tanpa login, tempelkan Cookie browser Facebook Anda (<code class="bg-slate-100 px-1 rounded">c_user=...; xs=...;</code>) dari tab Network DevTools (F12):
                        </p>
                        <input type="text" id="inputFbCookie" name="fb_cookie"
                            oninput="localStorage.setItem('grapari_fb_cookie', this.value)"
                            placeholder="Contoh: c_user=1000...; xs=45%3A...;"
                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-mono">
                    </div>
                </details>

                <!-- Fetch Status Banner -->
                <div id="fetchStatusBanner" class="hidden rounded-xl px-3 py-2 text-xs font-semibold flex items-start justify-between gap-2">
                    <span id="fetchStatusText" class="leading-relaxed"></span>
                    <span id="fetchPostTypeBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white/80 shrink-0"></span>
                </div>
            </div>

            <!-- STEP 3: AUTO-DETECTED METRICS & DETAILS -->
            <div class="space-y-3 pt-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700">3. Hasil Pembacaan Otomatis (Likes, Komentar & Share)</span>
                    <span class="text-[11px] text-slate-400">Otomatis terisi dari link (dapat disesuaikan)</span>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="p-2.5 rounded-xl bg-blue-50/60 border border-blue-100">
                        <label class="block text-[11px] font-bold text-blue-700 mb-1">👍 Jumlah Likes</label>
                        <input type="number" id="inputLikes" name="likes" placeholder="Otomatis" min="0"
                            class="w-full px-2.5 py-1.5 rounded-lg border border-blue-200 bg-white text-sm font-extrabold text-slate-800">
                    </div>
                    <div class="p-2.5 rounded-xl bg-purple-50/60 border border-purple-100">
                        <label class="block text-[11px] font-bold text-purple-700 mb-1">💬 Jumlah Komentar</label>
                        <input type="number" id="inputComments" name="comments" placeholder="Otomatis" min="0"
                            class="w-full px-2.5 py-1.5 rounded-lg border border-purple-200 bg-white text-sm font-extrabold text-slate-800">
                    </div>
                    <div class="p-2.5 rounded-xl bg-emerald-50/60 border border-emerald-100">
                        <label class="block text-[11px] font-bold text-emerald-700 mb-1">🔁 Jumlah Share</label>
                        <input type="number" id="inputShares" name="shares" placeholder="Otomatis" min="0"
                            class="w-full px-2.5 py-1.5 rounded-lg border border-emerald-200 bg-white text-sm font-extrabold text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Konten / Postingan (Terbaca Otomatis)</label>
                    <input type="text" id="inputTitle" name="title" placeholder="Otomatis terisi saat link ditempel..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Label Kampanye Banner</label>
                        <input type="text" id="inputCampaignTag" name="campaign_tag" value="Paket JITU"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sub-judul Banner</label>
                        <input type="text" id="inputBannerSubtitle" name="banner_subtitle" value="Internet Lebih Cepat Harga Lebih Hemat!"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="set_featured" value="1" id="set_featured" checked class="rounded text-red-600">
                <label for="set_featured" class="text-xs font-medium text-slate-600">Tampilkan sebagai Detail Konten Terbaru di Dashboard utama</label>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalAddContent').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Batal</button>
                <button type="submit" class="pill-active-red px-5 py-2.5 rounded-xl text-xs font-bold cursor-pointer">Simpan Konten</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== JAVASCRIPT & CHART.JS CONFIGURATION ==================== -->
<script>
    // Data passed from Laravel Eloquent Controller
    const grapariList = @json($graparis);
    const contentList = @json($contents);
    const dailyMetrics = @json($dailyMetrics);

    // Format number with Indonesian dot separator (e.g., 1.500)
    function formatIDNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // 1. Initialize "Performa 10 GraPARI" Grouped Bar Chart (Directly using real Likes, Komentar, Share)
    const barCtx = document.getElementById('performaBarChart').getContext('2d');
    const barLabels = grapariList.map(g => ['GraPARI', g.short_name]);

    const baseChartLikes = grapariList.map(g => Number(g.likes));
    const baseChartComments = grapariList.map(g => Number(g.comments));
    const baseChartChats = grapariList.map(g => Number(g.direct_chats));

    const performaBarChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [
                {
                    label: 'Likes',
                    data: [...baseChartLikes],
                    backgroundColor: '#2563EB',
                    hoverBackgroundColor: '#1D4ED8',
                    borderRadius: { topLeft: 4, topRight: 4 },
                    barPercentage: 0.78,
                    categoryPercentage: 0.72
                },
                {
                    label: 'Komentar',
                    data: [...baseChartComments],
                    backgroundColor: '#8B5CF6',
                    hoverBackgroundColor: '#7C3AED',
                    borderRadius: { topLeft: 4, topRight: 4 },
                    barPercentage: 0.78,
                    categoryPercentage: 0.72
                },
                {
                    label: 'Share',
                    data: [...baseChartChats],
                    backgroundColor: '#10B981',
                    hoverBackgroundColor: '#059669',
                    borderRadius: { topLeft: 4, topRight: 4 },
                    barPercentage: 0.78,
                    categoryPercentage: 0.72
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0F172A',
                    padding: 10,
                    cornerRadius: 8,
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        title: (items) => items[0].label.replace(',', ' ')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 25,
                    ticks: {
                        stepSize: 5,
                        precision: 0,
                        color: '#475569',
                        font: { size: 11, weight: '600' },
                        callback: function(value) {
                            return formatIDNumber(value);
                        }
                    },
                    grid: {
                        color: '#F1F5F9',
                        drawBorder: false
                    },
                    border: { display: false }
                },
                x: {
                    ticks: {
                        color: '#334155',
                        font: { size: 10.5, weight: '600' },
                        maxRotation: 0,
                        minRotation: 0
                    },
                    grid: { display: false },
                    border: { color: '#E2E8F0' }
                }
            }
        }
    });

    // Interactive Filter Buttons for Performa 10 GraPARI Chart
    let currentFilterMode = 'all';
    function filterBarChart(metric) {
        const btnLikes = document.getElementById('filter-btn-likes');
        const btnComments = document.getElementById('filter-btn-comments');
        const btnChats = document.getElementById('filter-btn-chats');

        const inactiveClass = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold text-slate-600 hover:text-slate-900 transition-all cursor-pointer';
        const activeClass = 'pill-active-red flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold transition-all cursor-pointer';

        if (metric === 'all' || (currentFilterMode === metric && currentFilterMode !== 'all')) {
            currentFilterMode = 'all';
            performaBarChart.data.datasets[0].hidden = false;
            performaBarChart.data.datasets[1].hidden = false;
            performaBarChart.data.datasets[2].hidden = false;
            btnLikes.className = activeClass;
            btnComments.className = inactiveClass;
            btnChats.className = inactiveClass;
        } else {
            currentFilterMode = metric;
            performaBarChart.data.datasets[0].hidden = (metric !== 'likes');
            performaBarChart.data.datasets[1].hidden = (metric !== 'comments');
            performaBarChart.data.datasets[2].hidden = (metric !== 'chats');

            btnLikes.className = (metric === 'likes') ? activeClass : inactiveClass;
            btnComments.className = (metric === 'comments') ? activeClass : inactiveClass;
            btnChats.className = (metric === 'chats') ? activeClass : inactiveClass;
        }
        performaBarChart.update();
    }

    // 2. Initialize "Tren Aktivitas (30 Hari Terakhir)" Multi-Line Chart
    const lineCtx = document.getElementById('trenLineChart').getContext('2d');
    const lineLabels = dailyMetrics.map(d => d.label);

    const blueGrad = lineCtx.createLinearGradient(0, 0, 0, 170);
    blueGrad.addColorStop(0, 'rgba(37, 99, 235, 0.14)');
    blueGrad.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

    const trenLineChart = new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: lineLabels,
            datasets: [
                {
                    label: 'Likes',
                    data: dailyMetrics.map(d => Number(d.likes)),
                    borderColor: '#2563EB',
                    backgroundColor: blueGrad,
                    borderWidth: 2.2,
                    tension: 0.42,
                    fill: true,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#2563EB',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 5
                },
                {
                    label: 'Komentar',
                    data: dailyMetrics.map(d => Number(d.comments)),
                    borderColor: '#8B5CF6',
                    backgroundColor: 'transparent',
                    borderWidth: 2.2,
                    tension: 0.42,
                    fill: false,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#8B5CF6',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 5
                },
                {
                    label: 'Share',
                    data: dailyMetrics.map(d => Number(d.direct_chats)),
                    borderColor: '#10B981',
                    backgroundColor: 'transparent',
                    borderWidth: 2.2,
                    tension: 0.42,
                    fill: false,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#10B981',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0F172A',
                    padding: 10,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 25,
                    ticks: {
                        stepSize: 5,
                        precision: 0,
                        color: '#475569',
                        font: { size: 11, weight: '600' },
                        callback: function(value) {
                            return formatIDNumber(value);
                        }
                    },
                    grid: {
                        color: '#F1F5F9'
                    },
                    border: { display: false }
                },
                x: {
                    ticks: {
                        color: '#475569',
                        font: { size: 11, weight: '600' },
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0,
                        callback: function(val, index, ticks) {
                            const total = ticks.length;
                            const dayNum = index + 1;
                            if (dayNum === 1 || dayNum === 7 || dayNum === 14 || dayNum === 21 || index === total - 1) {
                                return this.getLabelForValue(val);
                            }
                            return '';
                        }
                    },
                    grid: {
                        color: '#F8FAFC'
                    },
                    border: { color: '#E2E8F0' }
                }
            }
        }
    });

    // 3. Per-GraPARI Detail View & Line Chart (Triggered by clicking GraPARI in Ranking Performa)
    let currentSelectedGrapariId = {{ $selectedGrapariId ? (int) $selectedGrapariId : ($graparis->first()->id ?? 1) }};

    const detailLineCtx = document.getElementById('grapariDetailLineChart').getContext('2d');
    const detailBlueGrad = detailLineCtx.createLinearGradient(0, 0, 0, 220);
    detailBlueGrad.addColorStop(0, 'rgba(37, 99, 235, 0.16)');
    detailBlueGrad.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

    const grapariDetailLineChart = new Chart(detailLineCtx, {
        type: 'line',
        data: {
            labels: lineLabels,
            datasets: [
                {
                    label: 'Likes',
                    data: dailyMetrics.map(() => 0),
                    borderColor: '#2563EB',
                    backgroundColor: detailBlueGrad,
                    borderWidth: 2.4,
                    tension: 0.38,
                    fill: true,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#2563EB',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 5.5
                },
                {
                    label: 'Komentar',
                    data: dailyMetrics.map(() => 0),
                    borderColor: '#8B5CF6',
                    backgroundColor: 'transparent',
                    borderWidth: 2.2,
                    tension: 0.38,
                    fill: false,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#8B5CF6',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 5.5
                },
                {
                    label: 'Share',
                    data: dailyMetrics.map(() => 0),
                    borderColor: '#10B981',
                    backgroundColor: 'transparent',
                    borderWidth: 2.2,
                    tension: 0.38,
                    fill: false,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#10B981',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 5.5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0F172A',
                    padding: 10,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 25,
                    ticks: {
                        stepSize: 5,
                        precision: 0,
                        color: '#475569',
                        font: { size: 11, weight: '600' },
                        callback: function(value) {
                            return formatIDNumber(value);
                        }
                    },
                    grid: { color: '#F1F5F9' },
                    border: { display: false }
                },
                x: {
                    ticks: {
                        color: '#475569',
                        font: { size: 11, weight: '600' },
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0,
                        callback: function(val, index, ticks) {
                            const total = ticks.length;
                            if (total <= 12) {
                                return this.getLabelForValue(val);
                            }
                            const dayNum = index + 1;
                            if (dayNum === 1 || dayNum === 7 || dayNum === 14 || dayNum === 21 || index === total - 1) {
                                return this.getLabelForValue(val);
                            }
                            return '';
                        }
                    },
                    grid: { color: '#F8FAFC' },
                    border: { color: '#E2E8F0' }
                }
            }
        }
    });

    function openAddContentModal(grapariId = null) {
        const select = document.getElementById('modalGrapariSelect');
        if (select && (grapariId || currentSelectedGrapariId)) {
            select.value = grapariId || currentSelectedGrapariId;
        }
        document.getElementById('modalAddContent').classList.remove('hidden');
    }

    function updateGrapariDetailLineChart(grapariId) {
        const branchContents = contentList
            .filter(c => Number(c.grapari_id) === Number(grapariId))
            .slice()
            .reverse();

        if (branchContents.length > 0) {
            // Plot line chart of contents for this GraPARI (each content item along X-axis with its date & title)
            const labels = branchContents.map((c, idx) => {
                const shortTitle = (c.title || 'Konten').substring(0, 18);
                const datePart = (c.published_display || '').split('•')[0].trim();
                return `#${idx + 1} ${shortTitle}.. (${datePart})`;
            });
            grapariDetailLineChart.data.labels = labels;
            grapariDetailLineChart.data.datasets[0].data = branchContents.map(c => Number(c.likes));
            grapariDetailLineChart.data.datasets[1].data = branchContents.map(c => Number(c.comments));
            grapariDetailLineChart.data.datasets[2].data = branchContents.map(c => Number(c.direct_chats));
        } else {
            // When 0 contents, show clean monthly baseline at 0
            grapariDetailLineChart.data.labels = dailyMetrics.map(d => d.label);
            grapariDetailLineChart.data.datasets[0].data = dailyMetrics.map(() => 0);
            grapariDetailLineChart.data.datasets[1].data = dailyMetrics.map(() => 0);
            grapariDetailLineChart.data.datasets[2].data = dailyMetrics.map(() => 0);
        }
        grapariDetailLineChart.update();
    }

    function openGrapariDetail(grapariId) {
        const grapari = grapariList.find(g => Number(g.id) === Number(grapariId));
        if (!grapari) return;

        currentSelectedGrapariId = Number(grapari.id);

        // Also update the main dashboard featured preview box
        const latestPost = contentList.find(c => Number(c.grapari_id) === Number(grapari.id));
        document.getElementById('featured-grapari').textContent = grapari.name;
        const bannerGrapari = document.getElementById('featured-banner-grapari');
        if (bannerGrapari) {
            bannerGrapari.textContent = grapari.name;
        }
        if (latestPost) {
            document.getElementById('featured-title').textContent = latestPost.title;
            document.getElementById('featured-date').textContent = latestPost.published_display || '-';
            document.getElementById('featured-likes').textContent = latestPost.likes;
            document.getElementById('featured-comments').textContent = latestPost.comments;
            document.getElementById('featured-chats').textContent = latestPost.direct_chats;
        } else {
            document.getElementById('featured-title').textContent = `Belum ada konten untuk ${grapari.name}`;
            document.getElementById('featured-date').textContent = '-';
            document.getElementById('featured-likes').textContent = 0;
            document.getElementById('featured-comments').textContent = 0;
            document.getElementById('featured-chats').textContent = 0;
        }

        // Populate Detail Section Header & KPIs
        document.getElementById('detail-grapari-title').textContent = grapari.name;
        document.getElementById('detail-grapari-rank-badge').textContent = `Rank #${grapari.rank}`;
        document.getElementById('detail-grapari-address').textContent = grapari.address || 'Cluster Northern Manado';
        document.getElementById('chart-grapari-name').textContent = grapari.name;
        document.getElementById('list-grapari-name').textContent = grapari.name;

        const switcher = document.getElementById('detailGrapariSwitcher');
        if (switcher) {
            switcher.value = grapari.id;
        }

        document.getElementById('detail-kpi-contents').textContent = grapari.total_contents;
        document.getElementById('detail-kpi-likes').textContent = formatIDNumber(grapari.likes);
        document.getElementById('detail-kpi-comments').textContent = formatIDNumber(grapari.comments);
        document.getElementById('detail-kpi-shares').textContent = formatIDNumber(grapari.direct_chats);
        document.getElementById('detail-kpi-rate').textContent = Number(grapari.engagement_rate).toFixed(1) + '%';

        // Filter content cards to only show this GraPARI's contents
        filterContentCards();

        // Update the per-GraPARI Line Chart
        updateGrapariDetailLineChart(grapari.id);

        // Show Detail Section & Hide Main Dashboard Overview
        document.getElementById('tab-dashboard').classList.add('hidden');
        document.getElementById('tab-dashboard').classList.remove('block');
        document.getElementById('tab-laporan').classList.add('hidden');
        document.getElementById('tab-pengaturan').classList.add('hidden');

        const detailSection = document.getElementById('tab-grapari-detail');
        detailSection.classList.remove('hidden');
        detailSection.classList.add('block');
    }

    function closeGrapariDetail() {
        const detailSection = document.getElementById('tab-grapari-detail');
        detailSection.classList.add('hidden');
        detailSection.classList.remove('block');

        const dashSection = document.getElementById('tab-dashboard');
        dashSection.classList.remove('hidden');
        dashSection.classList.add('block');
    }

    // 4. Period Filter Simulation
    function handlePeriodChange(period) {
        const multipliers = {
            'nov-full': 1,
            'nov-h2': 0.54,
            'nov-h1': 0.46,
            'oct-full': 0.91
        };
        const m = multipliers[period] || 1;

        document.getElementById('kpi-konten').textContent = Math.round({{ $summary['total_konten'] }} * m);
        document.getElementById('kpi-likes').textContent = formatIDNumber(Math.round({{ $summary['total_likes'] }} * m));
        document.getElementById('kpi-komentar').textContent = formatIDNumber(Math.round({{ $summary['total_komentar'] }} * m));
        document.getElementById('kpi-chat').textContent = formatIDNumber(Math.round({{ $summary['total_direct_chat'] }} * m));

        performaBarChart.data.datasets[0].data = baseChartLikes.map(v => Math.round(v * m));
        performaBarChart.data.datasets[1].data = baseChartComments.map(v => Math.round(v * m));
        performaBarChart.data.datasets[2].data = baseChartChats.map(v => Math.round(v * m));
        performaBarChart.update();
    }

    // 5. Sidebar Tab Navigation
    function switchTab(tabName) {
        const detailSection = document.getElementById('tab-grapari-detail');
        if (detailSection) {
            detailSection.classList.add('hidden');
            detailSection.classList.remove('block');
        }

        const tabs = ['dashboard', 'laporan', 'pengaturan'];
        tabs.forEach(t => {
            const section = document.getElementById('tab-' + t);
            const navBtn = document.getElementById('nav-' + t);
            if (!section || !navBtn) return;
            if (t === tabName) {
                section.classList.remove('hidden');
                section.classList.add('block');
                navBtn.classList.add('nav-active');
                navBtn.classList.remove('text-slate-600');
            } else {
                section.classList.remove('block');
                section.classList.add('hidden');
                navBtn.classList.remove('nav-active');
                navBtn.classList.add('text-slate-600');
            }
        });
    }

    // 6. Filter Content Cards for Currently Selected GraPARI + Search Query
    function filterContentCards() {
        const q = ((document.getElementById('searchContentInput') || {}).value || '').toLowerCase();
        let visibleCount = 0;

        document.querySelectorAll('.content-card-item').forEach(card => {
            const cardGrapariId = Number(card.getAttribute('data-content-grapari'));
            const text = card.getAttribute('data-search') || '';
            const matchesGrapari = cardGrapariId === Number(currentSelectedGrapariId);
            const matchesQuery = text.includes(q);

            if (matchesGrapari && matchesQuery) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const emptyState = document.getElementById('grapariEmptyContentState');
        const emptyTitle = document.getElementById('grapariEmptyTitle');
        const currentGrapari = grapariList.find(g => Number(g.id) === Number(currentSelectedGrapariId));
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
                if (emptyTitle && currentGrapari) {
                    emptyTitle.textContent = `Belum ada konten promosi untuk ${currentGrapari.name}`;
                }
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    @if($selectedGrapariId)
        window.addEventListener('DOMContentLoaded', () => {
            openGrapariDetail({{ (int) $selectedGrapariId }});
        });
    @endif

    // 7. Export Ranking Table to CSV
    function exportTableToCSV() {
        let csv = 'Rank,Nama GraPARI,Total Konten,Likes,Komentar,Share,Engagement Rate\n';
        grapariList.forEach(g => {
            csv += `${g.rank},"${g.name}",${g.total_contents},${g.likes},${g.comments},${g.direct_chats},${g.engagement_rate}%\n`;
        });
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Laporan_Performa_GraPARI_Manado_Nov2026.csv';
        a.click();
    }

    // 8. Auto-Fetch Facebook Marketplace / Personal Post Link Metrics
    let fetchDebounceTimer = null;
    let lastFetchedUrl = '';

    // Restore saved FB cookie if any
    const savedFbCookie = localStorage.getItem('grapari_fb_cookie');
    if (savedFbCookie && document.getElementById('inputFbCookie')) {
        document.getElementById('inputFbCookie').value = savedFbCookie;
    }

    function handlePostUrlInput(val) {
        clearTimeout(fetchDebounceTimer);
        const trimmed = (val || '').trim();
        if (trimmed.startsWith('http://') || trimmed.startsWith('https://')) {
            fetchDebounceTimer = setTimeout(() => {
                if (trimmed !== lastFetchedUrl) {
                    fetchFacebookLinkMetrics();
                }
            }, 400);
        }
    }

    async function fetchFacebookLinkMetrics() {
        const urlInput = document.getElementById('inputPostUrl');
        const cookieInput = document.getElementById('inputFbCookie');
        const postUrl = (urlInput.value || '').trim();
        const fbCookie = cookieInput ? (cookieInput.value || '').trim() : '';
        const statusBanner = document.getElementById('fetchStatusBanner');
        const statusText = document.getElementById('fetchStatusText');
        const typeBadge = document.getElementById('fetchPostTypeBadge');
        const btnText = document.getElementById('btnFetchText');
        const btnIcon = document.getElementById('btnFetchIcon');

        if (!postUrl || (!postUrl.startsWith('http://') && !postUrl.startsWith('https://'))) {
            statusBanner.className = 'rounded-xl px-3 py-2 text-xs font-semibold flex items-start justify-between gap-2 bg-amber-50 text-amber-700 border border-amber-200';
            statusText.textContent = '⚠️ Masukkan link URL Facebook yang valid (diawali https://)';
            typeBadge.textContent = '';
            return;
        }

        lastFetchedUrl = postUrl;
        btnIcon.textContent = '⏳';
        btnText.textContent = 'Membaca...';
        statusBanner.className = 'rounded-xl px-3 py-2 text-xs font-semibold flex items-start justify-between gap-2 bg-blue-50 text-blue-700 border border-blue-200';
        statusText.textContent = '🔄 Sedang menghubungi Facebook untuk membaca Like, Komentar, dan Share asli...';
        typeBadge.textContent = 'Connecting...';

        try {
            const response = await fetch('{{ route('contents.fetch-link') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    post_url: postUrl,
                    fb_cookie: fbCookie
                })
            });

            const result = await response.json();

            if (response.ok && result.success && result.data) {
                const d = result.data;
                document.getElementById('inputLikes').value = d.likes;
                document.getElementById('inputComments').value = d.comments;
                document.getElementById('inputShares').value = d.shares;
                document.getElementById('inputTitle').value = d.title;
                document.getElementById('inputCampaignTag').value = d.campaign_tag;
                document.getElementById('inputBannerSubtitle').value = d.banner_subtitle;

                if (d.has_live_metrics) {
                    statusBanner.className = 'rounded-xl px-3 py-2 text-xs font-semibold flex items-start justify-between gap-2 bg-emerald-50 text-emerald-800 border border-emerald-200';
                    statusText.textContent = '✅ ' + (d.message || `Data asli terbaca: ${d.likes} Likes • ${d.comments} Komentar • ${d.shares} Share`);
                } else {
                    statusBanner.className = 'rounded-xl px-3 py-2 text-xs font-semibold flex items-start justify-between gap-2 bg-amber-50 text-amber-800 border border-amber-200';
                    statusText.textContent = '⚠️ ' + (d.message || 'Facebook mengunci angka metrik untuk akses tanpa login.');
                }
                typeBadge.textContent = d.post_type || 'Facebook Post';
            } else {
                statusBanner.className = 'rounded-xl px-3 py-2 text-xs font-semibold flex items-start justify-between gap-2 bg-red-50 text-red-700 border border-red-200';
                statusText.textContent = '❌ Gagal membaca link. Pastikan format URL sudah benar.';
                typeBadge.textContent = 'Error';
            }
        } catch (err) {
            statusBanner.className = 'rounded-xl px-3 py-2 text-xs font-semibold flex items-start justify-between gap-2 bg-red-50 text-red-700 border border-red-200';
            statusText.textContent = '❌ Terjadi kesalahan koneksi saat membaca link.';
            typeBadge.textContent = 'Error';
        } finally {
            btnIcon.textContent = '🔍';
            btnText.textContent = 'Baca Link';
        }
    }

    // 9. Real-Time Background Auto-Sync with Facebook Links
    let isAutoSyncRunning = false;

    async function runAutoSyncNow() {
        if (isAutoSyncRunning) return;
        isAutoSyncRunning = true;

        const syncIcon = document.getElementById('autoSyncIcon');
        const cookieInput = document.getElementById('inputFbCookie');
        const fbCookie = (cookieInput ? cookieInput.value : localStorage.getItem('grapari_fb_cookie')) || '';

        if (syncIcon) {
            syncIcon.classList.add('animate-spin');
        }

        try {
            const response = await fetch('{{ route('contents.sync-all') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ fb_cookie: fbCookie })
            });

            const result = await response.json();
            if (response.ok && result.success) {
                // 1. Update KPI Summary Cards
                if (result.summary) {
                    document.getElementById('kpi-konten').textContent = result.summary.total_konten;
                    document.getElementById('kpi-likes').textContent = formatIDNumber(result.summary.total_likes);
                    document.getElementById('kpi-komentar').textContent = formatIDNumber(result.summary.total_komentar);
                    document.getElementById('kpi-chat').textContent = formatIDNumber(result.summary.total_share);
                }

                // 2. Update Featured Content Box
                if (result.featured_content) {
                    const fc = result.featured_content;
                    if (fc.grapari && fc.grapari.name) {
                        document.getElementById('featured-grapari').textContent = fc.grapari.name;
                        const bannerGrapari = document.getElementById('featured-banner-grapari');
                        if (bannerGrapari) {
                            bannerGrapari.textContent = fc.grapari.name;
                        }
                    }
                    document.getElementById('featured-title').textContent = fc.title;
                    if (fc.published_display) {
                        document.getElementById('featured-date').textContent = fc.published_display;
                    }
                    document.getElementById('featured-likes').textContent = fc.likes;
                    document.getElementById('featured-comments').textContent = fc.comments;
                    document.getElementById('featured-chats').textContent = fc.direct_chats;
                }

                // 3. Update Ranking Table & Bar Chart Datasets (using real Likes, Komentar, Share)
                if (Array.isArray(result.graparis)) {
                    result.graparis.forEach((g, idx) => {
                        grapariList[idx] = g;
                        baseChartLikes[idx] = Number(g.likes);
                        baseChartComments[idx] = Number(g.comments);
                        baseChartChats[idx] = Number(g.direct_chats);

                        const elContents = document.querySelector(`[data-grapari-contents="${g.id}"]`);
                        const elLikes = document.querySelector(`[data-grapari-likes="${g.id}"]`);
                        const elComments = document.querySelector(`[data-grapari-comments="${g.id}"]`);
                        const elShares = document.querySelector(`[data-grapari-shares="${g.id}"]`);
                        const elRate = document.querySelector(`[data-grapari-rate="${g.id}"]`);

                        if (elContents) elContents.textContent = g.total_contents;
                        if (elLikes) elLikes.textContent = g.likes;
                        if (elComments) elComments.textContent = g.comments;
                        if (elShares) elShares.textContent = g.direct_chats;
                        if (elRate) elRate.textContent = Number(g.engagement_rate).toFixed(1) + '%';
                    });

                    performaBarChart.data.datasets[0].data = [...baseChartLikes];
                    performaBarChart.data.datasets[1].data = [...baseChartComments];
                    performaBarChart.data.datasets[2].data = [...baseChartChats];
                    performaBarChart.update();
                }

                // 3b. Update Tren Aktivitas Line Chart Datasets & Real-Time Dates
                if (Array.isArray(result.daily_metrics) && result.daily_metrics.length > 0) {
                    trenLineChart.data.labels = result.daily_metrics.map(d => d.label);
                    trenLineChart.data.datasets[0].data = result.daily_metrics.map(d => Number(d.likes));
                    trenLineChart.data.datasets[1].data = result.daily_metrics.map(d => Number(d.comments));
                    trenLineChart.data.datasets[2].data = result.daily_metrics.map(d => Number(d.direct_chats));
                    trenLineChart.update();

                    const rangeEl = document.getElementById('trend-date-range');
                    if (rangeEl) {
                        const firstLabel = result.daily_metrics[0].label;
                        const lastLabel = result.daily_metrics[result.daily_metrics.length - 1].label;
                        rangeEl.textContent = `${firstLabel} – ${lastLabel}`;
                    }
                }

                // 4. Update Detail Konten Cards & Per-GraPARI Line Chart
                if (Array.isArray(result.contents)) {
                    result.contents.forEach((c, idx) => {
                        contentList[idx] = c;
                        const elLikes = document.querySelector(`[data-content-likes="${c.id}"]`);
                        const elComments = document.querySelector(`[data-content-comments="${c.id}"]`);
                        const elShares = document.querySelector(`[data-content-shares="${c.id}"]`);

                        if (elLikes) elLikes.textContent = c.likes;
                        if (elComments) elComments.textContent = c.comments;
                        if (elShares) elShares.textContent = c.direct_chats;
                    });

                    if (currentSelectedGrapariId) {
                        const activeBranch = grapariList.find(g => Number(g.id) === Number(currentSelectedGrapariId));
                        if (activeBranch) {
                            document.getElementById('detail-kpi-contents').textContent = activeBranch.total_contents;
                            document.getElementById('detail-kpi-likes').textContent = formatIDNumber(activeBranch.likes);
                            document.getElementById('detail-kpi-comments').textContent = formatIDNumber(activeBranch.comments);
                            document.getElementById('detail-kpi-shares').textContent = formatIDNumber(activeBranch.direct_chats);
                            document.getElementById('detail-kpi-rate').textContent = Number(activeBranch.engagement_rate).toFixed(1) + '%';
                        }
                        updateGrapariDetailLineChart(currentSelectedGrapariId);
                    }
                }
            }
        } catch (err) {
            // Silently ignore transient network issues during background auto-sync
        } finally {
            if (syncIcon) {
                syncIcon.classList.remove('animate-spin');
            }
            isAutoSyncRunning = false;
        }
    }

    // Run Auto-Sync periodically in the background every 45 seconds
    setInterval(runAutoSyncNow, 45000);
</script>
</body>
</html>
