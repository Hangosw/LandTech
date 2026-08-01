@extends('layouts.app')

@section('title', 'Lịch hẹn của tôi')

@section('content')
<!-- DataTables CSS for Tailwind -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.tailwindcss.css">

<style>
    /* Custom tweaks for DataTables Tailwind */
    table.dataTable.nowrap th, table.dataTable.nowrap td {
        white-space: normal;
    }
    
    div.dt-container .dt-layout-row {
        margin-bottom: 1rem;
        padding: 0 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    div.dt-container .dt-layout-row:last-child {
        margin-bottom: 0;
        padding-top: 1rem;
        padding-bottom: 1rem;
        border-top: 1px solid #f3f4f6;
    }

    .dt-search label { font-size: 0.875rem; font-weight: 500; color: #6b7280; }
    .dt-search input {
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        outline: none;
        margin-left: 0.5rem;
    }
    .dt-search input:focus {
        border-color: #0F3460;
        box-shadow: 0 0 0 2px rgba(15,52,96,0.20);
    }

    .dt-length label { font-size: 0.875rem; font-weight: 500; color: #6b7280; margin-left: 0.5rem; }
    .dt-length select {
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        padding: 0.5rem 2rem 0.5rem 1rem;
        font-size: 0.875rem;
        margin-right: 0.5rem;
        outline: none;
    }

    .dt-paging {
        display: flex;
        gap: 0.25rem;
    }
    .dt-paging-button {
        padding: 0.5rem 1rem !important;
        border-radius: 0.5rem !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #4b5563 !important;
        background: transparent !important;
        border: 1px solid transparent !important;
        transition: all 0.2s ease;
        margin-left: 0.125rem !important;
    }
    .dt-paging-button:hover:not(.disabled) {
        background: #f3f4f6 !important;
        color: #111827 !important;
    }
    .dt-paging-button.current {
        background-color: #0F3460 !important;
        color: white !important;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
    }
    .dt-paging-button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    table.dataTable thead th {
        border-bottom: 1px solid #f3f4f6 !important;
    }
    table.dataTable tbody tr { border-bottom: 1px solid #f9fafb !important; }
</style>

<div class="min-h-[100vh] bg-slate-50/50 py-10">
    <div class="container mx-auto px-4 md:px-6 lg:px-8 max-w-7xl">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Lịch hẹn xem nhà</h1>
                <p class="text-sm text-gray-500 mt-1.5 font-medium">Danh sách các cuộc hẹn từ khách hàng quan tâm đến bất động sản của bạn.</p>
            </div>
        </div>

        @if(!session()->has('user'))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-16 flex flex-col items-center justify-center">
                <div class="w-20 h-20 rounded-full bg-amber-50 flex items-center justify-center mb-4">
                    <i class="fas fa-lock text-3xl text-amber-500"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Yêu cầu Đăng nhập</h2>
                <p class="text-gray-500 mb-6 text-center max-w-md">Bạn cần đăng nhập tài khoản để có thể xem danh sách lịch hẹn.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-teal-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-teal-700 transition-colors shadow-sm">
                    <i class="fas fa-sign-in-alt"></i> Đi đến trang Đăng nhập
                </a>
            </div>
        @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-4">
            <div class="overflow-x-auto w-full rounded-t-xl">
                <table id="bookingsTable" class="w-full text-left border-collapse hover" style="width:100%">
                    <thead class="bg-teal-50 text-teal-700">
                        <tr class="text-xs font-bold uppercase tracking-wider">
                            <th class="px-6 py-4 border-b border-teal-100">Bất động sản</th>
                            <th class="px-6 py-4 border-b border-teal-100">Khách hàng</th>
                            <th class="px-6 py-4 border-b border-teal-100">Thời gian hẹn</th>
                            <th class="px-6 py-4 border-b border-teal-100">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(isset($bookings) && count($bookings) > 0)
                            @foreach($bookings as $booking)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-2">
                                        <div class="text-left w-full">
                                            @if($booking->property)
                                                <a href="{{ route('rent.detail', $booking->property->slug ?? $booking->property->id) }}" class="text-sm font-bold text-gray-900 hover:text-teal-600 line-clamp-2" title="{{ $booking->property->title }}">{{ $booking->property->title }}</a>
                                                <div class="text-xs text-gray-500 mt-1 font-medium"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>{{ Str::limit($booking->property->address, 30) }}</div>
                                            @else
                                                <span class="text-sm text-gray-500 italic">Bất động sản đã bị xóa</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="font-bold text-gray-900">{{ $booking->renter_name }}</div>
                                    <div class="mt-1"><i class="fas fa-phone-alt text-gray-400 w-4"></i> {{ $booking->renter_phone }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-teal-600">
                                        {{ \Carbon\Carbon::parse($booking->scheduled_date)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-medium mt-1">
                                        <i class="far fa-clock"></i> {{ $booking->scheduled_time ?? 'Cả ngày' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        @if($booking->status == 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Chờ xác nhận
                                            </span>
                                        @elseif($booking->status == 'confirmed')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Đã xác nhận
                                            </span>
                                        @elseif($booking->status == 'completed')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Hoàn thành
                                            </span>
                                        @elseif($booking->status == 'cancelled')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Đã hủy
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-50 text-gray-700 text-xs font-semibold border border-gray-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> {{ ucfirst($booking->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
    </div>
</div>

<!-- jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.tailwindcss.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#bookingsTable').DataTable({
            responsive: true,
            order: [[2, 'desc']], // Sort by scheduled date
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/vi.json'
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 2 },
            ],
            dom: '<"dt-layout-row"lf>rt<"dt-layout-row"ip>',
        });
    });
</script>
@endsection
