@extends('layouts.admin')

@section('title', 'Quản lý Người Dùng — Admin LANDTEK')

@section('content')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.tailwindcss.css">

<style>
    /* ===========================
       BASE RESET & TYPOGRAPHY
    =========================== */
    .prop-page {
        min-height: 100vh;
        background: #f8fafc;
        padding: 2rem 0 3rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .prop-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    @media (min-width: 640px)  { .prop-container { padding: 0 1.5rem; } }
    @media (min-width: 1024px) { .prop-container { padding: 0 2rem; } }

    /* ===========================
       PAGE HEADER
    =========================== */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .page-breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.7rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.375rem;
    }
    .page-breadcrumb i { font-size: 0.6rem; }
    .page-title {
        font-size: clamp(1.4rem, 4vw, 1.875rem);
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }
    .page-subtitle {
        font-size: 0.8125rem;
        color: #94a3b8;
        margin-top: 0.25rem;
        font-weight: 500;
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: oklch(58% 0.13 218);
        background: oklch(58% 0.13 218 / 8%);
        border: 1px solid oklch(58% 0.13 218 / 20%);
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .btn-back:hover {
        background: oklch(58% 0.13 218 / 14%);
        border-color: oklch(58% 0.13 218 / 35%);
    }

    /* ===========================
       STAT CARDS
    =========================== */
    .stat-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.875rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 480px) {
        .stat-cards { grid-template-columns: 1fr; gap: 0.625rem; }
    }
    .stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1rem 1.125rem;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.875rem;
        border-left: 3px solid transparent;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: default;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -6px rgba(0,0,0,0.1);
    }
    .stat-card.total  { border-left-color: oklch(58% 0.13 218); }
    .stat-card.active { border-left-color: #10b981; }
    .stat-card.pending{ border-left-color: #f59e0b; }
    .stat-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.625rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .stat-icon.blue   { background: oklch(58% 0.13 218 / 10%); color: oklch(58% 0.13 218); }
    .stat-icon.green  { background: #ecfdf5; color: #059669; }
    .stat-icon.amber  { background: #fffbeb; color: #d97706; }
    .stat-info { min-width: 0; }
    .stat-value {
        font-size: 1.375rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }
    .stat-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 0.2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ===========================
       FILTER BAR
    =========================== */
    .filter-bar {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    @media (min-width: 1024px) {
        .filter-bar {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 1.25rem;
            gap: 1rem;
        }
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        width: 100%;
    }
    @media (min-width: 480px) {
        .filter-group { flex-direction: row; flex-wrap: wrap; }
    }
    @media (min-width: 1024px) {
        .filter-group { width: auto; flex-wrap: nowrap; align-items: center; }
    }
    .filter-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .filter-label i {
        color: oklch(58% 0.13 218);
        font-size: 0.7rem;
    }
    .filter-select {
        flex: 1;
        min-width: 130px;
        max-width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 0.625rem;
        padding: 0.5rem 0.875rem;
        font-size: 0.8125rem;
        font-weight: 500;
        color: #374151;
        background: #f8fafc;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        padding-right: 2rem;
    }
    .filter-select:focus {
        border-color: oklch(58% 0.13 218);
        box-shadow: 0 0 0 3px oklch(58% 0.13 218 / 12%);
        background-color: white;
    }
    @media (min-width: 1024px) {
        .filter-select { max-width: 160px; }
    }

    .search-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        width: 100%;
    }
    @media (min-width: 480px) {
        .search-group { flex-direction: row; align-items: center; }
    }
    @media (min-width: 1024px) {
        .search-group { width: auto; flex-wrap: nowrap; }
    }

    .search-wrapper {
        position: relative;
        flex: 1;
        min-width: 0;
    }
    @media (min-width: 1024px) {
        .search-wrapper { width: 220px; flex: none; }
    }
    .search-wrapper svg {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        width: 14px;
        height: 14px;
        color: #94a3b8;
        pointer-events: none;
    }
    .search-input {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 0.625rem;
        padding: 0.5rem 0.875rem 0.5rem 2.25rem;
        font-size: 0.8125rem;
        font-weight: 500;
        color: #374151;
        background: #f8fafc;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .search-input::placeholder { color: #94a3b8; }
    .search-input:focus {
        border-color: oklch(58% 0.13 218);
        box-shadow: 0 0 0 3px oklch(58% 0.13 218 / 12%);
        background: white;
    }
    .length-select {
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
        border-radius: 0.625rem;
        padding: 0.5rem 2rem 0.5rem 0.875rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #374151;
        background: #f8fafc;
        outline: none;
        transition: border-color 0.2s;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.625rem center;
        width: 100%;
    }
    @media (min-width: 480px) {
        .length-select { width: auto; }
    }
    .length-select:focus {
        border-color: oklch(58% 0.13 218);
        box-shadow: 0 0 0 3px oklch(58% 0.13 218 / 12%);
        background-color: white;
    }
    .filter-divider {
        display: none;
        width: 1px;
        height: 1.5rem;
        background: #e2e8f0;
        flex-shrink: 0;
    }
    @media (min-width: 1024px) {
        .filter-divider { display: block; }
    }

    /* ===========================
       TABLE CARD
    =========================== */
    .table-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    /* DataTable overrides */
    div.dt-container .dt-layout-row {
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        border-top: 1px solid #f1f5f9;
    }
    div.dt-container .dt-layout-row:first-child { border-top: none; }

    .dt-search, .dt-length { display: none !important; }

    .dt-info { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }
    .dt-paging { display: flex; gap: 0.25rem; }
    .dt-paging-button {
        padding: 0.4rem 0.75rem !important;
        border-radius: 0.5rem !important;
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        color: #64748b !important;
        background: transparent !important;
        border: 1px solid transparent !important;
        transition: all 0.15s ease;
        min-width: 2rem;
        text-align: center;
    }
    .dt-paging-button:hover:not(.disabled) {
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }
    .dt-paging-button.current {
        background: oklch(58% 0.13 218) !important;
        color: white !important;
        border-color: transparent !important;
        box-shadow: 0 2px 8px -2px oklch(58% 0.13 218 / 40%) !important;
    }
    .dt-paging-button.disabled { opacity: 0.35; cursor: not-allowed; }

    /* Table core styles */
    table.dataTable {
        width: 100% !important;
        border-collapse: collapse;
    }
    table.dataTable thead th {
        background: #f8fafc !important;
        color: #94a3b8 !important;
        font-weight: 700 !important;
        font-size: 0.6875rem !important;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-bottom: 1px solid #e2e8f0 !important;
        border-top: none !important;
        padding: 0.75rem 0.5rem !important;
    }
    table.dataTable tbody tr {
        border-bottom: 1px solid #f1f5f9 !important;
        transition: background-color 0.15s ease;
    }
    table.dataTable tbody tr:hover { background-color: #f8fafc !important; }
    table.dataTable tbody tr:last-child { border-bottom: none !important; }

    #usersTable th, #usersTable td {
        padding: 0.75rem 0.875rem !important;
        font-size: 0.875rem !important;
        vertical-align: middle;
        word-wrap: break-word;
    }

    /* Column widths */
    #usersTable { table-layout: auto; width: 100% !important; }
    #usersTable th:nth-child(1) { width: 42px; text-align: center; }
    #usersTable th:nth-child(3) { text-align: right; }
    @media (min-width: 1024px) {
        #usersTable { table-layout: fixed; }
        #usersTable th:nth-child(1) { width: 5%; }
        #usersTable th:nth-child(2) { width: 80%; }
        #usersTable th:nth-child(3) { width: 15%; }
    }

    /* Responsive child row */
    table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before,
    table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control::before {
        background-color: oklch(58% 0.13 218) !important;
        border-color: oklch(58% 0.13 218 / 30%) !important;
        box-shadow: 0 0 0 3px oklch(58% 0.13 218 / 15%) !important;
        width: 14px !important;
        height: 14px !important;
    }
    table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control::before,
    table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.dtr-control::before {
        background-color: #ef4444 !important;
        border-color: #fca5a5 !important;
    }
    tr.child td.child { padding: 0.75rem 1rem !important; background: #fafbfc; }
    ul.dtr-details {
        display: flex !important;
        flex-direction: column;
        gap: 0.5rem;
    }
    ul.dtr-details li {
        display: flex !important;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.375rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    ul.dtr-details li:last-child { border-bottom: none; }
    span.dtr-title {
        font-size: 0.6875rem !important;
        font-weight: 700 !important;
        color: #94a3b8 !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
        min-width: 80px;
        flex-shrink: 0;
    }
    span.dtr-data { font-size: 0.8125rem !important; }

    /* Action dropdown */
    #floatingDropdown {
        position: fixed;
        width: 200px;
        background: white;
        border-radius: 0.875rem;
        box-shadow: 0 10px 30px -5px rgba(0,0,0,0.15), 0 4px 12px -3px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        z-index: 99999;
        display: none;
    }
    #floatingDropdown.is-open { display: block; }
    .dm-section { border-top: 1px solid #f1f5f9; }
    .dm-section:first-child { border-top: none; }
    .dm-item {
        display: flex;
        align-items: center;
        gap: 9px;
        width: 100%;
        padding: 0.5rem 0.875rem;
        font-size: 0.8125rem;
        font-weight: 500;
        color: #374151;
        background: none;
        border: none;
        cursor: pointer;
        text-align: left;
        text-decoration: none;
        box-sizing: border-box;
        transition: background 0.12s;
    }
    .dm-item:hover { background: #f8fafc; }
    .dm-item.green { color: #059669; }
    .dm-item.green:hover { background: #ecfdf5; }
    .dm-item.amber { color: #d97706; }
    .dm-item.amber:hover { background: #fffbeb; }
    .dm-item.blue { color: #2563eb; }
    .dm-item.blue:hover { background: #eff6ff; }
    .dm-item.red { color: #dc2626; }
    .dm-item.red:hover { background: #fef2f2; }
    .dm-item i { width: 14px; text-align: center; flex-shrink: 0; font-size: 0.75rem; }

    .dropdown-btn {
        width: 30px; height: 30px;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
        background: white;
        color: #94a3b8;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .dropdown-btn:hover { background: #f0fdfa; color: #0d9488; border-color: #99f6e4; }
    .dropdown-btn.active { background: #f0fdfa; color: #0d9488; border-color: #99f6e4; }

    /* ===========================
       USER CELL
    =========================== */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 0.75rem;
        overflow: hidden;
        flex-shrink: 0;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-weight: 700;
        font-size: 1rem;
    }
    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .user-meta { min-width: 0; flex: 1; }
    .user-name {
        font-size: 0.875rem;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .user-contact {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.125rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="prop-page">
    <div class="prop-container">

        <!-- PAGE HEADER -->
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">
                    <i class="fas fa-home"></i>
                    <span>Admin</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Người dùng</span>
                </div>
                <h1 class="page-title">Quản lý Người dùng</h1>
                <p class="page-subtitle">Quản lý trạng thái và xét duyệt tài khoản đăng ký mới.</p>
            </div>
            <a href="{{ route('home') }}" class="btn-back">
                <i class="fas fa-arrow-left" style="font-size:0.75rem;"></i>
                Trang chủ
            </a>
        </div>

        <!-- STAT CARDS -->
        <div class="stat-cards">
            <div class="stat-card total">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="statTotal">{{ $users->count() }}</div>
                    <div class="stat-label">Tổng tài khoản</div>
                </div>
            </div>
            <div class="stat-card active">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="statActive">{{ $users->where('status', 'active')->count() }}</div>
                    <div class="stat-label">Đã duyệt (Active)</div>
                </div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon amber">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="statPending">{{ $users->where('status', 'pending')->count() }}</div>
                    <div class="stat-label">Chờ duyệt (Pending)</div>
                </div>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar">
            <!-- Left: Filters -->
            <div class="filter-group">
                <div class="filter-label">
                    <i class="fas fa-filter"></i>
                    Bộ lọc
                </div>
                <select id="filterType" class="filter-select">
                    <option value="">Tất cả loại TK</option>
                    <option value="Seller">Seller</option>
                    <option value="Buyer">Buyer</option>
                    <option value="Owner">Owner</option>
                    <option value="Renter">Renter</option>
                    <option value="Agent">Agent</option>
                    <option value="Admin">Admin</option>
                </select>
                <select id="filterStatus" class="filter-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Đã duyệt">Đã duyệt (Active)</option>
                    <option value="Chờ duyệt">Chờ duyệt (Pending)</option>
                    <option value="Tạm khóa">Tạm khóa (Inactive)</option>
                    <option value="Cấm">Cấm (Banned)</option>
                </select>
            </div>

            <div class="filter-divider"></div>

            <!-- Right: Length + Search -->
            <div class="search-group">
                <select id="customLength" class="length-select">
                    <option value="10">10 / trang</option>
                    <option value="25">25 / trang</option>
                    <option value="50">50 / trang</option>
                    <option value="100">100 / trang</option>
                </select>
                <div class="search-wrapper">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="customSearch" class="search-input" placeholder="Tìm kiếm...">
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="table-card">
            <div class="table-wrapper">
                <table id="usersTable" class="w-full text-left hover" style="width:100%">
                    <thead>
                        <tr>
                            <th class="all">STT</th>
                            <th class="all">Người dùng</th>
                            <th class="min-tablet">Loại TK</th>
                            <th class="min-tablet">Ngày đăng ký</th>
                            <th class="all">Trạng thái</th>
                            <th class="all" style="text-align:right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="text-center" style="font-size:12px;font-weight:700;color:#94a3b8;"></td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="avatar">
                                        @else
                                            {{ mb_substr($user->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="user-meta">
                                        <div class="user-name" title="{{ $user->name }}">{{ $user->name }}</div>
                                        <div class="user-contact" title="{{ $user->email ?? $user->phone }}">{{ $user->email ?? $user->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="display:inline-block;padding:3px 10px;border-radius:6px;background:#f1f5f9;color:#475569;font-size:11px;font-weight:700;text-transform:capitalize;letter-spacing:0.03em;">
                                    {{ $user->user_type }}
                                </span>
                            </td>
                            <td style="font-size:13px;color:#64748b;font-weight:500;">
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                @if($user->status == 'active')
                                    <span style="display:inline-block;padding:3px 10px;border-radius:6px;background:#ecfdf5;color:#059669;font-size:11px;font-weight:700;">Đã duyệt</span>
                                @elseif($user->status == 'pending')
                                    <span style="display:inline-block;padding:3px 10px;border-radius:6px;background:#fffbeb;color:#d97706;font-size:11px;font-weight:700;">Chờ duyệt</span>
                                @elseif($user->status == 'inactive')
                                    <span style="display:inline-block;padding:3px 10px;border-radius:6px;background:#f1f5f9;color:#64748b;font-size:11px;font-weight:700;">Tạm khóa</span>
                                @else
                                    <span style="display:inline-block;padding:3px 10px;border-radius:6px;background:#fef2f2;color:#dc2626;font-size:11px;font-weight:700;">Cấm</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;justify-content:flex-end;">
                                    <button type="button" class="dropdown-btn" title="Thao tác">
                                        <i class="fas fa-ellipsis-v" style="font-size:12px;"></i>
                                    </button>
                                    <div class="dropdown-menu-tpl" style="display:none">
                                        <div class="dm-section">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="dm-item"><i class="fas fa-edit"></i>Sửa người dùng</a>
                                        </div>
                                        <div class="dm-section">
                                            <form action="{{ route('admin.users.status', $user->id) }}" method="POST" style="margin:0">
                                                @csrf
                                                @if($user->status === 'active')
                                                    <input type="hidden" name="status" value="inactive">
                                                    <button type="submit" class="dm-item amber"><i class="fas fa-lock"></i>Khóa tài khoản</button>
                                                @else
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="dm-item green"><i class="fas fa-check-circle"></i>Duyệt tài khoản</button>
                                                @endif
                                            </form>
                                        </div>
                                        <div class="dm-section">
                                            <button type="button" class="dm-item red" onclick="deleteUser('delete-user-{{ $user->id }}')"><i class="fas fa-trash-alt"></i>Xóa người dùng</button>
                                            <form id="delete-user-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /.prop-container -->
</div><!-- /.prop-page -->

<!-- Floating Dropdown -->
<div id="floatingDropdown"></div>

<!-- jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.tailwindcss.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#usersTable').DataTable({
            responsive: {
                details: {
                    type: 'inline',
                    target: 'tr'
                }
            },
            autoWidth: false,
            language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/vi.json' },
            columnDefs: [
                { searchable: false, orderable: false, targets: 0 },
                { visible: false, targets: [2, 3, 4] },
                { orderable: false, targets: 5 }
            ],
            dom: 'rt<"dt-layout-row"ip>',
        });

        // STT Logic (auto-increment matching pagination)
        table.on('draw.dt order.dt search.dt', function () {
            var info = table.page.info();
            table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                cell.innerHTML = info.start + i + 1;
            });
        }).draw();

        // Close dropdown on redraw
        table.on('draw.dt responsive-display.dt', function() {
            closeFloatingDropdown();
        });

        // Custom Search
        $('#customSearch').on('keyup', function() { table.search(this.value).draw(); });

        // Custom Length
        $('#customLength').on('change', function() { table.page.len($(this).val()).draw(); });

        // Filter: Type
        $.fn.dataTable.ext.search.push(function(settings, data) {
            var sel = $('#filterType').val();
            if (!sel) return true;
            return (data[2] || '').toLowerCase().includes(sel.toLowerCase());
        });

        // Filter: Status
        $.fn.dataTable.ext.search.push(function(settings, data) {
            var sel = $('#filterStatus').val();
            if (!sel) return true;
            return (data[4] || '').includes(sel);
        });

        $('#filterType, #filterStatus').on('change', function() { table.draw(); });

        // ========================
        // Floating Dropdown Logic
        // ========================
        var $floatingDropdown = null;
        var $activeBtn = null;

        function closeFloatingDropdown() {
            if ($floatingDropdown) { $floatingDropdown.removeClass('is-open'); $floatingDropdown = null; }
            if ($activeBtn) { $activeBtn.removeClass('active'); $activeBtn = null; }
        }

        $(document).on('click', '.dropdown-btn', function(e) {
            e.stopPropagation();
            var $btn = $(this);
            if ($activeBtn && $activeBtn[0] === $btn[0]) { closeFloatingDropdown(); return; }
            closeFloatingDropdown();

            var $inlineMenu = $btn.siblings('.dropdown-menu-tpl');
            if (!$inlineMenu.length) return;

            var $fd = $('#floatingDropdown');
            $fd.html($inlineMenu.html());

            var rect = $btn[0].getBoundingClientRect();
            var menuWidth = 200;
            var left = rect.right - menuWidth;
            var top  = rect.bottom + 6;
            if (left < 8) left = 8;
            if (top + 200 > window.innerHeight) top = rect.top - 210;

            $fd.css({ top: top + 'px', left: left + 'px' });
            $fd.addClass('is-open');
            $activeBtn = $btn;
            $activeBtn.addClass('active');
            $floatingDropdown = $fd;
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#floatingDropdown').length && !$(e.target).closest('.dropdown-btn').length) {
                closeFloatingDropdown();
            }
        });

        table.on('draw.dt responsive-display.dt', closeFloatingDropdown);
        $(window).on('scroll resize', closeFloatingDropdown);

        // Animate stats
        animateCount('statTotal', {{ $users->count() }});
        animateCount('statActive', {{ $users->where('status', 'active')->count() }});
        animateCount('statPending', {{ $users->where('status', 'pending')->count() }});
    });

    // ========================
    // Stat counter animation
    // ========================
    function animateCount(id, target) {
        var el = document.getElementById(id);
        if (!el) return;
        var start = 0;
        var duration = 600;
        var startTime = null;
        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(eased * target);
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target;
        }
        requestAnimationFrame(step);
    }

    function deleteUser(formId) {
        closeFloatingDropdown && closeFloatingDropdown();
        Swal.fire({
            title: 'Xóa người dùng?',
            text: 'Bạn có chắc chắn muốn xóa? Hành động này không thể hoàn tác.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xóa ngay',
            cancelButtonText: 'Hủy',
            buttonsStyling: false,
            customClass: { confirmButton: 'swal-btn-danger', cancelButton: 'swal-btn-secondary' }
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    function closeFloatingDropdown() {
        $('#floatingDropdown').removeClass('is-open');
        $('.dropdown-btn.active').removeClass('active');
    }
</script>
@endsection
