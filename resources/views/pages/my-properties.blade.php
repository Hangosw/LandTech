@extends('layouts.app')

@section('title', 'Quản lý tin đăng')

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
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Quản lý tin đăng</h1>
                <p class="text-sm text-gray-500 mt-1.5 font-medium">Danh sách các bất động sản bạn đã đăng tải.</p>
            </div>
            <div>
                <a href="{{ route('property.post') }}" class="text-sm font-semibold text-white hover:bg-teal-700 transition-colors bg-teal-600 px-4 py-2 rounded-xl flex items-center gap-2 shadow-sm shadow-teal-600/30">
                    <i class="fas fa-plus"></i> Đăng tin mới
                </a>
            </div>
        </div>

        @if(!session()->has('user'))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-16 flex flex-col items-center justify-center">
                <div class="w-20 h-20 rounded-full bg-amber-50 flex items-center justify-center mb-4">
                    <i class="fas fa-lock text-3xl text-amber-500"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Yêu cầu Đăng nhập</h2>
                <p class="text-gray-500 mb-6 text-center max-w-md">Bạn cần đăng nhập tài khoản để có thể xem và quản lý danh sách tin đăng.</p>
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
                            <th class="px-6 py-4 border-b border-teal-100 w-[30%]">Bài đăng</th>
                            <th class="px-6 py-4 border-b border-teal-100 w-[20%]">Trạng thái</th>
                            <th class="px-6 py-4 border-b border-teal-100 w-[20%]">Chi tiết</th>
                            <th class="px-6 py-4 border-b border-teal-100 w-[15%]">Giá</th>
                            <th class="px-6 py-4 text-right border-b border-teal-100 w-[15%]">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
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
            autoWidth: false,
            serverSide: false,
            ajax: {
                url: '{{ route("my-properties") }}',
                type: 'GET'
            },
            columns: [
                {
                    data: null,
                    render: function(data, type, row) {
                        var imgHtml = row.image 
                            ? `<img src="${row.image}" alt="property image" class="w-full h-full object-cover">`
                            : `<i class="fas fa-image text-xl"></i>`;
                        
                        return `
                        <div class="flex flex-col items-start gap-2 max-w-[16rem]">
                            <div class="w-32 h-20 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center text-gray-400 overflow-hidden border border-gray-200/50 shadow-sm">
                                ${imgHtml}
                            </div>
                            <div class="text-left w-full">
                                <a href="${row.route_detail}" class="text-sm font-bold text-gray-900 hover:text-teal-600 line-clamp-2 break-words" title="${row.title}">${row.title}</a>
                                <div class="text-xs text-gray-500 mt-1 font-medium truncate"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>${row.address}</div>
                            </div>
                        </div>`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        const statusBadges = {
                            'sansangchothue': `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sẵn sàng cho thuê</span>`,
                            'choduyet': `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Chờ duyệt</span>`,
                            'nhap': `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200"><span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Nháp</span>`,
                            'dachothue': `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-200"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Đã cho thuê</span>`,
                            'taman': `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-50 text-gray-700 text-xs font-semibold border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Tạm ẩn</span>`,
                            'hethantin': `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-semibold border border-rose-200"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Hết hạn tin</span>`,
                            'ngungkhaithac': `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-semibold border border-purple-200"><span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Ngừng khai thác</span>`,
                            'bigovipham': `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold border border-red-200"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Bị gỡ (vi phạm)</span>`
                        };
                        var statusHtml = statusBadges[row.status] || `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-50 text-gray-700 text-xs font-semibold border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> ${row.status}</span>`;
                        
                        return `
                        <div class="text-sm">
                            ${statusHtml}
                            <div class="text-xs text-gray-500 mt-2"><i class="far fa-clock mr-1"></i> ${row.created_at}</div>
                        </div>`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                        <div class="flex items-center gap-3">
                            <span title="Phòng ngủ"><i class="fas fa-bed text-gray-400 w-4"></i> ${row.bedrooms}</span>
                            <span title="Phòng tắm"><i class="fas fa-bath text-gray-400 w-4"></i> ${row.bathrooms}</span>
                            <span title="Diện tích"><i class="fas fa-vector-square text-gray-400 w-4"></i> ${row.area} m²</span>
                        </div>`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        var monthHtml = row.transaction_type == 'rent' ? `<div class="text-xs text-gray-500 font-medium">/ tháng</div>` : '';
                        return `
                        <div class="text-sm font-bold text-teal-600">
                            ${row.price} VNĐ
                        </div>
                        ${monthHtml}`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                        <div class="text-right whitespace-nowrap" x-data="{ open: false }">
                            <div class="relative inline-block text-left">
                                <button @click="open = !open" @click.outside="open = false" type="button" class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-teal-50 transition-colors text-gray-500 hover:text-teal-600 focus:outline-none ml-auto border border-transparent hover:border-teal-100">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100" 
                                     x-transition:enter-start="transform opacity-0 scale-95" 
                                     x-transition:enter-end="transform opacity-100 scale-100" 
                                     x-transition:leave="transition ease-in duration-75" 
                                     x-transition:leave-start="transform opacity-100 scale-100" 
                                     x-transition:leave-end="transform opacity-0 scale-95" 
                                     style="display: none;"
                                     class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden">
                                    <div class="py-1">
                                        <a href="${row.route_detail}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700 transition-colors text-left">
                                            <i class="fas fa-eye w-5 text-center mr-1 text-gray-400"></i> Xem bài đăng
                                        </a>
                                        <a href="/quan-ly-tin-dang/${row.id}/edit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700 transition-colors text-left">
                                            <i class="fas fa-edit w-5 text-center mr-1 text-gray-400"></i> Chỉnh sửa
                                        </a>
                                        ${row.status === 'active' ? `
                                        <button type="button" onclick="alert('Tính năng tạm ngưng đang được phát triển')" class="w-full text-left block px-4 py-2 text-sm text-amber-600 hover:bg-amber-50 transition-colors">
                                            <i class="fas fa-pause-circle w-5 text-center mr-1"></i> Tạm ngưng đăng
                                        </button>
                                        ` : ''}
                                        <button type="button" onclick="deleteProperty(${row.id}, '${row.status}')" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-trash-alt w-5 text-center mr-1"></i> Xóa bài đăng
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    }
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/vi.json'
            },
            columnDefs: [
                { orderable: false, targets: [4] },
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 4 },
                { className: "px-6 py-4", targets: "_all" }
            ],
            dom: '<"dt-layout-row"lf>rt<"dt-layout-row"ip>',
        });
    });

    function deleteProperty(id, status) {
        var message = "Bạn có chắc muốn xóa bài này?";
        if (status === 'active') {
            message = "Cảnh báo: Nếu môi giới xóa bài đăng sẽ không thể khôi phục. Bạn có chắc chắn muốn xóa?";
        }

        Swal.fire({
            title: 'Xác nhận xóa',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Đồng ý xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/quan-ly-tin-dang/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire(
                                'Đã xóa!',
                                response.message,
                                'success'
                            );
                            $('#propertiesTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Lỗi!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        var errMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Có lỗi xảy ra khi xóa bài đăng.';
                        Swal.fire('Lỗi!', errMsg, 'error');
                    }
                });
            }
        });
    }
</script>
@endsection
