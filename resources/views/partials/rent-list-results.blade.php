                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-gray-100 shadow-sm">
                        <p class="text-sm text-gray-500">
                            <span class="font-bold text-teal-700 text-base">{{ count($properties) }}</span> tin cho thuê tại Nha Trang
                        </p>
                        <select name="sort" class="h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm outline-none font-medium text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                            <option value="">Tin xác thực trước</option>
                            <option value="price-asc" {{ request('sort') === 'price-asc' ? 'selected' : '' }}>Giá thấp → cao</option>
                            <option value="price-desc" {{ request('sort') === 'price-desc' ? 'selected' : '' }}>Giá cao → thấp</option>
                        </select>
                    </div>

                    @if(count($properties) === 0)
                        <div class="rounded-2xl border border-dashed border-gray-300 bg-white py-20 text-center shadow-sm mt-4">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                <i class="fas fa-search-minus text-2xl text-gray-400"></i>
                            </div>
                            <p class="font-bold text-gray-900 text-lg">Không tìm thấy tin phù hợp</p>
                            <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">Thử nới rộng bộ lọc hoặc xóa điều kiện tìm kiếm để xem thêm nhiều kết quả hơn.</p>
                            <a href="{{ route('rent.list') }}" class="mt-6 inline-flex items-center justify-center bg-teal-50 text-teal-700 font-semibold px-6 py-2.5 rounded-xl hover:bg-teal-100 transition-colors">
                                Xóa tất cả bộ lọc
                            </a>
                        </div>
                    @else
                        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach($properties as $property)
                                @php
                                    $isFavorited = in_array($property['id'], $userWishlists ?? []);
                                @endphp
                                <x-property-card :property="$property" :isFavorited="$isFavorited" />
                            @endforeach
                        </div>
                    @endif
