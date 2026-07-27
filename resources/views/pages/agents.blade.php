@extends('layouts.app')

@section('title', 'Môi giới uy tín — Verified Agent | LANDTEK')
@section('description', 'Bảng xếp hạng môi giới và hệ thống Verified Agent Silver / Gold minh bạch tại LANDTEK Nha Trang.')

@section('content')
<div class="container mx-auto px-4 md:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold">Môi giới uy tín</h1>
    <p class="mt-2 max-w-2xl text-gray-500">
        Hệ thống Verified Agent giúp khách thuê yên tâm làm việc với những môi giới chuyên nghiệp, minh bạch.
    </p>

    <div class="mt-8 grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-star text-gray-400 text-xl"></i>
                <h2 class="text-lg font-bold">Silver Agent</h2>
            </div>
            <ul class="mt-3 space-y-2 text-sm text-gray-500">
                <li>• Đăng tin cơ bản, tự động đóng watermark LANDTEK</li>
                <li>• Quản lý lead và lịch xem nhà</li>
                <li>• Hiển thị tiêu chuẩn trong danh mục</li>
            </ul>
        </div>
        <div class="rounded-2xl border-2 border-yellow-500 bg-white p-6 shadow-md">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-yellow-500 text-xl"></i>
                <h2 class="text-lg font-bold">Gold Verified Agent</h2>
            </div>
            <ul class="mt-3 space-y-2 text-sm text-gray-500">
                <li>• Tick xanh xác thực, ưu tiên hiển thị đầu danh mục</li>
                <li>• Đăng tin không giới hạn</li>
                <li>• Truy cập Analytics thị trường &amp; báo cáo dự án</li>
                <li>• Giới hạn tối đa 300 Verified Agent</li>
            </ul>
        </div>
    </div>

    <div class="mt-10 flex items-center gap-2">
        <i class="fa-solid fa-trophy text-yellow-500 text-2xl"></i>
        <h2 class="text-2xl font-bold">Top môi giới tuần này</h2>
    </div>
    
    <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200">
        @foreach($topAgents as $i => $a)
            <div class="flex items-center gap-4 border-b border-gray-200 bg-white px-4 py-3 last:border-0">
                <span class="grid h-8 w-8 place-items-center rounded-full text-sm font-bold {{ $i < 3 ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-600' }}">
                    {{ $i + 1 }}
                </span>
                <img src="{{ $a->avatar }}" alt="{{ $a->name }}" class="h-11 w-11 rounded-full object-cover" />
                <div class="min-w-0 flex-1">
                    <p class="flex items-center gap-1 font-semibold text-gray-900">
                        {{ $a->name }}
                        @if($a->tier === 'gold')
                            <i class="fa-solid fa-circle-check text-yellow-500 text-sm"></i>
                        @endif
                    </p>
                    <p class="text-xs text-gray-500">{{ $a->listings }} tin đang đăng</p>
                </div>
                <div class="hidden gap-6 text-sm sm:flex">
                    <span class="inline-flex items-center gap-1.5 text-gray-500">
                        <i class="fa-regular fa-eye"></i> {{ $a->views }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-gray-500">
                        <i class="fa-regular fa-calendar-check"></i> {{ $a->bookings }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
