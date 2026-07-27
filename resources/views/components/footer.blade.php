<footer class="bg-footer-bg border-t border-slate-100/80 text-muted-foreground mt-16">
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-12 md:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12">
            <!-- Col 1: Logo & Intro -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-teal-600 flex items-center justify-center text-white shadow-xs">
                        <i class="fas fa-water text-sm"></i>
                    </div>
                    <span class="text-lg font-black text-gray-900 tracking-wider">
                        LAND<span class="text-teal-600">TEK</span>
                    </span>
                </div>
                <p class="text-sm text-muted-foreground leading-relaxed max-w-xs">
                    Nền tảng thuê & cho thuê bất động sản chuyên biệt tại Nha Trang. Dữ liệu chuẩn xác, tin đăng xác thực.
                </p>
            </div>

            <!-- Col 2: Khám phá -->
            <div>
                <h4 class="font-bold text-gray-900 mb-4 text-base tracking-wide">Khám phá</h4>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('rent.list', ['type' => 'apartment']) }}" class="text-sm text-muted-foreground hover:text-teal-600 font-medium transition-colors">
                            Thuê căn hộ
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('projects') }}" class="text-sm text-muted-foreground hover:text-teal-600 font-medium transition-colors">
                            Dự án nổi bật
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('agents') }}" class="text-sm text-muted-foreground hover:text-teal-600 font-medium transition-colors">
                            Môi giới uy tín
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Col 3: Dành cho chủ nhà -->
            <div>
                <h4 class="font-bold text-gray-900 mb-4 text-base tracking-wide">Dành cho chủ nhà</h4>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('property.post') }}" class="text-sm text-muted-foreground hover:text-teal-600 font-medium transition-colors">
                            Đăng tin cho thuê
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-sm text-muted-foreground hover:text-teal-600 font-medium transition-colors">
                            Trở thành Verified Agent
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Col 4: Liên hệ -->
            <div>
                <h4 class="font-bold text-gray-900 mb-4 text-base tracking-wide">Liên hệ</h4>
                <ul class="space-y-3 text-sm text-muted-foreground font-medium">
                    <li class="flex items-center gap-2">
                        <span>Hotline: 0258 888 6868</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span>Email: hello@landtek.com.vn</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span>TP. Nha Trang, Khánh Hòa</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="mt-12 pt-8 border-t border-slate-200/40 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-xs text-muted-foreground/80">&copy; {{ date('Y') }} LANDTEK. All rights reserved.</p>
            <div class="flex gap-4 text-xs text-muted-foreground/80">
                <a href="#" class="hover:text-teal-600 transition-colors">Điều khoản dịch vụ</a>
                <a href="#" class="hover:text-teal-600 transition-colors">Chính sách bảo mật</a>
            </div>
        </div>
    </div>
</footer>
