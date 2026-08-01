@extends('layouts.app')

@section('title', '403 — Truy Cập Bị Từ Chối — LANDTEK')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center bg-slate-50/50 py-16 px-4">
    <div class="max-w-md w-full text-center">
        {{-- Icon --}}
        <div class="mx-auto w-20 h-20 rounded-3xl bg-red-50 border border-red-100 flex items-center justify-center text-red-500 mb-6 shadow-sm">
            <i class="fas fa-user-shield text-3xl"></i>
        </div>

        {{-- Error Code Badge --}}
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold tracking-wider uppercase mb-3">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
            Lỗi 403 — Truy Cập Bị Từ Chối
        </div>

        {{-- Heading --}}
        <h1 class="text-2xl sm:text-3xl font-extrabold text-navy tracking-tight mb-3">Bạn Không Có Quyền Truy Cập</h1>

        {{-- Description Message --}}
        <p class="text-slate-600 text-sm leading-relaxed mb-8 px-2 font-medium">
            {{ !empty($exception) && $exception->getMessage() ? $exception->getMessage() : 'Tài khoản của bạn không có quyền truy cập vào trang này.' }}
        </p>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-navy text-white font-bold text-sm hover:bg-navy-mid transition-all shadow-sm active:scale-95">
                <i class="fas fa-home text-xs"></i>
                Về Trang Chủ
            </a>
            <button onclick="window.history.back()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition-all shadow-sm active:scale-95">
                <i class="fas fa-arrow-left text-xs"></i>
                Quay Lại Trang Trước
            </button>
        </div>
    </div>
</div>
@endsection
