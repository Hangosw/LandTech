@extends('layouts.app')

@section('title', 'LANDTEK — Trang chủ')
@section('description', 'LANDTEK kết nối Người thuê và Chủ nhà tại Nha Trang với dịch vụ quản lý gia sản và tin thuê xác thực.')

@section('content')
<div class="bg-slate-950">
    <div class="bg-slate-900 text-slate-200 text-xs uppercase tracking-[0.24em] py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            BẢN DEMO ĐỀ XUẤT — Trang chủ tái cấu trúc cân bằng 50% Người thuê / 50% Chủ nhà, cầu nối là khối giá trị T.R.U.S.T
        </div>
    </div>
    <section class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white py-24 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-12 lg:gap-20 lg:flex-row lg:items-center">
                <div class="lg:w-6/12">
                    <span class="inline-flex rounded-full bg-amber-400/15 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-200">NỀN TẢNG QUẢN LÝ & CHO THUÊ BẤT ĐỘNG SẢN NHA TRANG</span>
                    <h1 class="mt-8 text-3xl sm:text-4xl font-black leading-tight">Thuê nhà Nha Trang; <span class="text-amber-400">an tâm</span>, dễ dàng</h1>
                    <p class="mt-6 max-w-2xl text-sm sm:text-base text-slate-300 leading-7">Với người thuê: nhà thật, xem tận nơi. Với chủ nhà: tài sản của bạn — trách nhiệm của chúng tôi.</p>
                </div>
                <div class="lg:w-5/12">
                    <div class="grid gap-5">
                        <a href="{{ route('rent.list') }}" class="group block rounded-[28px] border border-slate-700/60 bg-slate-950/80 p-8 shadow-2xl shadow-slate-950/20 transition hover:-translate-y-1">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-800 text-white text-xl">🔎</div>
                            <h2 class="mt-6 text-xl font-bold text-white">Tôi cần thuê nhà</h2>
                            <p class="mt-3 text-sm text-slate-300">Căn hộ, nhà phố, văn phòng đã xác thực tận nơi — không tin ảo, xem lịch và đặt hẹn ngay trên nền tảng.</p>
                            <span class="mt-8 inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition group-hover:bg-amber-400">Tìm nhà ngay →</span>
                        </a>
                        <a href="{{ route('property.post') }}" class="group block rounded-[28px] border border-slate-700/60 bg-amber-400 p-8 shadow-2xl shadow-amber-500/20 transition hover:-translate-y-1">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-amber-400 text-xl">🏠</div>
                            <h2 class="mt-6 text-xl font-bold text-slate-950">Tôi có nhà cho thuê</h2>
                            <p class="mt-3 text-sm text-slate-950/80">Giao chìa khóa, nhận dòng tiền hàng tháng và báo cáo minh bạch — LANDTEK quản lý toàn bộ vòng đời tài sản.</p>
                            <span class="mt-8 inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition group-hover:bg-slate-800">Tư vấn miễn phí →</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl bg-slate-900 p-8 text-center">
                <div class="text-5xl font-black text-white">100%</div>
                <p class="mt-4 text-sm text-slate-300">Tin xác thực tận nơi</p>
            </div>
            <div class="rounded-3xl bg-slate-900 p-8 text-center">
                <div class="text-5xl font-black text-white">10</div>
                <p class="mt-4 text-sm text-slate-300">Môi giới đồng hành</p>
            </div>
            <div class="rounded-3xl bg-slate-900 p-8 text-center">
                <div class="text-5xl font-black text-white">5-8%</div>
                <p class="mt-4 text-sm text-slate-300">Phí quản lý Managed Rental</p>
            </div>
        </div>
    </section>
</div>

