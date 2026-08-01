@extends('layouts.app')

@section('title', 'Chủ nhà & Quản lý gia sản — LANDTEK')
@section('description', 'Giao chìa khóa — nhận dòng tiền. LANDTEK quản lý vòng đời tài sản: tìm khách, thu tiền, xử lý sự cố, báo cáo minh bạch.')

@section('content')
<div class="bg-white min-h-screen">

    <div class="bg-gray-100 px-4 py-2.5 text-[12.5px] text-gray-500 md:px-8">
        <div class="mx-auto max-w-[1180px]">
            <a href="{{ route('home') }}" class="hover:text-navy">Trang chủ</a>
            <span class="mx-1.5">/</span>
            <span class="font-semibold text-navy">Chủ nhà &amp; Quản lý gia sản</span>
        </div>
    </div>

    {{-- Hero + lead form --}}
    <section id="tu-van" class="relative overflow-hidden bg-gradient-to-br from-navy-deep via-navy to-navy-mid px-4 py-12 md:px-8 md:py-16">
        <div class="pointer-events-none absolute -top-36 -right-24 h-[460px] w-[460px] rounded-full bg-[radial-gradient(circle,rgba(245,158,11,0.16),transparent_70%)]"></div>
        <div class="relative z-10 mx-auto grid max-w-[1180px] items-center gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:gap-12">
            <div>
                <p class="mb-3.5 text-[12.5px] font-bold tracking-[0.2em] text-amber-brand">DÀNH CHO CHỦ NHÀ</p>
                <h1 class="mb-4 text-3xl font-extrabold leading-snug text-white md:text-4xl">
                    Giao chìa khóa — nhận dòng tiền.<br>
                    <span class="text-amber-brand">Chúng tôi lo phần còn lại.</span>
                </h1>
                <p class="mb-6 max-w-[480px] text-[15px] leading-relaxed text-navy-muted">
                    LANDTEK quản lý toàn bộ vòng đời tài sản của bạn: tìm khách, thu tiền, xử lý sự cố, báo cáo minh bạch mỗi tháng — để bạn thảnh thơi tận hưởng cuộc sống.
                </p>
                <div class="flex flex-wrap gap-7">
                    <div>
                        <div class="text-[22px] font-extrabold text-white">100%</div>
                        <div class="text-[11.5px] text-[#8FB1DE]">Tài sản xác thực tận nơi</div>
                    </div>
                    <div>
                        <div class="text-[22px] font-extrabold text-white">5–8%</div>
                        <div class="text-[11.5px] text-[#8FB1DE]">Phí quản lý minh bạch</div>
                    </div>
                    <div>
                        <div class="text-[22px] font-extrabold text-white">24h</div>
                        <div class="text-[11.5px] text-[#8FB1DE]">Phản hồi sự cố</div>
                    </div>
                </div>
            </div>

            <form class="rounded-2xl bg-white p-6 shadow-2xl shadow-black/25 md:p-7" onsubmit="event.preventDefault(); SwalSuccess.fire({title:'Đã nhận yêu cầu!', text:'Đội ngũ LANDTEK sẽ liên hệ trong 24h.'});">
                <h3 class="mb-1 text-[17px] font-bold text-navy">Nhận tư vấn miễn phí</h3>
                <p class="mb-4 text-xs text-gray-500">Đội ngũ LANDTEK liên hệ trong 24h, không ràng buộc</p>
                <div class="mb-3">
                    <label class="mb-1.5 block text-[11.5px] font-bold text-navy">Số điện thoại</label>
                    <input type="tel" required placeholder="VD: 0905 xxx xxx"
                           class="w-full rounded-lg border-[1.5px] border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-navy">
                </div>
                <div class="mb-3">
                    <label class="mb-1.5 block text-[11.5px] font-bold text-navy">Khu vực bất động sản</label>
                    @include('components.area-select', [
                        'name' => 'area',
                        'required' => false,
                        'placeholder' => 'Chọn khu vực...',
                        'class' => 'w-full rounded-lg border-[1.5px] border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-navy',
                    ])
                </div>
                <button type="submit" class="mt-1.5 w-full rounded-lg bg-amber-brand py-3 text-sm font-bold text-navy transition hover:brightness-110">
                    Đăng ký tư vấn ngay →
                </button>
                <p class="mt-2.5 text-center text-[10.5px] text-gray-500">Hoặc gọi trực tiếp: 086 8979799</p>
            </form>
        </div>
    </section>

    {{-- Pain points --}}
    <section class="px-4 py-14 md:px-8 md:py-16">
        <div class="mx-auto mb-10 max-w-[680px] text-center">
            <span class="mb-3 inline-block rounded-full bg-red-50 px-3.5 py-1 text-xs font-bold tracking-wide text-red-600">CHÚNG TÔI HIỂU NỖI LO CỦA BẠN</span>
            <h2 class="mb-2.5 text-2xl font-extrabold text-navy md:text-[27px]">Sở hữu tài sản không nên là một gánh nặng</h2>
            <p class="text-sm text-gray-500">Ba nỗi lo phổ biến nhất của chủ nhà — và cũng chính là lý do LANDTEK ra đời</p>
        </div>
        <div class="mx-auto grid max-w-[1000px] gap-5 md:grid-cols-3">
            @foreach([
                ['quote' => 'Tôi ở xa, không thể tự quản lý', 'desc' => 'Không biết nhà mình đang ra sao, ai đang ở, có hư hỏng gì không — sống trong bất an thường trực.'],
                ['quote' => 'Tôi sợ bị lừa, mất uy tín với khách thuê', 'desc' => 'Từng gặp môi giới thiếu trách nhiệm, hoặc khách thuê không đáng tin, phá hoại tài sản.'],
                ['quote' => 'Tôi không có thời gian xử lý sự cố', 'desc' => 'Bận công việc riêng, không thể tự đòi tiền thuê hàng tháng hay chạy đi sửa ống nước lúc nửa đêm.'],
            ] as $p)
                <div class="rounded-[10px] border border-red-50 border-l-4 border-l-red-600 bg-white p-5">
                    <p class="mb-2.5 text-[14.5px] font-semibold italic leading-snug text-navy">
                        <span class="text-red-600 text-xl leading-none">“</span>{{ $p['quote'] }}
                    </p>
                    <p class="text-[12.5px] leading-relaxed text-gray-500">{{ $p['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 3 tiers --}}
    <section id="managed-rental" class="bg-navy-mist px-4 py-14 md:px-8 md:py-16">
        <div class="mx-auto mb-10 max-w-[680px] text-center">
            <span class="mb-3 inline-block rounded-full bg-white px-3.5 py-1 text-xs font-bold tracking-wide text-navy-soft">GIẢI PHÁP LANDTEK</span>
            <h2 class="mb-2.5 text-2xl font-extrabold text-navy md:text-[27px]">3 tầng dịch vụ — chọn đúng mức bạn cần</h2>
            <p class="text-sm text-gray-500">Từ hỗ trợ tìm khách đến quản lý trọn gói, LANDTEK đồng hành theo đúng nhu cầu của bạn</p>
        </div>
        <div class="mx-auto grid max-w-[1180px] gap-5 lg:grid-cols-3 lg:items-stretch">
            <div class="rounded-2xl border border-[#C7D9F0] bg-navy-mist p-6 md:p-7">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-[11px] bg-navy-soft text-[17px] font-extrabold text-white">1</div>
                <h3 class="mb-2.5 text-[19px] font-bold text-navy">Cho thuê &amp; Kết nối</h3>
                <ul class="mb-4 space-y-1.5 text-[12.5px] leading-relaxed text-gray-500">
                    <li class="relative pl-4 before:absolute before:left-0 before:font-extrabold before:text-navy-soft before:content-['✓']">Khảo sát, chụp ảnh, xác thực tài sản tận nơi</li>
                    <li class="relative pl-4 before:absolute before:left-0 before:font-extrabold before:text-navy-soft before:content-['✓']">Đăng tin trên nền tảng, tiếp cận khách chất lượng</li>
                    <li class="relative pl-4 before:absolute before:left-0 before:font-extrabold before:text-navy-soft before:content-['✓']">Hỗ trợ đàm phán và soạn hợp đồng chuẩn</li>
                </ul>
                <div class="border-t border-[#C7D9F0] pt-2 text-[11.5px] font-extrabold text-navy-soft">Phí: 50–100% tiền thuê tháng đầu</div>
            </div>

            <div class="relative rounded-2xl bg-navy p-6 shadow-xl shadow-navy/30 md:p-7 lg:-translate-y-2.5">
                <span class="absolute -top-3 left-6 rounded-full bg-amber-brand px-3 py-1 text-[10.5px] font-extrabold text-navy">PHỔ BIẾN NHẤT</span>
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-[11px] bg-amber-brand text-[17px] font-extrabold text-navy">2</div>
                <h3 class="mb-2.5 text-[19px] font-bold text-white">Managed Rental</h3>
                <ul class="mb-4 space-y-1.5 text-[12.5px] leading-relaxed text-navy-muted">
                    <li class="relative pl-4 before:absolute before:left-0 before:font-extrabold before:text-amber-brand before:content-['✓']">Thu tiền thuê, chuyển khoản đúng hạn mỗi tháng</li>
                    <li class="relative pl-4 before:absolute before:left-0 before:font-extrabold before:text-amber-brand before:content-['✓']">Xử lý sự cố (điện, nước, hỏng hóc) trong 24h</li>
                    <li class="relative pl-4 before:absolute before:left-0 before:font-extrabold before:text-amber-brand before:content-['✓']">Báo cáo minh bạch hàng tháng</li>
                    <li class="relative pl-4 before:absolute before:left-0 before:font-extrabold before:text-amber-brand before:content-['✓']">Hỗ trợ gia hạn / chấm dứt hợp đồng đúng quy định</li>
                </ul>
                <div class="border-t border-white/20 pt-2 text-[11.5px] font-extrabold text-amber-brand">Phí: 5–8% giá thuê / tháng</div>
            </div>

            <div class="rounded-2xl bg-gradient-to-br from-amber-950 to-amber-900 p-6 md:p-7">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-[11px] bg-amber-brand text-[17px] font-extrabold text-navy">3</div>
                <h3 class="mb-2.5 text-[19px] font-bold text-white">Tư vấn khai thác gia sản</h3>
                <ul class="mb-4 space-y-1.5 text-[12.5px] leading-relaxed text-amber-100">
                    <li class="relative pl-4 before:absolute before:left-0 before:font-extrabold before:text-amber-brand before:content-['✓']">Phân tích nên cho thuê dài hạn hay ngắn hạn</li>
                    <li class="relative pl-4 before:absolute before:left-0 before:font-extrabold before:text-amber-brand before:content-['✓']">Đề xuất cải tạo để tăng giá trị tài sản</li>
                    <li class="relative pl-4 before:absolute before:left-0 before:font-extrabold before:text-amber-brand before:content-['✓']">Chiến lược tối ưu dòng tiền cho danh mục nhiều BĐS</li>
                </ul>
                <div class="border-t border-white/25 pt-2 text-[11.5px] font-extrabold text-amber-brand">Phí: Tư vấn theo dự án riêng</div>
            </div>
        </div>
    </section>

    {{-- Process --}}
    <section class="px-4 py-14 md:px-8 md:py-16">
        <div class="mx-auto mb-10 max-w-[640px] text-center">
            <span class="mb-3 inline-block rounded-full bg-navy-mist px-3.5 py-1 text-xs font-bold tracking-wide text-navy-soft">QUY TRÌNH HỢP TÁC</span>
            <h2 class="text-2xl font-extrabold text-navy md:text-[27px]">5 bước đơn giản, không phức tạp</h2>
        </div>
        <div class="mx-auto grid max-w-[1180px] gap-6 sm:grid-cols-2 lg:grid-cols-5">
            @foreach([
                ['Khảo sát miễn phí', 'Đội ngũ đến tận nơi xem, tư vấn định giá'],
                ['Ký thỏa thuận', 'Thống nhất mức phí, chọn gói dịch vụ phù hợp'],
                ['Đăng tin & tìm khách', 'Xác thực, đăng tin, sàng lọc khách chất lượng'],
                ['Ký hợp đồng thuê', 'Hoàn thiện hồ sơ, bàn giao tài sản'],
                ['Vận hành hàng tháng', 'Thu tiền, báo cáo, xử lý sự cố định kỳ'],
            ] as $i => [$title, $desc])
                <div class="text-center">
                    <div class="mx-auto mb-3.5 flex h-11 w-11 items-center justify-center rounded-full bg-navy text-[17px] font-extrabold text-white">{{ $i + 1 }}</div>
                    <h4 class="mb-1.5 text-[13.5px] font-bold text-navy">{{ $title }}</h4>
                    <p class="text-[11px] leading-relaxed text-gray-500">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Report sample --}}
    <section class="bg-navy-mist px-4 py-14 md:px-8 md:py-16">
        <div class="mx-auto grid max-w-[1100px] items-center gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-12">
            <div>
                <span class="mb-3 inline-block rounded-full bg-white px-3.5 py-1 text-xs font-bold tracking-wide text-green-700">MINH BẠCH THẬT SỰ</span>
                <h2 class="mb-3.5 text-2xl font-extrabold text-navy md:text-[26px]">Không nói suông — đây là báo cáo thật mỗi tháng</h2>
                <p class="mb-5 text-sm leading-relaxed text-gray-500">
                    Mỗi tháng, bạn nhận được báo cáo rõ ràng qua Zalo/Email — không cần hỏi, không cần nhắc, mọi con số đều có căn cứ.
                </p>
                <ul class="space-y-3 text-[13px] text-gray-800">
                    <li class="relative pl-6 before:absolute before:left-0 before:font-extrabold before:text-green-600 before:content-['✓']">Tình trạng thu tiền: đã thu đủ hay chưa, ngày nhận</li>
                    <li class="relative pl-6 before:absolute before:left-0 before:font-extrabold before:text-green-600 before:content-['✓']">Số tiền đã chuyển khoản cho bạn sau khi trừ phí</li>
                    <li class="relative pl-6 before:absolute before:left-0 before:font-extrabold before:text-green-600 before:content-['✓']">Tình trạng nhà: ổn định hay đã xử lý sự cố gì</li>
                    <li class="relative pl-6 before:absolute before:left-0 before:font-extrabold before:text-green-600 before:content-['✓']">Mọi chi phí phát sinh đều kèm hóa đơn, chứng từ</li>
                </ul>
            </div>
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl shadow-navy/15">
                <div class="bg-navy px-5 py-4 text-white">
                    <div class="mb-1 text-[10.5px] font-bold tracking-wide text-amber-brand">BÁO CÁO THÁNG 7/2026</div>
                    <h4 class="text-[15px] font-bold">Căn hộ 2PN — 123 Phước Hải</h4>
                </div>
                <div class="px-5 py-4">
                    @foreach([
                        ['Tiền thuê tháng này', '8.000.000đ', false],
                        ['Ngày thu tiền', '05/07/2026 ✓', true],
                        ['Phí quản lý (7%)', '560.000đ', false],
                        ['Đã chuyển khoản cho bạn', '7.440.000đ', true],
                        ['Ngày chuyển khoản', '06/07/2026 ✓', true],
                    ] as [$label, $val, $green])
                        <div class="flex items-center justify-between border-b border-gray-100 py-2.5 last:border-0">
                            <span class="text-[12.5px] text-gray-500">{{ $label }}</span>
                            <span class="text-[13px] font-bold {{ $green ? 'text-green-600' : 'text-navy' }}">{{ $val }}</span>
                        </div>
                    @endforeach
                    <div class="mt-3.5 rounded-[10px] bg-navy-mist px-3.5 py-3 text-xs text-navy">
                        ✓ Tình trạng nhà: <b class="text-green-700">Ổn định</b>, không phát sinh sự cố trong tháng.
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Fee table --}}
    <section class="px-4 py-14 md:px-8 md:py-16">
        <div class="mx-auto mb-8 max-w-[640px] text-center">
            <span class="mb-3 inline-block rounded-full bg-navy-mist px-3.5 py-1 text-xs font-bold tracking-wide text-navy-soft">CHI PHÍ RÕ RÀNG</span>
            <h2 class="text-2xl font-extrabold text-navy md:text-[27px]">Không phí ẩn, không bất ngờ</h2>
        </div>
        <div class="mx-auto max-w-[820px] overflow-hidden rounded-[14px] border border-gray-200">
            <div class="grid grid-cols-3 bg-navy text-[12.5px] font-bold text-white">
                <div class="px-4 py-3.5 md:px-[18px]">Loại phí</div>
                <div class="px-4 py-3.5 md:px-[18px]">Mức phí</div>
                <div class="px-4 py-3.5 md:px-[18px]">Áp dụng khi nào</div>
            </div>
            @foreach([
                ['Phí môi giới (1 lần)', '50–100% tháng đầu', 'Khi ký hợp đồng thuê thành công'],
                ['Phí Managed Rental', '5–8% / tháng', 'Hàng tháng, khi dùng gói quản lý'],
                ['Phí gia hạn hợp đồng', '30–50% tháng', 'Khi hợp đồng cũ gia hạn thành công'],
            ] as $i => [$name, $amt, $when])
                <div class="grid grid-cols-3 text-[13px] {{ $i % 2 ? 'bg-gray-50' : 'bg-white' }}">
                    <div class="border-t border-gray-200 px-4 py-4 font-bold text-navy md:px-[18px]">{{ $name }}</div>
                    <div class="border-t border-gray-200 px-4 py-4 font-extrabold text-amber-brand md:px-[18px]">{{ $amt }}</div>
                    <div class="border-t border-gray-200 px-4 py-4 text-gray-600 md:px-[18px]">{{ $when }}</div>
                </div>
            @endforeach
        </div>
        <p class="mx-auto mt-5 max-w-[820px] text-center text-[12.5px] text-gray-500">
            <span class="font-bold text-green-600">✓ Không bao giờ thu phí trước khi hợp đồng được ký chính thức</span> — đây là nguyên tắc bắt buộc của LANDTEK.
        </p>
    </section>

    {{-- TRUST for owner --}}
    <section class="bg-navy px-4 py-14 md:px-8 md:py-14">
        <div class="mx-auto mb-10 max-w-[680px] text-center">
            <span class="mb-3 inline-block rounded-full bg-amber-brand/15 px-3.5 py-1 text-xs font-bold tracking-wide text-amber-brand">GIÁ TRỊ CỐT LÕI — GÓC NHÌN CHỦ NHÀ</span>
            <h2 class="mb-2.5 text-2xl font-extrabold text-white md:text-[27px]">T.R.U.S.T — 5 cam kết với riêng bạn</h2>
            <p class="text-sm text-navy-muted">Không phải khẩu hiệu suông — mỗi chữ gắn với một nỗi lo cụ thể của chủ nhà</p>
        </div>
        <div class="mx-auto flex max-w-[1180px] gap-4 overflow-x-auto pb-2 md:grid md:grid-cols-5 md:overflow-visible [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach([
                ['T', 'Tận tâm', 'Chăm tài sản của bạn như của chính chúng tôi, chủ động hỏi thăm định kỳ'],
                ['R', 'Rõ ràng', 'Báo cáo đúng hạn mỗi tháng, tin xấu báo sớm hơn tin tốt'],
                ['U', 'Uy tín', 'Không thu phí trước hợp đồng — xóa nỗi sợ bị lừa cọc'],
                ['S', 'Sâu sát địa phương', 'Hiểu từng khu vực Nha Trang, cập nhật giá thị trường hàng tháng'],
                ['T', 'Tăng trưởng bền vững', 'Chủ động đề xuất tăng giá trị tài sản, không chỉ duy trì hiện trạng'],
            ] as [$letter, $title, $desc])
                <div class="min-w-[180px] flex-shrink-0 rounded-[14px] border border-white/12 bg-white/[0.05] p-5 md:min-w-0">
                    <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-full bg-amber-brand text-[15px] font-extrabold text-navy">{{ $letter }}</div>
                    <h4 class="mb-1.5 text-[13px] font-bold text-white">{{ $title }}</h4>
                    <p class="text-[11px] leading-relaxed text-[#9DB8DA]">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- FAQ --}}
    <section class="px-4 py-14 md:px-8 md:py-16" x-data="{ open: 0 }">
        <div class="mx-auto mb-8 max-w-[640px] text-center">
            <span class="mb-3 inline-block rounded-full bg-navy-mist px-3.5 py-1 text-xs font-bold tracking-wide text-navy-soft">CÂU HỎI THƯỜNG GẶP</span>
            <h2 class="text-2xl font-extrabold text-navy md:text-[27px]">Chủ nhà thường hỏi gì</h2>
        </div>
        <div class="mx-auto max-w-[780px]">
            @foreach([
                ['Tôi tự đăng tin được, không cần qua LANDTEK?', 'LANDTEK định hướng là công ty quản lý khai thác gia sản — tin đăng do đội ngũ khảo sát & xác thực tận nơi, đảm bảo chất lượng “Tin đã xác thực”. Bạn có thể liên hệ để được tư vấn gói phù hợp.'],
                ['Phí quản lý 5–8% có cao không?', 'Mức phí tương đương mặt bằng chung thị trường. Đổi lại bạn có: khách đã sàng lọc kỹ, hợp đồng chuẩn pháp lý, hỗ trợ xử lý sự cố 24h — tiết kiệm thời gian và giảm rủi ro nhiều hơn chi phí này.'],
                ['Nếu khách thuê phá hoại tài sản thì sao?', 'Mọi khoản trừ từ tiền cọc đều có bằng chứng cụ thể (ảnh so sánh trước/sau, hóa đơn sửa chữa). Biên bản bàn giao ban đầu có ảnh chụp tình trạng nhà làm căn cứ đối chiếu.'],
                ['Tôi có thể ngừng hợp tác bất cứ lúc nào không?', 'Có. Thỏa thuận hợp tác có thời hạn rõ ràng (thường 1–3 tháng, có thể gia hạn), bạn chủ động quyết định tiếp tục hay dừng sau mỗi kỳ.'],
            ] as $i => [$q, $a])
                <div class="border-b border-gray-200 py-4">
                    <button type="button" class="flex w-full items-center justify-between text-left text-[14.5px] font-bold text-navy" @click="open = open === {{ $i }} ? -1 : {{ $i }}">
                        <span>{{ $q }}</span>
                        <span class="ml-3 text-gray-400" x-text="open === {{ $i }} ? '⌃' : '⌄'"></span>
                    </button>
                    <p class="mt-2.5 text-[13px] leading-relaxed text-gray-500" x-show="open === {{ $i }}" x-cloak>{{ $a }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-deep to-navy px-4 py-16 text-center md:px-8">
        <div class="pointer-events-none absolute bottom-[-160px] left-1/2 h-[400px] w-[600px] -translate-x-1/2 rounded-full bg-[radial-gradient(ellipse,rgba(245,158,11,0.15),transparent_70%)]"></div>
        <div class="relative z-10">
            <h2 class="mb-3.5 text-2xl font-extrabold text-white md:text-[28px]">Sẵn sàng để tài sản của bạn thảnh thơi sinh lời?</h2>
            <p class="mx-auto mb-7 max-w-[520px] text-[14.5px] text-navy-muted">
                Khảo sát miễn phí, tư vấn không ràng buộc — đội ngũ LANDTEK liên hệ trong 24h.
            </p>
            <div class="flex flex-col items-center justify-center gap-3.5 sm:flex-row">
                <a href="#tu-van" class="inline-flex items-center gap-2 rounded-lg bg-amber-brand px-6 py-3 text-sm font-bold text-navy transition hover:brightness-110">
                    <i class="fas fa-phone"></i> Đăng ký tư vấn miễn phí
                </a>
                <a href="tel:0868979799" class="inline-flex items-center gap-2 rounded-lg border-[1.5px] border-white/40 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                    Gọi ngay: 086 8979799
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
