@extends('layouts.admin')

@section('title', 'Quản lý Bài đăng Bất động sản')

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
    .stat-value.skeleton {
        background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
        border-radius: 0.375rem;
        color: transparent;
        width: 2rem;
        height: 1.375rem;
    }
    @keyframes shimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
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

    #propertiesTable th, #propertiesTable td {
        padding: 0.75rem 0.875rem !important;
        font-size: 0.875rem !important;
        vertical-align: middle;
        word-wrap: break-word;
    }

    /* Column widths */
    #propertiesTable { table-layout: auto; width: 100% !important; }
    #propertiesTable th:nth-child(1) { width: 42px; text-align: center; }
    #propertiesTable th:nth-child(7) { text-align: right; }
    @media (min-width: 1024px) {
        #propertiesTable { table-layout: fixed; }
        #propertiesTable th:nth-child(1) { width: 4%; }
        #propertiesTable th:nth-child(2) { width: 28%; }
        #propertiesTable th:nth-child(3) { width: 16%; }
        #propertiesTable th:nth-child(4) { width: 15%; }
        #propertiesTable th:nth-child(5) { width: 17%; }
        #propertiesTable th:nth-child(6) { width: 12%; }
        #propertiesTable th:nth-child(7) { width: 8%; }
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

    /* Status badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
        border-width: 1px;
        border-style: solid;
    }
    .status-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
        display: inline-block;
    }
    .status-active  { background:#ecfdf5; color:#059669; border-color:rgba(16,185,129,0.25); }
    .status-active .status-dot  { background:#10b981; }
    .status-pending { background:#fffbeb; color:#d97706; border-color:rgba(245,158,11,0.25); }
    .status-pending .status-dot { background:#f59e0b; }
    .status-sold    { background:#eff6ff; color:#2563eb; border-color:rgba(59,130,246,0.25); }
    .status-sold .status-dot    { background:#3b82f6; }
    .status-hidden  { background:#f8fafc; color:#64748b; border-color:rgba(100,116,139,0.25); }
    .status-hidden .status-dot  { background:#94a3b8; }
    .status-deleted { background:#fef2f2; color:#dc2626; border-color:rgba(220,38,38,0.25); }
    .status-deleted .status-dot { background:#ef4444; }

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
       POST CELL — Compact inline
    =========================== */
    .post-cell {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .post-thumb {
        width: 52px;
        height: 52px;
        border-radius: 0.625rem;
        overflow: hidden;
        flex-shrink: 0;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
    }
    .post-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .post-cell:hover .post-thumb img { transform: scale(1.06); }
    .post-meta { min-width: 0; flex: 1; }
    .post-title {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.15s;
    }
    .post-cell:hover .post-title { color: oklch(58% 0.13 218); }
    .post-addr {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.25rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        overflow: hidden;
    }
    .post-addr span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .post-agent {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        margin-top: 0.375rem;
        padding-top: 0.375rem;
        border-top: 1px solid #f1f5f9;
    }
    .post-agent-name {
        font-size: 0.7rem;
        font-weight: 600;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100px;
    }

    /* ===========================
       MEDIA MODAL
    =========================== */
    .media-modal {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        z-index: 99999; display: none;
    }
    .media-modal.is-open { display: block; }
    .media-modal-backdrop {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15,23,42,0.75);
        backdrop-filter: blur(4px);
    }
    .media-modal-container {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        padding: 1rem; box-sizing: border-box;
    }
    .media-modal-content {
        background: white; border-radius: 1.25rem;
        width: 100%; max-width: 960px; height: 85vh;
        display: flex; flex-direction: column;
        box-shadow: 0 25px 60px -12px rgba(0,0,0,0.35);
        transform: scale(0.95); opacity: 0;
        transition: transform 0.25s ease, opacity 0.25s ease;
    }
    .media-modal-content.is-visible { transform: scale(1); opacity: 1; }
    .media-modal-body {
        padding: 1.25rem; overflow-y: auto; flex: 1;
        background: rgba(248,250,252,0.5);
    }
    .media-modal-body::-webkit-scrollbar { width: 5px; }
    .media-modal-body::-webkit-scrollbar-track { background: transparent; }
    .media-modal-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; }
    .media-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    @media (min-width: 640px)  { .media-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (min-width: 1024px) { .media-grid { grid-template-columns: repeat(4, 1fr); } }
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
                    <span>Bất động sản</span>
                </div>
                <h1 class="page-title">Danh sách Bài đăng</h1>
                <p class="page-subtitle">Quản lý tất cả bài đăng bất động sản trên hệ thống</p>
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
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value skeleton" id="statTotal">0</div>
                    <div class="stat-label">Tổng bài đăng</div>
                </div>
            </div>
            <div class="stat-card active">
                <div class="stat-icon green">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value skeleton" id="statActive">0</div>
                    <div class="stat-label">Đang hiển thị</div>
                </div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon amber">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value skeleton" id="statPending">0</div>
                    <div class="stat-label">Chờ duyệt</div>
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
                <select id="filterProject" class="filter-select">
                    <option value="">Tất cả dự án</option>
                    <option value="Vinhomes Central Park">Vinhomes Central Park</option>
                    <option value="Masteri Thảo Điền">Masteri Thảo Điền</option>
                    <option value="Sala Đại Quang Minh">Sala Đại Quang Minh</option>
                    <option value="Đất nền tự do">Đất nền tự do</option>
                </select>
                <select id="filterDistrict" class="filter-select">
                    <option value="">Tất cả khu vực</option>
                    <option value="Quận 1">Quận 1</option>
                    <option value="Quận 2">Quận 2</option>
                    <option value="Quận 3">Quận 3</option>
                    <option value="Quận 7">Quận 7</option>
                    <option value="Bình Thạnh">Bình Thạnh</option>
                </select>
                <select id="filterStatus" class="filter-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Đang hiển thị">Đang hiển thị</option>
                    <option value="Chờ duyệt">Chờ duyệt</option>
                    <option value="Đã bán/Cho thuê">Đã bán/Cho thuê</option>
                    <option value="Đã ẩn/Khóa">Đã ẩn/Khóa</option>
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
                <table id="propertiesTable" class="w-full text-left hover" style="width:100%">
                    <thead>
                        <tr>
                            <th class="all">#</th>
                            <th class="all">Bài đăng</th>
                            <th class="min-tablet">Thông tin</th>
                            <th class="min-tablet">Chi tiết</th>
                            <th class="min-tablet">Giá</th>
                            <th class="all">Trạng thái</th>
                            <th class="min-tablet" style="text-align:right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div><!-- /.prop-container -->
</div><!-- /.prop-page -->

<!-- Floating Dropdown -->
<div id="floatingDropdown"></div>

<!-- Media Modal -->
<div id="mediaModal" class="media-modal">
    <div class="media-modal-backdrop" onclick="closeMediaModal()"></div>
    <div class="media-modal-container">
        <div class="media-modal-content" id="mediaModalContent">
            <!-- Header -->
            <div style="padding:1rem 1.25rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
                <div style="display:flex;align-items:center;gap:0.75rem;min-width:0;flex:1;">
                    <div style="width:2.25rem;height:2.25rem;border-radius:0.625rem;background:#eff6ff;display:flex;align-items:center;justify-content:center;color:#3b82f6;flex-shrink:0;">
                        <i class="fas fa-images" style="font-size:0.875rem;"></i>
                    </div>
                    <div style="min-width:0;">
                        <h3 style="font-size:0.9375rem;font-weight:800;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" id="mediaModalTitle">Thư viện ảnh</h3>
                        <p style="font-size:0.7rem;color:#94a3b8;font-weight:600;margin-top:1px;" id="mediaModalCount">0 tệp tin</p>
                    </div>
                </div>
                <button onclick="closeMediaModal()" style="width:2rem;height:2rem;border-radius:50%;background:#f8fafc;border:1px solid #e2e8f0;color:#94a3b8;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:all 0.2s;" onmouseover="this.style.background='#f1f5f9';this.style.color='#374151'" onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8'">
                    <i class="fas fa-times" style="font-size:0.875rem;"></i>
                </button>
            </div>
            <!-- Body -->
            <div class="media-modal-body">
                <div id="mediaGrid" class="media-grid"></div>
                <div id="mediaEmpty" class="hidden" style="display:none;flex-direction:column;align-items:center;justify-content:center;padding:4rem 1rem;text-align:center;">
                    <div style="width:4rem;height:4rem;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#cbd5e1;margin-bottom:1rem;">
                        <i class="fas fa-image" style="font-size:1.5rem;"></i>
                    </div>
                    <h4 style="font-size:0.875rem;font-weight:700;color:#475569;margin:0 0 0.25rem;">Không có hình ảnh</h4>
                    <p style="font-size:0.75rem;color:#94a3b8;">Bài đăng này chưa có hình ảnh hoặc video nào.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.tailwindcss.js"></script>

<script>
    const csrfToken = '{{ csrf_token() }}';

    $(document).ready(function() {
        document.body.appendChild(document.getElementById('mediaModal'));

        var table = $('#propertiesTable').DataTable({
            responsive: {
                details: {
                    type: 'inline',
                    target: 'tr'
                }
            },
            autoWidth: false,
            serverSide: false,
            ajax: {
                url: '{{ route("admin.properties.index") }}',
                type: 'GET',
                dataSrc: function(json) {
                    // Compute stats from the returned data
                    var data = json.data || [];
                    var total   = data.length;
                    var active  = data.filter(function(r){ return (r.status === 'active' || r.status === 'published') && r.active !== 0 && r.active !== false; }).length;
                    var pending = data.filter(function(r){ return r.status === 'pending' && r.active !== 0 && r.active !== false; }).length;

                    animateCount('statTotal',   total);
                    animateCount('statActive',  active);
                    animateCount('statPending', pending);

                    ['statTotal','statActive','statPending'].forEach(function(id){
                        var el = document.getElementById(id);
                        if (el) el.classList.remove('skeleton');
                    });

                    return data;
                }
            },
            columns: [
                /* 0 - STT */
                {
                    data: 'stt_index',
                    defaultContent: '',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        var stt = data ? data : (meta.row + 1);
                        if (type === 'display') {
                            return `<span style="font-size:11px;font-weight:700;color:#cbd5e1;">${stt}</span>`;
                        }
                        return stt;
                    }
                },
                /* 1 - Bài đăng */
                {
                    data: null,
                    render: function(data, type, row) {
                        var imgHtml = row.image
                            ? `<img src="${row.image}" alt="thumb">`
                            : `<i class="fas fa-image" style="font-size:1rem;"></i>`;

                        var agentBadge = '';
                        if (row.user_type == 'agent') {
                            if (row.agent_tier == 'gold') {
                                agentBadge = `<span style="display:inline-flex;align-items:center;gap:2px;padding:1px 5px;border-radius:4px;background:linear-gradient(to right,#fbbf24,#f59e0b);color:white;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:0.04em;flex-shrink:0;"><i class="fas fa-crown" style="font-size:7px;"></i>Gold</span>`;
                            } else if (row.agent_tier == 'silver') {
                                agentBadge = `<span style="display:inline-flex;align-items:center;gap:2px;padding:1px 5px;border-radius:4px;background:linear-gradient(to right,#cbd5e1,#94a3b8);color:#1e293b;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:0.04em;flex-shrink:0;"><i class="fas fa-medal" style="font-size:7px;"></i>Silver</span>`;
                            } else {
                                agentBadge = `<span style="display:inline-flex;align-items:center;gap:2px;padding:1px 5px;border-radius:4px;background:#f1f5f9;color:#64748b;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;flex-shrink:0;">Pro</span>`;
                            }
                        }

                        var shortAddr = row.address && row.address.length > 28
                            ? row.address.substring(0, 28) + '…'
                            : (row.address || 'Chưa cập nhật');

                        return `
                        <a href="${row.route_detail}" class="post-cell" style="text-decoration:none;">
                            <div class="post-thumb">${imgHtml}</div>
                            <div class="post-meta">
                                <div class="post-title" title="${row.title}">${row.title}</div>
                                <div class="post-addr">
                                    <i class="fas fa-map-marker-alt" style="font-size:0.6rem;color:#cbd5e1;flex-shrink:0;margin-top:1px;"></i>
                                    <span title="${row.address}">${shortAddr}</span>
                                </div>
                                <div class="post-agent">
                                    <i class="fas fa-user-circle" style="font-size:0.75rem;color:#cbd5e1;flex-shrink:0;"></i>
                                    <span class="post-agent-name" title="${row.user_name}">${row.user_name}</span>
                                    ${agentBadge}
                                </div>
                            </div>
                        </a>`;
                    }
                },
                /* 2 - Thông tin */
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                        <div style="display:flex;flex-direction:column;gap:4px;min-width:0;">
                            <span style="display:inline-block;padding:2px 8px;border-radius:4px;background:#f1f5f9;color:#64748b;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;width:fit-content;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${row.property_type || 'N/A'}</span>
                            <div style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:3px;min-width:0;"><span style="color:#94a3b8;font-weight:600;flex-shrink:0;">KV:</span><span style="font-weight:600;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${row.district || 'N/A'}">${row.district || 'N/A'}</span></div>
                            <div style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:3px;min-width:0;"><span style="color:#94a3b8;font-weight:600;flex-shrink:0;">Dự án:</span><span style="font-weight:600;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${row.project || 'Tự do'}">${row.project || 'Tự do'}</span></div>
                        </div>`;
                    }
                },
                /* 3 - Chi tiết */
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                        <div style="display:flex;flex-direction:column;gap:5px;">
                            <div style="display:flex;align-items:center;gap:6px;font-size:12px;" title="Phòng ngủ"><i class="fas fa-bed" style="color:#cbd5e1;width:12px;text-align:center;"></i><span style="font-weight:600;color:#374151;">${row.bedrooms}</span></div>
                            <div style="display:flex;align-items:center;gap:6px;font-size:12px;" title="Phòng tắm"><i class="fas fa-bath" style="color:#cbd5e1;width:12px;text-align:center;"></i><span style="font-weight:600;color:#374151;">${row.bathrooms}</span></div>
                            <div style="display:flex;align-items:center;gap:6px;font-size:12px;" title="Diện tích"><i class="fas fa-vector-square" style="color:#cbd5e1;width:12px;text-align:center;"></i><span style="font-weight:600;color:#374151;">${row.area} m²</span></div>
                        </div>`;
                    }
                },
                /* 4 - Giá */
                {
                    data: null,
                    render: function(data, type, row) {
                        var formattedPrice = new Intl.NumberFormat('vi-VN').format(row.price);
                        var monthHtml = row.transaction_type == 'rent'
                            ? `<div style="font-size:10px;color:#94a3b8;font-weight:600;margin-top:2px;">/ tháng</div>`
                            : '';
                        return `
                        <div style="font-size:13px;font-weight:800;color:oklch(58% 0.13 218);">
                            ${formattedPrice} <span style="font-size:10px;font-weight:600;">VNĐ</span>
                        </div>${monthHtml}`;
                    }
                },
                /* 5 - Trạng thái */
                {
                    data: 'status',
                    render: function(data, type, row) {
                        if (row.active === false || row.active === 0) {
                            return `<span class="status-badge status-deleted"><span class="status-dot"></span>Đã xóa</span>`;
                        } else if (data == 'active' || data == 'published') {
                            return `<span class="status-badge status-active"><span class="status-dot"></span>Đang hiển thị</span>`;
                        } else if (data == 'pending') {
                            return `<span class="status-badge status-pending"><span class="status-dot"></span>Chờ duyệt</span>`;
                        } else if (data == 'sold' || data == 'rented') {
                            return `<span class="status-badge status-sold"><span class="status-dot"></span>Đã giao dịch</span>`;
                        } else {
                            return `<span class="status-badge status-hidden"><span class="status-dot"></span>Đã ẩn</span>`;
                        }
                    }
                },
                /* 6 - Thao tác */
                {
                    data: null,
                    render: function(data, type, row) {
                        var actionButtons = '';
                        if (row.status == 'pending' || row.status == 'draft') {
                            actionButtons += `
                            <form action="${row.route_status}" method="POST" style="margin:0">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="dm-item green"><i class="fas fa-check-circle"></i>Duyệt bài</button>
                            </form>
                            <form action="${row.route_status}" method="POST" style="margin:0">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="status" value="hidden">
                                <button type="submit" class="dm-item amber"><i class="fas fa-times-circle"></i>Từ chối</button>
                            </form>`;
                        } else if (row.status == 'active' || row.status == 'published') {
                            actionButtons += `
                            <form action="${row.route_status}" method="POST" style="margin:0">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="status" value="hidden">
                                <button type="submit" class="dm-item amber"><i class="fas fa-eye-slash"></i>Ẩn bài</button>
                            </form>`;
                        }

                        return `
                        <div style="display:flex;align-items:center;justify-content:flex-end;">
                            <button type="button" class="dropdown-btn" title="Thao tác">
                                <i class="fas fa-ellipsis-v" style="font-size:12px;"></i>
                            </button>
                            <div class="dropdown-menu-tpl" style="display:none">
                                <div class="dm-section">
                                    <a href="${row.route_edit}" class="dm-item"><i class="fas fa-edit"></i>Sửa bài</a>
                                </div>
                                <div class="dm-section">${actionButtons}</div>
                                <div class="dm-section">
                                    <button type="button" class="dm-item blue" onclick='openMediaModal(${JSON.stringify(row.all_media || []).replace(/'/g, "&#39;")}, "${row.title.replace(/"/g, '&quot;')}")'><i class="fas fa-images"></i>Xem media</button>
                                </div>
                                <div class="dm-section">
                                    <button type="button" class="dm-item red" onclick="deleteProperty('${row.route_delete}', this)"><i class="fas fa-trash-alt"></i>Xóa bài</button>
                                </div>
                            </div>
                        </div>`;
                    }
                }
            ],
            language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/vi.json' },
            columnDefs: [
                { searchable: false, orderable: false, targets: 0 },
                { orderable: false, targets: [1, 6] },
                { className: 'all',        targets: [0, 1, 5] },
                { className: 'min-tablet', targets: [2, 3, 4, 6] },
                { responsivePriority: 1,  targets: [1] },
                { responsivePriority: 2,  targets: [5] },
                { responsivePriority: 3,  targets: [4] },
                { responsivePriority: 4,  targets: [2] },
                { responsivePriority: 5,  targets: [3] },
                { responsivePriority: 6,  targets: [6] },
            ],
            order: [],
            dom: 'rt<"dt-layout-row"ip>',
        });

        // Re-number STT
        table.on('order.dt search.dt', function() {
            var i = 1;
            table.cells(null, 0, { search: 'applied', order: 'applied' }).every(function() {
                this.data(i++);
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

        // Filter: Project
        $.fn.dataTable.ext.search.push(function(settings, data) {
            var sel = $('#filterProject').val();
            if (!sel) return true;
            return (data[2] || '').includes(sel);
        });

        // Filter: District
        $.fn.dataTable.ext.search.push(function(settings, data) {
            var sel = $('#filterDistrict').val();
            if (!sel) return true;
            return (data[2] || '').includes(sel);
        });

        $('#filterProject, #filterDistrict').on('change', function() { table.draw(); });

        // Filter: Status
        $('#filterStatus').on('change', function() {
            table.column(5).search($(this).val()).draw();
        });

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
            // Prevent going below viewport
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

    }); // end ready

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
            var eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            el.textContent = Math.round(eased * target);
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target;
        }
        requestAnimationFrame(step);
    }

    // ========================
    // Media Modal
    // ========================
    function openMediaModal(mediaList, title) {
        var modal        = document.getElementById('mediaModal');
        var modalContent = document.getElementById('mediaModalContent');
        var titleEl      = document.getElementById('mediaModalTitle');
        var countEl      = document.getElementById('mediaModalCount');
        var gridEl       = document.getElementById('mediaGrid');
        var emptyEl      = document.getElementById('mediaEmpty');

        $('#floatingDropdown').removeClass('is-open');

        titleEl.textContent = title;
        countEl.textContent = mediaList.length + ' tệp tin';
        gridEl.innerHTML = '';

        if (mediaList && mediaList.length > 0) {
            gridEl.style.display = 'grid';
            emptyEl.style.display = 'none';

            mediaList.forEach(function(media, index) {
                var coverBadge = media.is_cover
                    ? `<div style="position:absolute;top:6px;left:6px;padding:2px 6px;background:#10b981;color:white;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:0.04em;border-radius:4px;z-index:10;"><i class="fas fa-star" style="font-size:7px;margin-right:2px;"></i>Bìa</div>`
                    : '';

                if (media.type === 'video') {
                    gridEl.innerHTML += `
                    <div style="position:relative;border-radius:0.75rem;overflow:hidden;background:black;aspect-ratio:1;border:1px solid #e2e8f0;">
                        ${coverBadge}
                        <video controls style="width:100%;height:100%;object-fit:contain;">
                            <source src="${media.url}" type="video/mp4">
                        </video>
                    </div>`;
                } else {
                    gridEl.innerHTML += `
                    <div style="position:relative;border-radius:0.75rem;overflow:hidden;background:#f1f5f9;aspect-ratio:1;border:1px solid #e2e8f0;cursor:pointer;" onclick="window.open('${media.url}','_blank')">
                        ${coverBadge}
                        <img src="${media.url}" alt="Media ${index+1}" style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s ease;">
                        <div style="position:absolute;inset:0;background:rgba(0,0,0,0);transition:background 0.2s;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.background='rgba(0,0,0,0.2)';this.querySelector('.expand-icon').style.opacity='1'" onmouseout="this.style.background='rgba(0,0,0,0)';this.querySelector('.expand-icon').style.opacity='0'">
                            <div class="expand-icon" style="width:2rem;height:2rem;border-radius:50%;background:rgba(255,255,255,0.9);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;"><i class="fas fa-expand" style="font-size:0.75rem;color:#374151;"></i></div>
                        </div>
                    </div>`;
                }
            });
        } else {
            gridEl.style.display = 'none';
            emptyEl.style.display = 'flex';
        }

        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        setTimeout(function() { modalContent.classList.add('is-visible'); }, 10);
    }

    function closeMediaModal() {
        var modal        = document.getElementById('mediaModal');
        var modalContent = document.getElementById('mediaModalContent');
        var gridEl       = document.getElementById('mediaGrid');
        modalContent.classList.remove('is-visible');
        setTimeout(function() {
            modal.classList.remove('is-open');
            gridEl.querySelectorAll('video').forEach(function(v) { v.pause(); });
            document.body.style.overflow = '';
        }, 250);
    }

    function deleteProperty(url, btn) {
        closeFloatingDropdown && closeFloatingDropdown();
        Swal.fire({
            title: 'Xóa bài đăng?',
            text: 'Bài đăng sẽ bị xóa cứng nếu không có dữ liệu liên quan, ngược lại sẽ bị ẩn khỏi hệ thống. Hành động này có thể khôi phục nếu chỉ bị ẩn.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xóa bài',
            cancelButtonText: 'Hủy',
            buttonsStyling: false,
            customClass: { confirmButton: 'swal-btn-danger', cancelButton: 'swal-btn-secondary' }
        }).then(function(result) {
            if (!result.isConfirmed) return;

            fetch(url, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    SwalSuccess.fire({ title: 'Thành công!', text: data.message, timer: 2000, showConfirmButton: false });
                    $('#propertiesTable').DataTable().ajax.reload(null, false);
                }
            })
            .catch(function() { SwalError.fire({ title: 'Lỗi!', text: 'Có lỗi xảy ra. Vui lòng thử lại.' }); });
        });
    }

    function closeFloatingDropdown() {
        $('#floatingDropdown').removeClass('is-open');
        $('.dropdown-btn.active').removeClass('active');
    }
</script>
@endsection
