<footer class="bg-navy-deep text-[#8FB1DE] mt-0">
    <div class="max-w-[1180px] mx-auto px-4 md:px-8 pt-12 pb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 pb-8 border-b border-white/10">
            <div>
                <div class="text-white text-xl font-extrabold mb-2.5">LANDTEK</div>
                <p class="text-[12.5px] leading-relaxed max-w-xs">
                    Nền tảng thuê &amp; quản lý gia sản bất động sản chuyên biệt tại Nha Trang. Dữ liệu chuẩn xác, tin đăng xác thực.
                </p>
            </div>
            <div>
                <h5 class="text-white text-[12.5px] font-bold tracking-wide mb-3">KHÁM PHÁ</h5>
                <ul class="space-y-2">
                    {{-- Tạm ẩn: Thuê nhà, Môi giới --}}
                    <li><a href="{{ route('projects') }}" class="text-xs hover:text-white transition">Dự án nổi bật</a></li>
                    <li><a href="{{ route('owner') }}" class="text-xs hover:text-white transition">Chủ nhà &amp; Quản lý gia sản</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white text-[12.5px] font-bold tracking-wide mb-3">CHỦ NHÀ</h5>
                <ul class="space-y-2">
                    <li><a href="{{ route('owner') }}" class="text-xs hover:text-white transition">Quản lý gia sản</a></li>
                    <li><a href="{{ route('owner') }}#managed-rental" class="text-xs hover:text-white transition">Managed Rental</a></li>
                    <li><a href="{{ route('owner') }}#tu-van" class="text-xs hover:text-white transition">Tư vấn khai thác gia sản</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white text-[12.5px] font-bold tracking-wide mb-3">LIÊN HỆ</h5>
                <ul class="space-y-2 text-xs">
                    <li>Hotline: 086 8979799</li>
                    <li>cuonglandtek@gmail.com</li>
                    <li>Phường Nha Trang, Khánh Hòa</li>
                </ul>
            </div>
        </div>
        <p class="pt-5 text-center text-[11.5px] text-[#6688B0]">&copy; {{ date('Y') }} LANDTEK. All rights reserved.</p>
    </div>
</footer>
