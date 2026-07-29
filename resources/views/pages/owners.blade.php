@extends('layouts.app')

@section('title', 'LANDTEK — Chủ nhà & Quản lý gia sản')
@section('description', 'LANDTEK hỗ trợ chủ nhà Nha Trang quản lý tài sản: tìm khách, thu tiền, xử lý sự cố và báo cáo minh bạch.')

@section('content')
<div class="bg-white">
    <section class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white py-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-12 lg:gap-20 lg:flex-row lg:items-center">
                <div class="lg:w-6/12">
                    <span class="inline-flex rounded-full bg-amber-400/15 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-200">DÀNH CHO CHỦ NHÀ</span>
                    <h1 class="mt-8 text-4xl sm:text-5xl font-black leading-tight">Quản lý gia sản Nha Trang<br><span class="text-amber-400">an toàn, minh bạch</span></h1>
                    <p class="mt-6 max-w-2xl text-sm sm:text-base text-slate-200 leading-7">LANDTEK hỗ trợ chủ nhà từ đăng tin, tìm khách thuê đến quản lý vận hành và báo cáo tài chính hàng tháng.</p>
                    <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                        <a href="{{ route('property.post') }}" class="inline-flex items-center justify-center rounded-2xl bg-amber-400 px-7 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-amber-500/20 hover:bg-amber-300">Đăng ký tư vấn miễn phí</a>
                        <a href="{{ route('rent.list') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-7 py-3 text-sm font-semibold text-white hover:bg-white/20">Xem dịch vụ Managed Rental</a>
                    </div>
                </div>
                <div class="lg:w-5/12">
                    <div class="rounded-[28px] bg-white p-8 shadow-2xl shadow-slate-950/10">
                        <h2 class="text-xl font-bold text-slate-900">Báo cáo quản lý tài sản</h2>
                        <p class="mt-3 text-sm text-slate-600">Theo dõi doanh thu, chi phí, và lịch sửa chữa trong một dashboard đơn giản.</p>
                        <div class="mt-8 space-y-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">Số điện thoại</label>
                                <input type="text" placeholder="VD: 0905 xxx xxx" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-amber-400 focus:outline-none" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">Địa chỉ tài sản</label>
                                <select class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-amber-400 focus:outline-none">
                                    <option>Chọn tài sản...</option>
                                    <option>Phước Hải</option>
                                    <option>Vĩnh Hòa</option>
                                    <option>Trung tâm</option>
                                </select>
                            </div>
                        </div>
                        <button class="mt-6 w-full rounded-2xl bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-amber-300">Nhận tư vấn trong 24h →</button>
                        <p class="mt-4 text-center text-xs text-slate-500">Hoặc liên hệ: <span class="font-semibold text-slate-900">0258 888 6868</span></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                    <div class="mb-4 text-2xl">✓</div>
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Tin thuê xác thực</h3>
                    <p class="text-sm text-slate-600">Xác minh thực tế giúp chủ nhà tránh rủi ro và khách thuê đủ điều kiện.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                    <div class="mb-4 text-2xl">🛠️</div>
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Quản lý vận hành</h3>
                    <p class="text-sm text-slate-600">Xử lý bảo trì, thu tiền, và chăm sóc khách thuê để tài sản vận hành mượt mà.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                    <div class="mb-4 text-2xl">📄</div>
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Hợp đồng & pháp lý</h3>
                    <p class="text-sm text-slate-600">Hỗ trợ hợp đồng thuê rõ ràng, bảo vệ quyền lợi chủ nhà theo quy định.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                    <div class="mb-4 text-2xl">📊</div>
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Báo cáo minh bạch</h3>
                    <p class="text-sm text-slate-600">Báo cáo thu chi, lịch sử sửa chữa và tình trạng tài sản cập nhật hàng tháng.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-100 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-800">NỖI LO CỦA CHỦ NHÀ</span>
                <h2 class="mt-6 text-3xl font-bold text-slate-900">Ba điều chủ nhà quan tâm nhất</h2>
                <p class="mt-4 text-sm text-slate-600 max-w-2xl mx-auto">Không phải chỉ là đăng tin — LANDTEK giải quyết vấn đề vận hành và gia tăng giá trị tài sản.</p>
            </div>
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-3xl border border-slate-200 bg-white p-8">
                    <h3 class="text-xl font-semibold text-slate-900">Khách thuê chất lượng</h3>
                    <p class="mt-4 text-sm text-slate-600">Lọc khách thuê qua dữ liệu, lịch sử và đội ngũ kiểm tra thực tế trước khi ký hợp đồng.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-8">
                    <h3 class="text-xl font-semibold text-slate-900">Tài sản luôn ổn định</h3>
                    <p class="mt-4 text-sm text-slate-600">Quản lý bảo trì định kỳ, xử lý sự cố nhanh và bảo vệ tài sản trước hao mòn.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-8">
                    <h3 class="text-xl font-semibold text-slate-900">Báo cáo minh bạch</h3>
                    <p class="mt-4 text-sm text-slate-600">Mỗi khoản thu, chi, và cam kết đều được ghi nhận rõ ràng trong báo cáo hàng tháng.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-950 py-20 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-flex rounded-full bg-amber-400/15 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-200">GÓI DỊCH VỤ CHỦ NHÀ</span>
                <h2 class="mt-6 text-3xl font-bold">3 tầng dịch vụ quản lý gia sản</h2>
                <p class="mt-4 text-sm text-slate-300 max-w-3xl mx-auto">Chủ nhà chọn dịch vụ phù hợp: từ kết nối thuê, đến quản lý vận hành, đến tư vấn khai thác gia tăng giá trị.</p>
            </div>
            <div class="grid gap-4">
                <div class="rounded-3xl bg-slate-900/80 p-6 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <div class="text-3xl font-bold text-amber-400">1</div>
                        <h3 class="mt-4 text-xl font-semibold">Cho thuê & kết nối</h3>
                        <p class="mt-3 text-sm text-slate-300">Xác minh tài sản, chụp hình, đăng tin và kết nối khách thuê hợp lý.</p>
                    </div>
                    <span class="mt-4 inline-flex rounded-full bg-white/10 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-200 sm:mt-0">Phí một lần</span>
                </div>
                <div class="rounded-3xl bg-amber-400 p-6 sm:flex sm:items-center sm:justify-between text-slate-950">
                    <div>
                        <div class="text-3xl font-bold">2</div>
                        <h3 class="mt-4 text-xl font-semibold">Managed Rental</h3>
                        <p class="mt-3 text-sm">Thu tiền, xử lý dịch vụ, đối soát và chăm sóc khách thuê hàng tháng.</p>
                    </div>
                    <span class="mt-4 inline-flex rounded-full bg-slate-950 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-white sm:mt-0">5–8%/tháng</span>
                </div>
                <div class="rounded-3xl bg-slate-900/80 p-6 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <div class="text-3xl font-bold text-amber-400">3</div>
                        <h3 class="mt-4 text-xl font-semibold">Tư vấn khai thác gia sản</h3>
                        <p class="mt-3 text-sm text-slate-300">Đề xuất cải tạo, tối ưu giá thuê và quản lý tài sản dài hạn.</p>
                    </div>
                    <span class="mt-4 inline-flex rounded-full bg-white/10 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-200 sm:mt-0">Tư vấn riêng</span>
                </div>
            </div>

            <div class="mt-12 rounded-3xl bg-slate-800 p-8 text-center">
                <h3 class="text-lg font-semibold">Nhận tư vấn quản lý gia sản miễn phí</h3>
                <p class="mt-3 text-sm text-slate-300">Đội ngũ LANDTEK khảo sát tài sản và đề xuất phương án khai thác phù hợp, không ràng buộc.</p>
                <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('property.post') }}" class="inline-flex items-center justify-center rounded-2xl bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950">Đăng ký tư vấn</a>
                    <a href="tel:02588886868" class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white">Gọi ngay: 0258 888 6868</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
