@extends('layouts.app')

@section('title', 'Danh sách Yêu thích')

@section('content')
<!-- DataTables CSS for Tailwind -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.tailwindcss.css">

<style>
    /* Custom tweaks for DataTables Tailwind */
    table.dataTable.nowrap th, table.dataTable.nowrap td {
        white-space: normal;
    }
    
    /* Make top and bottom rows look clean */
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

    /* Inputs */
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
        border-color: oklch(58% 0.13 218);
        box-shadow: 0 0 0 2px oklch(58% 0.13 218 / 20%);
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

    /* Pagination */
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
        background-color: oklch(58% 0.13 218) !important;
        color: white !important;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
    }
    .dt-paging-button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Table headers */
    table.dataTable thead th {
        border-bottom: 1px solid #f3f4f6 !important;
    }
    
    /* Fix row lines */
    table.dataTable tbody tr { border-bottom: 1px solid #f9fafb !important; }
</style>

<div class="min-h-[100vh] bg-slate-50/50 py-10">
    <div class="container mx-auto px-4 md:px-6 lg:px-8 max-w-7xl">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Tin đã lưu</h1>
                <p class="text-sm text-gray-500 mt-1.5 font-medium">Danh sách các bất động sản bạn đã yêu thích.</p>
            </div>
            <div>
                <a href="{{ route('home') }}" class="text-sm font-semibold text-teal-600 hover:opacity-80 transition-opacity bg-teal-50 px-4 py-2 rounded-xl flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        @if(!session()->has('user'))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-16 flex flex-col items-center justify-center">
                <div class="w-20 h-20 rounded-full bg-amber-50 flex items-center justify-center mb-4">
                    <i class="fas fa-lock text-3xl text-amber-500"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Yêu cầu Đăng nhập</h2>
                <p class="text-gray-500 mb-6 text-center max-w-md">Bạn cần đăng nhập tài khoản để có thể xem và quản lý danh sách các tin đăng bất động sản đã yêu thích.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-teal-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-teal-700 transition-colors shadow-sm">
                    <i class="fas fa-sign-in-alt"></i> Đi đến trang Đăng nhập
                </a>
            </div>
        @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-4">
            <div class="overflow-x-auto w-full rounded-t-xl">
                <table id="propertiesTable" class="w-full text-left border-collapse hover" style="width:100%">
                    <thead class="bg-teal-50 text-teal-700">
                        <tr class="text-xs font-bold uppercase tracking-wider">
                            <th class="px-6 py-4 border-b border-teal-100 min-w-[250px]">Bài đăng</th>
                            <th class="px-6 py-4 border-b border-teal-100">Thông tin</th>
                            <th class="px-6 py-4 border-b border-teal-100">Chi tiết</th>
                            <th class="px-6 py-4 border-b border-teal-100">Giá</th>
                            <th class="px-6 py-4 text-right border-b border-teal-100">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(isset($properties) && count($properties) > 0)
                            @foreach($properties as $property)
                            <tr class="hover:bg-gray-50/50 transition-colors" id="property-row-{{ $property->id }}">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-2">
                                        <div class="w-32 h-20 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center text-gray-400 overflow-hidden border border-gray-200/50 shadow-sm">
                                            @if($property->media && $property->media->count() > 0)
                                                <img src="{{ $property->media->first()->file_url }}" alt="property image" class="w-full h-full object-cover">
                                            @elseif($property->cover_image_url)
                                                <img src="{{ $property->cover_image_url }}" alt="property image" class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-image text-xl"></i>
                                            @endif
                                        </div>
                                        <div class="text-left w-full">
                                            <a href="{{ route('rent.detail', $property->slug) }}" class="text-sm font-bold text-gray-900 hover:text-teal-600 line-clamp-2" title="{{ $property->title }}">{{ $property->title }}</a>
                                            <div class="text-xs text-gray-500 mt-1 font-medium"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>{{ Str::limit($property->address, 30) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-semibold text-gray-800">{{ $property->type_label ?? 'Chưa cập nhật' }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5"><span class="font-medium">KV:</span> <span class="district-data">{{ $property->district ?? 'N/A' }}</span></div>
                                        <div class="text-xs text-gray-500 mt-0.5"><span class="font-medium">Dự án:</span> <span class="project-data">{{ $property->project ?? 'Tự do' }}</span></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="flex items-center gap-3">
                                        <span title="Phòng ngủ"><i class="fas fa-bed text-gray-400 w-4"></i> {{ $property->bedrooms ?? 0 }}</span>
                                        <span title="Phòng tắm"><i class="fas fa-bath text-gray-400 w-4"></i> {{ $property->bathrooms ?? 0 }}</span>
                                        <span title="Diện tích"><i class="fas fa-vector-square text-gray-400 w-4"></i> {{ $property->area ?? 0 }} m²</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-teal-600">
                                        {{ number_format($property->price, 0, ',', '.') }} VNĐ
                                    </div>
                                    @if($property->transaction_type == 'rent')
                                        <div class="text-xs text-gray-500 font-medium">/ tháng</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button onclick="removeFavorite({{ $property->id }}, this)" class="text-sm font-semibold text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors border border-red-100">
                                        <i class="fas fa-trash-alt mr-1"></i> Bỏ thích
                                    </button>
                                    <a href="{{ route('rent.detail', $property->slug) }}" class="ml-2 text-sm font-semibold text-teal-600 hover:text-teal-800 hover:bg-teal-50 px-3 py-1.5 rounded-lg transition-colors border border-teal-100">
                                        Chi tiết
                                    </a>
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
        var table = $('#propertiesTable').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/vi.json'
            },
            columnDefs: [
                { orderable: false, targets: [4] },
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 3 },
                { responsivePriority: 3, targets: 4 }
            ],
            dom: '<"dt-layout-row"lf>rt<"dt-layout-row"ip>',
        });
    });

    function removeFavorite(propertyId, button) {
        fetch('{{ route("wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ property_id: propertyId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var row = $(button).closest('tr');
                var table = $('#propertiesTable').DataTable();
                table.row(row).remove().draw(false);
            } else if (data.redirect) {
                window.location.href = data.redirect;
            }
        });
    }
</script>
@endsection