<section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                    <div class="mb-4 text-2xl">✓</div>
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Tin đã xác thực</h3>
                    <p class="text-sm text-slate-600">Verified Property loại bỏ tin ảo, dữ liệu chuẩn xác.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                    <div class="mb-4 text-2xl">🗓️</div>
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Đặt lịch xem nhà</h3>
                    <p class="text-sm text-slate-600">Chọn lịch trống và đặt xem nhà ngay trên platform.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                    <div class="mb-4 text-2xl">🌐</div>
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Đa ngôn ngữ</h3>
                    <p class="text-sm text-slate-600">Hỗ trợ Việt, Anh, Nga, Hàn, Trung cho khách quốc tế.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                    <div class="mb-4 text-2xl">🛡️</div>
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Môi giới uy tín</h3>
                    <p class="text-sm text-slate-600">Hệ thống Verified Agent Silver / Gold minh bạch.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-100 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-flex rounded-full bg-slate-50 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-700">DÀNH CHO NGƯỜI THUÊ</span>
                <h2 class="mt-6 text-3xl font-bold text-slate-900">Tin cho thuê nổi bật</h2>
                <p class="mt-4 text-sm text-slate-600">Các căn đã xác thực tận nơi, cập nhật mới nhất tại Nha Trang.</p>
            </div>
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl border border-slate-200 bg-white overflow-hidden">
                    <div class="h-40 bg-gradient-to-br from-blue-700 via-slate-900 to-slate-700 relative">
                        <span class="absolute left-4 top-4 rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-emerald-700">✓ Đã xác thực</span>
                    </div>
                    <div class="p-6">
                        <div class="text-lg font-bold text-slate-900">25 triệu/tháng</div>
                        <div class="mt-2 text-sm font-semibold text-slate-700">Nhà nguyên căn KĐT Hà Quang 2 — view công viên</div>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-500">
                            <span>4 PN</span><span>6 WC</span><span>100 m²</span>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white overflow-hidden">
                    <div class="h-40 bg-gradient-to-br from-sky-600 via-blue-700 to-slate-800 relative">
                        <span class="absolute left-4 top-4 rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-emerald-700">✓ Đã xác thực</span>
                    </div>
                    <div class="p-6">
                        <div class="text-lg font-bold text-slate-900">12,5 triệu/tháng</div>
                        <div class="mt-2 text-sm font-semibold text-slate-700">Căn hộ 2PN full nội thất — Scenia Bay view biển</div>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-500">
                            <span>2 PN</span><span>2 WC</span><span>68 m²</span>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white overflow-hidden">
                    <div class="h-40 bg-gradient-to-br from-slate-800 via-slate-900 to-blue-900 relative">
                        <span class="absolute left-4 top-4 rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-emerald-700">✓ Đã xác thực</span>
                    </div>
                    <div class="p-6">
                        <div class="text-lg font-bold text-slate-900">45 triệu/tháng</div>
                        <div class="mt-2 text-sm font-semibold text-slate-700">Mặt bằng kinh doanh mặt tiền Trần Phú</div>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-500">
                            <span>120 m²</span><span>Tầng trệt</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-10 text-center">
                <a href="{{ route('rent.list') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-50">Xem tất cả tin cho thuê →</a>
            </div>
        </div>
    </section>

    <section class="bg-slate-950 py-20 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-flex rounded-full bg-amber-400/15 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-200">GIÁ TRỊ CỐT LÕI</span>
                <h2 class="mt-6 text-3xl font-bold">Vì sao cả người thuê và chủ nhà đều tin LANDTEK</h2>
                <p class="mt-4 text-sm text-slate-300 max-w-3xl mx-auto">5 giá trị T.R.U.S.T là nền tảng cho mọi dịch vụ — từ một lượt xem nhà đến một hợp đồng quản lý gia sản nhiều năm.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-3xl bg-white/5 p-6 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-400 text-slate-950 font-bold">T</div>
                    <h3 class="text-sm font-semibold text-white mb-2">Tận tâm</h3>
                    <p class="text-xs text-slate-300">Chăm tài sản của khách như của mình</p>
                </div>
                <div class="rounded-3xl bg-white/5 p-6 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-400 text-slate-950 font-bold">R</div>
                    <h3 class="text-sm font-semibold text-white mb-2">Rõ ràng</h3>
                    <p class="text-xs text-slate-300">Báo cáo đúng hạn, mọi khoản chi minh bạch</p>
                </div>
                <div class="rounded-3xl bg-white/5 p-6 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-400 text-slate-950 font-bold">U</div>
                    <h3 class="text-sm font-semibold text-white mb-2">Uy tín</h3>
                    <p class="text-xs text-slate-300">Không thu phí trước hợp đồng, không tin ảo</p>
                </div>
                <div class="rounded-3xl bg-white/5 p-6 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-400 text-slate-950 font-bold">S</div>
                    <h3 class="text-sm font-semibold text-white mb-2">Sâu sát địa phương</h3>
                    <p class="text-xs text-slate-300">Mọi listing xác thực tận nơi tại Nha Trang</p>
                </div>
                <div class="rounded-3xl bg-white/5 p-6 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-400 text-slate-950 font-bold">T</div>
                    <h3 class="text-sm font-semibold text-white mb-2">Tăng trưởng bền vững</h3>
                    <p class="text-xs text-slate-300">Chủ động đề xuất tăng giá trị tài sản</p>
                </div>
            </div>
            <div class="mt-10 text-center text-slate-400 text-sm">"Tài sản của bạn — Trách nhiệm của chúng tôi." Cùng một lời hứa, cho cả người thuê lẫn chủ nhà.</div>
        </div>
    </section>

    <section class="bg-[radial-gradient(circle_at_top_right,_rgba(253,236,180,0.4),_transparent_45%)] py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-800">DÀNH CHO CHỦ NHÀ</span>
                <h2 class="mt-6 text-3xl font-bold text-slate-900">Quản lý gia sản — không chỉ là cho thuê</h2>
                <p class="mt-4 text-sm text-slate-600 max-w-3xl mx-auto">LANDTEK đồng hành trọn vòng đời tài sản của bạn, theo 3 tầng dịch vụ rõ ràng.</p>
            </div>
            <div class="grid gap-4">
                <div class="rounded-3xl bg-slate-100 border border-slate-200 p-6 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-700 text-white font-bold">1</div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Cho thuê &amp; Kết nối</h3>
                        <p class="text-sm text-slate-600">Xác thực tài sản tận nơi, tìm khách thuê chất lượng, hỗ trợ ký kết hợp đồng.</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-blue-700">Phí 1 lần</span>
                </div>
                <div class="rounded-3xl bg-amber-400 p-6 shadow-2xl shadow-amber-300/30 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-950 font-bold">2</div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-950">Managed Rental — Quản lý vận hành</h3>
                        <p class="text-sm text-slate-950">Thu tiền, xử lý sự cố, báo cáo minh bạch hàng tháng. Bạn giao chìa khóa — nhận dòng tiền.</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-slate-950 px-3 py-1 text-[11px] font-semibold text-white">5–8%/tháng</span>
                </div>
                <div class="rounded-3xl bg-gradient-to-r from-orange-700 to-amber-400 p-6 flex flex-col gap-4 sm:flex-row sm:items-center text-slate-950">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white font-bold">3</div>
                    <div>
                        <h3 class="text-base font-semibold">Tư vấn khai thác gia sản</h3>
                        <p class="text-sm">Chiến lược cho thuê dài/ngắn hạn, đề xuất cải tạo tăng giá trị tài sản theo thời gian.</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-slate-950">Tư vấn riêng</span>
                </div>
            </div>

            <div class="mt-12 rounded-3xl bg-slate-950 p-8 text-center text-white shadow-2xl shadow-slate-950/20">
                <h3 class="text-lg font-semibold">Nhận tư vấn quản lý gia sản miễn phí</h3>
                <p class="mt-3 text-sm text-slate-300">Đội ngũ LANDTEK khảo sát tài sản và đề xuất phương án khai thác phù hợp nhất, không ràng buộc.</p>
                <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('property.post') }}" class="inline-flex items-center justify-center rounded-2xl bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950">📞 Đăng ký tư vấn</a>
                    <a href="{{ route('rent.list') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white">Tìm hiểu Managed Rental</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-flex rounded-full bg-slate-50 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-700">DỰ ÁN TRỌNG ĐIỂM</span>
                <h2 class="mt-6 text-3xl font-bold text-slate-900">Căn hộ chuẩn hóa theo từng dự án</h2>
            </div>
            <div class="grid gap-6 xl:grid-cols-5">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center">
                    <div class="mb-4 h-16 rounded-2xl bg-gradient-to-br from-slate-400 to-slate-300"></div>
                    <h5 class="text-sm font-semibold text-slate-900">Mường Thanh</h5>
                    <p class="mt-2 text-xs text-slate-500">32 tin · từ 8tr</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center">
                    <div class="mb-4 h-16 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-700"></div>
                    <h5 class="text-sm font-semibold text-slate-900">Vinpearl</h5>
                    <p class="mt-2 text-xs text-slate-500">18 tin · từ 15tr</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center">
                    <div class="mb-4 h-16 rounded-2xl bg-gradient-to-br from-cyan-400 to-slate-700"></div>
                    <h5 class="text-sm font-semibold text-slate-900">Sun Group</h5>
                    <p class="mt-2 text-xs text-slate-500">24 tin · từ 10tr</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center">
                    <div class="mb-4 h-16 rounded-2xl bg-gradient-to-br from-slate-400 to-blue-500"></div>
                    <h5 class="text-sm font-semibold text-slate-900">Scenia Bay</h5>
                    <p class="mt-2 text-xs text-slate-500">15 tin · từ 11tr</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center">
                    <div class="mb-4 h-16 rounded-2xl bg-gradient-to-br from-orange-300 to-amber-400"></div>
                    <h5 class="text-sm font-semibold text-slate-900">Gold Coast</h5>
                    <p class="mt-2 text-xs text-slate-500">9 tin · từ 13tr</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-950 py-20 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold">Sẵn sàng để tài sản của bạn thảnh thơi sinh lời?</h2>
            <p class="mt-4 text-sm text-slate-300 max-w-2xl mx-auto">Khảo sát miễn phí, tư vấn không ràng buộc — đội ngũ LANDTEK liên hệ trong 24h.</p>
            <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="{{ route('property.post') }}" class="inline-flex items-center justify-center rounded-2xl bg-amber-400 px-6 py-3 text-sm font-semibold text-slate-950 hover:bg-amber-300">📞 Đăng ký tư vấn miễn phí</a>
                <a href="tel:02588886868" class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20">Gọi ngay: 0258 888 6868</a>
            </div>
        </div>
    </section>
</div>
@endsection
