@extends('layouts.app')

@section('title', 'LANDTEK — Thuê & Quản lý gia sản BĐS Nha Trang')
@section('description', 'Thuê nhà đã xác thực tại Nha Trang. Chủ nhà giao chìa khóa — nhận dòng tiền với Managed Rental.')

@section('content')
<div class="bg-white min-h-screen">

    {{-- HERO — Dual path --}}
    <section class="relative overflow-hidden bg-gradient-to-b from-navy-deep via-navy to-navy-mid px-4 pt-14 pb-0 md:px-8 md:pt-16">
        <div class="pointer-events-none absolute -top-28 -right-24 h-[420px] w-[420px] rounded-full bg-[radial-gradient(circle,rgba(245,158,11,0.18),transparent_70%)]"></div>
        <div class="relative z-10 mx-auto max-w-[1180px] text-center">
            <p class="mb-3.5 text-[11px] font-bold tracking-[0.2em] text-amber-brand md:text-[13px]">
                NỀN TẢNG QUẢN LÝ &amp; CHO THUÊ BẤT ĐỘNG SẢN NHA TRANG
            </p>
            <h1 class="mb-3.5 text-3xl font-extrabold leading-tight text-white sm:text-4xl md:text-[40px]">
                Thuê nhà Nha Trang: <span class="text-amber-brand">an tâm</span>, dễ dàng
            </h1>
            <p class="mx-auto mb-8 max-w-[560px] text-sm leading-relaxed text-navy-muted md:mb-9 md:text-[15.5px]">
                Với người thuê: nhà thật, xem tận nơi. Với chủ nhà: tài sản của bạn — trách nhiệm của chúng tôi.
            </p>

            <div class="mx-auto flex max-w-[900px] flex-col gap-4 md:flex-row md:gap-5">
                <a href="{{ route('rent.list') }}"
                   class="group flex flex-1 flex-col rounded-2xl border border-white/15 bg-white/[0.06] p-6 text-left backdrop-blur-sm transition hover:border-amber-brand/40 md:p-7">
                    <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-amber-brand">
                        <i class="fas fa-search text-lg"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-bold text-white md:text-[19px]">Tôi cần thuê nhà</h3>
                    <p class="mb-5 min-h-[40px] flex-1 text-[13px] leading-relaxed text-navy-muted">
                        Căn hộ, nhà phố, văn phòng đã xác thực tận nơi — không tin ảo, xem lịch và đặt hẹn ngay trên nền tảng.
                    </p>
                    <span class="inline-flex w-full items-center justify-center rounded-lg bg-white px-4 py-2.5 text-[13.5px] font-bold text-navy transition group-hover:bg-amber-brand">
                        Tìm nhà ngay →
                    </span>
                </a>

                <a href="{{ route('owner') }}"
                   class="group flex flex-1 flex-col rounded-2xl border border-amber-brand/55 bg-amber-brand/10 p-6 text-left backdrop-blur-sm transition hover:bg-amber-brand/15 md:p-7">
                    <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-amber-brand/20 text-amber-brand">
                        <i class="fas fa-house text-lg"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-bold text-white md:text-[19px]">Tôi có nhà cho thuê</h3>
                    <p class="mb-5 min-h-[40px] flex-1 text-[13px] leading-relaxed text-navy-muted">
                        Giao chìa khóa, nhận dòng tiền hàng tháng và báo cáo minh bạch — LANDTEK quản lý toàn bộ vòng đời tài sản.
                    </p>
                    <span class="inline-flex w-full items-center justify-center rounded-lg bg-amber-brand px-4 py-2.5 text-[13.5px] font-bold text-navy transition group-hover:brightness-110">
                        Tư vấn miễn phí →
                    </span>
                </a>
            </div>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-8 border-t border-white/15 py-5 md:gap-11">
                <div class="text-center">
                    <div class="text-xl font-extrabold text-white">100%</div>
                    <div class="mt-0.5 text-[11.5px] text-[#8FB1DE]">Tin xác thực tận nơi</div>
                </div>
                <div class="text-center">
                    <div class="text-xl font-extrabold text-white">10</div>
                    <div class="mt-0.5 text-[11.5px] text-[#8FB1DE]">Môi giới đồng hành</div>
                </div>
                <div class="text-center">
                    <div class="text-xl font-extrabold text-white">5–8%</div>
                    <div class="mt-0.5 text-[11.5px] text-[#8FB1DE]">Phí Managed Rental</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Differentiators --}}
    <section class="px-4 py-12 md:px-8 md:py-16">
        <div class="mx-auto grid max-w-[1180px] gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-[18px]">
            @foreach([
                ['icon' => 'fa-shield-halved', 'title' => 'Tin đã xác thực', 'desc' => 'Verified Property loại bỏ tin ảo, dữ liệu chuẩn xác.'],
                ['icon' => 'fa-calendar-check', 'title' => 'Đặt lịch xem nhà', 'desc' => 'Chọn lịch trống và đặt xem nhà ngay trên platform.'],
                ['icon' => 'fa-globe', 'title' => 'Đa ngôn ngữ', 'desc' => 'Hỗ trợ Việt, Anh, Nga, Hàn, Trung cho khách quốc tế.'],
                ['icon' => 'fa-user-tie', 'title' => 'Môi giới uy tín', 'desc' => 'Hệ thống Verified Agent Silver / Gold minh bạch.'],
            ] as $item)
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-[10px] bg-navy-mist text-navy-soft">
                        <i class="fas {{ $item['icon'] }}"></i>
                    </div>
                    <h4 class="mb-1.5 text-[14.5px] font-bold text-navy">{{ $item['title'] }}</h4>
                    <p class="text-xs leading-relaxed text-gray-500">{{ $item['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Renter listings --}}
    <section class="bg-navy-mist px-4 py-14 md:px-8 md:py-16">
        <div class="mx-auto mb-8 max-w-[640px] text-center md:mb-10">
            <span class="mb-2.5 inline-block rounded-full bg-white px-3.5 py-1 text-[12.5px] font-bold tracking-wide text-navy-soft">DÀNH CHO NGƯỜI THUÊ</span>
            <h2 class="mb-2.5 text-2xl font-extrabold text-navy md:text-[28px]">Tin cho thuê nổi bật</h2>
            <p class="text-sm leading-relaxed text-gray-500 md:text-[14.5px]">Các căn đã xác thực tận nơi, cập nhật mới nhất tại Nha Trang.</p>
        </div>

        {{-- Mobile: horizontal scroll --}}
        <div class="mx-auto max-w-[1180px] md:hidden">
            <div class="flex gap-4 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @forelse(array_slice($properties, 0, 6) as $property)
                    @include('components.property-card-home', ['property' => $property, 'userWishlists' => $userWishlists ?? [], 'compact' => true])
                @empty
                    <p class="w-full py-10 text-center text-gray-400">Không có tin đăng nào.</p>
                @endforelse
            </div>
        </div>

        {{-- Desktop grid --}}
        <div class="mx-auto hidden max-w-[1180px] gap-5 md:grid md:grid-cols-2 lg:grid-cols-3">
            @forelse(array_slice($properties, 0, 6) as $property)
                @include('components.property-card-home', ['property' => $property, 'userWishlists' => $userWishlists ?? [], 'compact' => false])
            @empty
                <div class="col-span-full py-16 text-center text-gray-400">
                    <i class="fas fa-building mb-3 block text-4xl opacity-30"></i>
                    Không có tin đăng nào.
                </div>
            @endforelse
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('rent.list') }}"
               class="inline-flex items-center gap-2 rounded-lg border-[1.5px] border-navy px-5 py-2.5 text-[13.5px] font-bold text-navy transition hover:bg-navy hover:text-white">
                Xem tất cả tin cho thuê →
            </a>
        </div>
    </section>

    {{-- T.R.U.S.T bridge --}}
    <section class="relative overflow-hidden bg-navy px-4 py-14 md:px-8 md:py-16">
        <div class="pointer-events-none absolute -bottom-36 -left-24 h-[400px] w-[400px] rounded-full bg-[radial-gradient(circle,rgba(26,86,168,0.35),transparent_70%)]"></div>
        <div class="relative z-10 mx-auto max-w-[1180px]">
            <div class="mx-auto mb-10 max-w-[680px] text-center">
                <span class="mb-3 inline-block rounded-full bg-amber-brand/15 px-3.5 py-1 text-xs font-bold tracking-wide text-amber-brand">GIÁ TRỊ CỐT LÕI</span>
                <h2 class="mb-3 text-2xl font-extrabold text-white md:text-[28px]">Vì sao cả người thuê và chủ nhà đều tin LANDTEK</h2>
                <p class="text-sm leading-relaxed text-navy-muted md:text-[14.5px]">
                    5 giá trị T.R.U.S.T là nền tảng cho mọi dịch vụ — từ một lượt xem nhà đến một hợp đồng quản lý gia sản nhiều năm.
                </p>
            </div>

            <div class="flex gap-4 overflow-x-auto pb-2 md:grid md:grid-cols-5 md:gap-4 md:overflow-visible [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @foreach([
                    ['T', 'Tận tâm', 'Chăm tài sản của khách như của mình'],
                    ['R', 'Rõ ràng', 'Báo cáo đúng hạn, mọi khoản chi minh bạch'],
                    ['U', 'Uy tín', 'Không thu phí trước hợp đồng, không tin ảo'],
                    ['S', 'Sâu sát địa phương', 'Mọi listing xác thực tận nơi tại Nha Trang'],
                    ['T', 'Tăng trưởng bền vững', 'Chủ động đề xuất tăng giá trị tài sản'],
                ] as [$letter, $title, $desc])
                    <div class="min-w-[160px] flex-shrink-0 rounded-[14px] border border-white/12 bg-white/[0.05] p-5 text-center md:min-w-0">
                        <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-amber-brand text-[17px] font-extrabold text-navy">{{ $letter }}</div>
                        <h4 class="mb-1.5 text-[13.5px] font-bold text-white">{{ $title }}</h4>
                        <p class="text-[11px] leading-relaxed text-[#9DB8DA]">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>

            <p class="mt-8 text-center text-[12.5px] italic text-[#8FB1DE]">
                “Tài sản của bạn — Trách nhiệm của chúng tôi.” Cùng một lời hứa, cho cả người thuê lẫn chủ nhà.
            </p>
        </div>
    </section>

    {{-- Owner / asset management --}}
    <section class="bg-gradient-to-b from-white to-amber-50 px-4 py-14 md:px-8 md:py-16">
        <div class="mx-auto mb-10 max-w-[680px] text-center">
            <span class="mb-3 inline-block rounded-full bg-amber-100 px-3.5 py-1 text-xs font-bold tracking-wide text-amber-900">DÀNH CHO CHỦ NHÀ</span>
            <h2 class="mb-3 text-2xl font-extrabold text-navy md:text-[28px]">Quản lý gia sản — không chỉ là cho thuê</h2>
            <p class="text-sm leading-relaxed text-gray-500 md:text-[14.5px]">
                LANDTEK đồng hành trọn vòng đời tài sản của bạn, theo 3 tầng dịch vụ rõ ràng.
            </p>
        </div>

        <div class="mx-auto mb-10 flex max-w-[820px] flex-col items-center gap-2.5">
            <div class="flex w-full items-center gap-4 rounded-xl border border-[#C7D9F0] bg-navy-mist px-5 py-4 md:px-6">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-[10px] bg-navy-soft text-sm font-extrabold text-white">1</div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-[15px] font-bold text-navy">Cho thuê &amp; Kết nối</h4>
                    <p class="text-xs leading-relaxed text-gray-500">Xác thực tài sản tận nơi, tìm khách thuê chất lượng, hỗ trợ ký kết hợp đồng.</p>
                </div>
                <span class="hidden flex-shrink-0 rounded-full bg-white px-2.5 py-1 text-[10.5px] font-extrabold text-navy-soft sm:inline">Phí 1 lần</span>
            </div>
            <div class="flex w-full scale-[1.02] items-center gap-4 rounded-xl bg-navy px-5 py-4 shadow-lg shadow-navy/25 md:px-6">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-[10px] bg-amber-brand text-sm font-extrabold text-navy">2</div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-[15px] font-bold text-white">Managed Rental — Quản lý vận hành</h4>
                    <p class="text-xs leading-relaxed text-navy-muted">Thu tiền, xử lý sự cố, báo cáo minh bạch hàng tháng. Bạn giao chìa khóa — nhận dòng tiền.</p>
                </div>
                <span class="hidden flex-shrink-0 rounded-full bg-amber-brand px-2.5 py-1 text-[10.5px] font-extrabold text-navy sm:inline">5–8%/tháng</span>
            </div>
            <div class="flex w-full items-center gap-4 rounded-xl bg-gradient-to-r from-amber-950 to-amber-900 px-5 py-4 md:px-6">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-[10px] bg-amber-brand text-sm font-extrabold text-navy">3</div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-[15px] font-bold text-white">Tư vấn khai thác gia sản</h4>
                    <p class="text-xs leading-relaxed text-amber-100">Chiến lược cho thuê dài/ngắn hạn, đề xuất cải tạo tăng giá trị tài sản theo thời gian.</p>
                </div>
                <span class="hidden flex-shrink-0 rounded-full bg-white/20 px-2.5 py-1 text-[10.5px] font-extrabold text-white sm:inline">Tư vấn riêng</span>
            </div>
        </div>

        <div class="mx-auto mb-10 grid max-w-[1000px] gap-4 sm:grid-cols-3">
            @foreach([
                ['icon' => 'fa-coins', 'title' => 'Dòng tiền đều đặn', 'desc' => 'Thu tiền thuê đúng hạn, chuyển khoản minh bạch mỗi tháng'],
                ['icon' => 'fa-chart-pie', 'title' => 'Báo cáo rõ ràng', 'desc' => 'Mọi khoản thu-chi, sự cố đều có hồ sơ theo dõi đầy đủ'],
                ['icon' => 'fa-umbrella-beach', 'title' => 'Thảnh thơi thật sự', 'desc' => 'Không cần tự đòi tiền thuê, tự xử lý sự cố phát sinh'],
            ] as $b)
                <div class="px-3 py-4 text-center">
                    <div class="mb-2.5 text-navy"><i class="fas {{ $b['icon'] }} text-2xl"></i></div>
                    <h4 class="mb-1 text-[13.5px] font-bold text-navy">{{ $b['title'] }}</h4>
                    <p class="text-[11.5px] leading-relaxed text-gray-500">{{ $b['desc'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="mx-auto max-w-[640px] rounded-2xl bg-navy px-6 py-8 text-center shadow-xl shadow-navy/20 md:px-9">
            <h3 class="mb-2 text-lg font-bold text-white md:text-[19px]">Nhận tư vấn quản lý gia sản miễn phí</h3>
            <p class="mb-5 text-[12.5px] text-navy-muted">
                Đội ngũ LANDTEK khảo sát tài sản và đề xuất phương án khai thác phù hợp nhất, không ràng buộc.
            </p>
            <div class="flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('owner') }}#tu-van"
                   class="inline-flex items-center gap-2 rounded-lg bg-amber-brand px-5 py-2.5 text-sm font-bold text-navy transition hover:brightness-110">
                    <i class="fas fa-phone"></i> Đăng ký tư vấn
                </a>
                <a href="{{ route('owner') }}#managed-rental"
                   class="inline-flex items-center gap-2 rounded-lg border border-white/30 bg-white/10 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-white/15">
                    Tìm hiểu Managed Rental
                </a>
            </div>
        </div>
    </section>

    {{-- Projects --}}
    <section class="px-4 py-14 md:px-8 md:py-16">
        <div class="mx-auto mb-8 max-w-[640px] text-center">
            <span class="mb-2.5 inline-block rounded-full bg-navy-mist px-3.5 py-1 text-xs font-bold tracking-wide text-navy-soft">DỰ ÁN TRỌNG ĐIỂM</span>
            <h2 class="text-2xl font-extrabold text-navy md:text-[28px]">Căn hộ chuẩn hóa theo từng dự án</h2>
        </div>

        <div class="mx-auto flex max-w-[1180px] gap-3.5 overflow-x-auto pb-2 md:grid md:grid-cols-5 md:overflow-visible [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach($projects as $proj)
                <a href="{{ route('rent.list', ['project' => $proj['slug']]) }}"
                   class="min-w-[140px] flex-shrink-0 rounded-xl border border-gray-200 bg-white p-3.5 text-center transition hover:border-navy-soft/40 hover:shadow-md md:min-w-0">
                    <div class="mb-2.5 h-[60px] overflow-hidden rounded-lg bg-gradient-to-br from-[#C7D9F0] to-[#8FB1DE]">
                        <img src="{{ $proj['image'] }}" alt="{{ $proj['label'] }}" class="h-full w-full object-cover">
                    </div>
                    <h5 class="mb-0.5 text-[12.5px] font-bold text-navy">{{ $proj['label'] }}</h5>
                    <div class="text-[10px] text-gray-500">Xem tin cho thuê</div>
                </a>
            @endforeach
        </div>
    </section>
</div>

<script>
    function toggleFavorite(button, event) {
        event.preventDefault();
        event.stopPropagation();

        const propertyId = button.getAttribute('data-property-id');
        const icon = button.querySelector('i');
        const isFavorited = button.getAttribute('data-favorited') === 'true';

        if (isFavorited) {
            button.setAttribute('data-favorited', 'false');
            button.classList.remove('bg-red-500', 'text-white', 'shadow-md', 'shadow-red-200');
            button.classList.add('bg-white/80', 'backdrop-blur-sm', 'text-gray-400', 'hover:bg-red-50', 'hover:text-red-400', 'shadow-sm');
            icon.classList.remove('fas');
            icon.classList.add('far');
        } else {
            button.setAttribute('data-favorited', 'true');
            button.classList.add('bg-red-500', 'text-white', 'shadow-md', 'shadow-red-200');
            button.classList.remove('bg-white/80', 'backdrop-blur-sm', 'text-gray-400', 'hover:bg-red-50', 'hover:text-red-400', 'shadow-sm');
            icon.classList.remove('far');
            icon.classList.add('fas');
        }

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
            if (!data.success && data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(error => console.error('Error toggling wishlist:', error));
    }
</script>
@endsection
