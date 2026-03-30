@extends('client.layouts.app')

@section('title','Trang chủ')

@section('content')
    <main class="max-w-[1440px] mx-auto pb-20">
        <section class="px-4 md:px-10 lg:px-20 pt-6">
            <div class="relative rounded-xl overflow-hidden min-h-[500px] flex items-center bg-black">
                <div class="absolute inset-0 opacity-60 bg-cover bg-center"
                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuC5kdFa3m9RUyDOq-JtvVLeftcUlBc6RuUdpkwd-5rIXy21cwcMlwnUIkUZaRfoj74ICQrqwsO7DeeuvrhgFF_x8I92BXUHwJMMRUZf-MR8fjOhUaI9Oxm8WU2eguZGf_UlhPXIrY623OqW6I1pyC1LyqeQLCOmaBjJXRvA_tqBqeuWZ9YFkhq0TXuqygePpabB11X7C97enS5EAu0DtT6mle7wJJJRXh6vPTyatcvr5OshfcRZEWITof1ivLP04JrBjdi79dY67SE');">
                </div>
                <div class="relative z-10 p-8 md:p-16 max-w-2xl text-white">
                    <span class="bg-primary text-black px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 inline-block">Flash Sale</span>
                    <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6">Trải nghiệm Tương lai: iPhone 15 Pro</h1>
                    <p class="text-lg text-gray-200 mb-8">Thiết kế Titan, chip A17 Pro và hệ thống camera chuyên nghiệp. Ưu đãi flagship không thể bỏ lỡ trong thời gian có hạn.</p>
                    <div class="flex flex-wrap gap-4">
                        <button class="bg-primary text-black px-8 py-4 rounded-lg font-bold hover:scale-105 transition-transform flex items-center gap-2">
                            Mua ngay <span class="material-symbols-outlined">arrow_forward</span>
                        </button>
                        <button class="bg-white/10 backdrop-blur-md text-white border border-white/20 px-8 py-4 rounded-lg font-bold hover:bg-white/20 transition-colors">
                            Thông số
                        </button>
                    </div>
                </div>
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
                    <div class="w-8 h-1 bg-primary rounded-full"></div>
                    <div class="w-8 h-1 bg-white/30 rounded-full"></div>
                    <div class="w-8 h-1 bg-white/30 rounded-full"></div>
                </div>
            </div>
        </section>

        <section class="px-4 md:px-10 lg:px-20 mt-20">
            <div class="flex items-center gap-3 mb-8">
                <span class="material-symbols-outlined text-primary">auto_awesome</span>
                <h2 class="text-3xl font-bold">Combo Hoàn Hảo</h2>
                <span class="bg-primary/20 text-black dark:text-primary text-[10px] font-bold px-2 py-1 rounded">AI GỢI Ý</span>
            </div>
            <div class="bg-white dark:bg-white/5 border border-[#f5f3f0] dark:border-white/10 rounded-2xl p-6 lg:p-10 flex flex-col lg:flex-row items-center gap-8">
                <div class="flex-1 grid grid-cols-3 gap-4 w-full">
                    <div class="flex flex-col items-center">
                        <div class="aspect-square w-full rounded-xl bg-gray-100 dark:bg-white/10 bg-cover bg-center mb-4"
                            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuC5kdFa3m9RUyDOq-JtvVLeftcUlBc6RuUdpkwd-5rIXy21cwcMlwnUIkUZaRfoj74ICQrqwsO7DeeuvrhgFF_x8I92BXUHwJMMRUZf-MR8fjOhUaI9Oxm8WU2eguZGf_UlhPXIrY623OqW6I1pyC1LyqeQLCOmaBjJXRvA_tqBqeuWZ9YFkhq0TXuqygePpabB11X7C97enS5EAu0DtT6mle7wJJJRXh6vPTyatcvr5OshfcRZEWITof1ivLP04JrBjdi79dY67SE');">
                        </div>
                        <p class="text-sm font-bold text-center">iPhone 15 Pro</p>
                    </div>
                    <div class="flex items-center justify-center">
                        <span class="material-symbols-outlined text-gray-400">add</span>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-white/5 rounded-xl">
                            <div class="size-16 rounded-lg bg-cover bg-center flex-shrink-0"
                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBZp11AcszU9PiGrp6P8pUbHSD87aNa0yxEh63Ans10gq55eeR9itxGcg47cvcN4vtjs63hWu9YYBGc6Oxeop7t9iHTJDiFMM_GCa4_Q110rUn9BpM4j1vAhw5NgoLKm418eNW_z-481DLyUQpsPTn2M3ZslkLE4Tb6O3Uz9JYIpV_dKGXJ0FXCmgYeAZHCdzTHUNt7EMGBT2oAxyFc3acmNWDmZCi6fRQLvkDRp2ldGXj3lBLigzLX-nhYvJ-jYHfW_reZMfz94ys');">
                            </div>
                            <div>
                                <p class="text-xs font-bold">Ốp lưng MagSafe</p>
                                <p class="text-xs text-gray-500">1.150.000₫</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-white/5 rounded-xl">
                            <div class="size-16 rounded-lg bg-cover bg-center flex-shrink-0"
                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCEw9D9tBP0Y5ICsquYYtjRFOLKYTZipIrzjYOsRJc-WlAviyQnHlKmXJvWIEqykSf0SVlQcxtpmhfm2n_DK5r0XvwzzPlll3IUPSYBrw8coo4VBAIGL8c7hvMTW1AkCHuxipEm0kuT3lvTtdQif9Cj4UB-s2WBlX1H68qOONkg2o8Z6wpZwbPlCZime6_5not2J-5adjtaIzTm-vfX5CrwnGm7574ylneRYB3c9bFIYTz-mF5s3v3b6fKqTvYTy2rz47V8PvJLeLw');">
                            </div>
                            <div>
                                <p class="text-xs font-bold">Củ sạc 20W</p>
                                <p class="text-xs text-gray-500">690.000₫</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:w-80 w-full p-6 bg-primary rounded-xl text-black">
                    <div class="mb-4">
                        <p class="text-sm font-medium opacity-80 uppercase tracking-wider">Tổng giá trị combo</p>
                        <div class="flex items-end gap-2">
                            <h3 class="text-3xl font-black">28.990.000₫</h3>
                            <span class="text-sm line-through opacity-60">32.200.000₫</span>
                        </div>
                    </div>
                    <p class="text-xs mb-6 font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        Tiết kiệm ngay 10% khi mua cả bộ
                    </p>
                    <button class="w-full bg-black text-white py-4 rounded-lg font-bold hover:scale-[1.02] transition-transform flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">shopping_basket</span> Mua cả bộ
                    </button>
                </div>
            </div>
        </section>

        <section class="px-4 md:px-10 lg:px-20 mt-16">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold">Sản phẩm mới</h2>
                <a class="text-primary font-bold flex items-center gap-1 hover:underline" href="{{ route('client.products.index') }}">Xem tất cả
                    <span class="material-symbols-outlined">chevron_right</span></a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                @foreach($newProducts->take(8) as $product)
                    @php
                        $prodImg = $product->thumbnail ?? '';
                        $prodUrl = Str::startsWith($prodImg, ['http://', 'https://']) ? $prodImg : ($prodImg ? asset('storage/' . $prodImg) : 'https://placehold.co/400x400/f8f9fa/1a1a1a?text=BeePhone');
                        
                        // Logic tìm giá chuẩn cho biến thể
                        $finalPrice = $product->price;
                        $finalSalePrice = $product->sale_price;
                        $isVariable = false;

                        if($product->type == 'variable' && $product->variants && $product->variants->count() > 0) {
                            $isVariable = true;
                            $minVariant = $product->variants->sortBy(function($v) {
                                return ($v->sale_price > 0 && $v->sale_price < $v->price) ? $v->sale_price : $v->price;
                            })->first();

                            $finalPrice = $minVariant->price;
                            $finalSalePrice = $minVariant->sale_price;
                        }
                        
                        $hasSale = $finalSalePrice > 0 && $finalSalePrice < $finalPrice;
                        $discountPercent = $hasSale ? round((($finalPrice - $finalSalePrice) / $finalPrice) * 100) : 0;
                        $displayPrice = $hasSale ? $finalSalePrice : $finalPrice;
                    @endphp

                    <div class="bg-white dark:bg-white/5 p-4 rounded-xl border border-transparent hover:border-primary transition-all group flex flex-col">
                        <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}" class="relative rounded-lg overflow-hidden aspect-square bg-gray-100 mb-4 block">
                            <div class="w-full h-full bg-cover bg-center group-hover:scale-110 transition-transform duration-500"
                                style="background-image: url('{{ $prodUrl }}');">
                            </div>
                            <span class="absolute top-2 left-2 bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase shadow-sm">MỚI</span>
                            @if($hasSale)
                                <span class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase shadow-sm">-{{ $discountPercent }}%</span>
                            @endif
                        </a>
                        
                        <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}">
                            <h3 class="font-bold text-lg mb-4 line-clamp-1 hover:text-primary transition-colors" title="{{ $product->name }}">{{ $product->name }}</h3>
                        </a>
                        
                        <div class="flex items-end justify-between mt-auto">
                            <div class="flex flex-col">
                                @if($isVariable) 
                                    <span class="text-[10px] text-gray-400 font-bold leading-none mb-1 uppercase tracking-wider">Giá từ</span> 
                                @endif
                                <span class="text-xl font-bold text-red-500">{{ number_format($displayPrice, 0, ',', '.') }}₫</span>
                            </div>
                            
                            @if($isVariable)
                                <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}" 
                                   class="bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-white w-10 h-10 rounded-lg flex items-center justify-center hover:bg-primary hover:text-black transition-colors shrink-0 shadow-sm" title="Chọn phiên bản">
                                    <span class="material-symbols-outlined">tune</span>
                                </a>
                            @else
                                <button class="btn-add-cart-quick bg-black dark:bg-primary text-white dark:text-black w-10 h-10 rounded-lg flex items-center justify-center hover:scale-105 transition-transform shrink-0 shadow-md" 
                                        data-product-id="{{ $product->id }}" title="Thêm vào giỏ">
                                    <span class="material-symbols-outlined">add_shopping_cart</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>
        </section>

        <section class="px-4 md:px-10 lg:px-20 mt-20">
            <div class="bg-primary/10 border-2 border-primary/20 rounded-2xl p-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-8 mb-10">
                    <div>
                        <h2 class="text-3xl font-bold mb-2">🔥 Ưu đãi cực HOT trong tuần</h2>
                        <p class="text-gray-600 dark:text-gray-300">Săn ngay deal hời từ các sản phẩm yêu thích nhất. Đừng bỏ lỡ!</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-center">
                            <div class="bg-primary text-black font-bold text-xl w-12 h-12 flex items-center justify-center rounded-lg">02</div>
                            <span class="text-[10px] font-bold uppercase mt-1 block">NGÀY</span>
                        </div>
                        <div class="text-xl font-bold">:</div>
                        <div class="text-center">
                            <div class="bg-primary text-black font-bold text-xl w-12 h-12 flex items-center justify-center rounded-lg">14</div>
                            <span class="text-[10px] font-bold uppercase mt-1 block">GIỜ</span>
                        </div>
                        <div class="text-xl font-bold">:</div>
                        <div class="text-center">
                            <div class="bg-primary text-black font-bold text-xl w-12 h-12 flex items-center justify-center rounded-lg">25</div>
                            <span class="text-[10px] font-bold uppercase mt-1 block">PHÚT</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex bg-white dark:bg-white/5 rounded-xl overflow-hidden items-center p-4 gap-6 group">
                        <div class="w-32 h-32 flex-shrink-0 bg-cover bg-center rounded-lg"
                            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAT692iQFuOuZkJYlJztHJpErp5rZ0BAO5OAwsgcs5D-z-3lhnd0LYXDEkicx50cuQUfADjS_dyoTybD2Hh2ukAK4NiTNKfM4XpOHcGt9d_cPGN6VLYFNqHTf2BMPCWvY3Ve5NUgCYrwL48OiodZZsnUbTjSf6cyUUCLgSlIf1lpk9eFNtN3KqKWKFY15iAxlU2AmD-yN6mS_8VIbqM2A4FfShaG8h4KvIyEdZojxa3X35myxGCfMEqIDAevbOnh6rWmMORitqmOSg');">
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-primary text-black text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">BÁN CHẠY</span>
                            </div>
                            <h4 class="font-bold text-lg">Bee Home Hub Pro</h4>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xl font-bold text-primary">3.200.000₫</span>
                                <span class="text-sm text-gray-400 line-through">4.990.000₫</span>
                            </div>
                            <button class="bg-black dark:bg-white dark:text-black text-white text-xs font-bold px-4 py-2 rounded-lg hover:scale-105 transition-transform">Nhận ưu đãi</button>
                        </div>
                    </div>
                    <div class="flex bg-white dark:bg-white/5 rounded-xl overflow-hidden items-center p-4 gap-6 group">
                        <div class="w-32 h-32 flex-shrink-0 bg-cover bg-center rounded-lg"
                            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDd9kLQWgl-v-WkmZynnezI1kg_FlE1X_Yttc7SchEeehjq5l55xhw0RvkqEqIn4eCoESlXE8_mW5_wo36xt2MeHTQlXnnjjJk2SZpDCZTsJ-Ox-AsyQH58dMik9rRarvKR9B2ubX-KuhRSkKhod8D-U8zdWteCZhlZBYhyT50HvQ_nzik2lP6xA9YdU58giY3FjV-PA5XMi-cz1D9vcWlARHCOjv-dIXgMly7Lku_e1ke8MqGWOJuJ1zbRVg8Br7IPjtHMH_W5d6s');">
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-primary text-black text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">SỐ LƯỢNG CÓ HẠN</span>
                            </div>
                            <h4 class="font-bold text-lg">Pro Game Pad X</h4>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xl font-bold text-primary">1.200.000₫</span>
                                <span class="text-sm text-gray-400 line-through">2.190.000₫</span>
                            </div>
                            <button class="bg-black dark:bg-white dark:text-black text-white text-xs font-bold px-4 py-2 rounded-lg hover:scale-105 transition-transform">Nhận ưu đãi</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-20 px-4 md:px-10 lg:px-20 overflow-hidden">
            <div class="flex items-center gap-3 mb-8">
                <span class="material-symbols-outlined text-primary">auto_awesome</span>
                <h2 class="text-3xl font-bold ai-sparkle">Đề xuất thông minh bởi Bee AI</h2>
                <div class="h-[2px] flex-1 bg-gray-200 dark:bg-white/10 ml-4"></div>
            </div>
            <div class="flex gap-6 overflow-x-auto pb-6 snap-x no-scrollbar">
                <div class="min-w-[280px] snap-start bg-white dark:bg-white/5 p-4 rounded-xl border border-transparent hover:border-primary transition-all">
                    <div class="aspect-video bg-cover bg-center rounded-lg mb-4"
                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCu2nXMwUaxcgMFJ3DStTUgvb06MPwKj14rOq1mH0VclphFw_2M5iAvYziUuhH5QPaXNJendjAlFdCtsOwatHMj67LqKzbjz-OfDYey38KYrtb-kesCMAi8AHCDpZ2YDimjLD_38rIqPmoAtrdJJKzx7yTBSgFiAtFBPmSD3mnIXNk-ABUurpj3Fs9RoY5iygmOVMaX7EdXyxk3Cf4SxQ8Sm37r4HhW6-ckcCyFT9R7fqNCk0vx0hejVveNwJE-SZv85a4wLJ-1YNM');">
                    </div>
                    <h4 class="font-bold">Bee Book Air M3</h4>
                    <p class="text-primary font-bold mt-1">32.990.000₫</p>
                    <p class="text-[10px] text-gray-400 mt-2 uppercase font-medium">Dựa trên lịch sử xem: Laptop</p>
                </div>
                <div class="min-w-[280px] snap-start bg-white dark:bg-white/5 p-4 rounded-xl border border-transparent hover:border-primary transition-all">
                    <div class="aspect-video bg-cover bg-center rounded-lg mb-4"
                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCLsRzrbfHVM4SQlyaMcpPwVSNdAlZyIXITEh7s1-yuaJ-zcmhhoPOnGu1K3ZH10QFR0ruM-BP317eoQ2pc-xpPdM9Hgpg-3PydU6iWyBVuv-ymonHvgyW_fAbcDvfhkg-aJ76ZkHxJyOdISSJpL-CNSIXEy0QXeIBusj4EDIJRorMevTJ2ZoqeMB93iW0ZdMFHWvULSEKGwYpGFGU1NsmfUOz10oDX01RfKaBLi956F7jOLkKL_IcPELZa6tQ2C3lfm8ZOrzm6v8o');">
                    </div>
                    <h4 class="font-bold">Lumina Pro Lens Kit</h4>
                    <p class="text-primary font-bold mt-1">13.990.000₫</p>
                    <p class="text-[10px] text-gray-400 mt-2 uppercase font-medium">Có thể bạn quan tâm: Nhiếp ảnh</p>
                </div>
                <div class="min-w-[280px] snap-start bg-white dark:bg-white/5 p-4 rounded-xl border border-transparent hover:border-primary transition-all">
                    <div class="aspect-video bg-cover bg-center rounded-lg mb-4"
                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBZp11AcszU9PiGrp6P8pUbHSD87aNa0yxEh63Ans10gq55eeR9itxGcg47cvcN4vtjs63hWu9YYBGc6Oxeop7t9iHTJDiFMM_GCa4_Q110rUn9BpM4j1vAhw5NgoLKm418eNW_z-481DLyUQpsPTn2M3ZslkLE4Tb6O3Uz9JYIpV_dKGXJ0FXCmgYeAZHCdzTHUNt7EMGBT2oAxyFc3acmNWDmZCi6fRQLvkDRp2ldGXj3lBLigzLX-nhYvJ-jYHfW_reZMfz94ys');">
                    </div>
                    <h4 class="font-bold">Leather Case Series</h4>
                    <p class="text-primary font-bold mt-1">1.150.000₫</p>
                    <p class="text-[10px] text-gray-400 mt-2 uppercase font-medium">Bạn vừa mua iPhone 15 Pro</p>
                </div>
                <div class="min-w-[280px] snap-start bg-white dark:bg-white/5 p-4 rounded-xl border border-transparent hover:border-primary transition-all">
                    <div class="aspect-video bg-cover bg-center rounded-lg mb-4"
                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBbI2RkcODivjNwLY8UdZfEV4UCcW_Wr6lwa0nGObhEPoslpANr5VLcdDHhSZhAgmePGQKmtXRVH2DJ9A-q5uOyxwtsJOXO2NhPVRh5SNoGfoy87Pluv87_IQgk86NcCZKsTJwQshHZxcj3n6PW6hO_QiqT4DguT3WKf92x3P0DgPoS2v7-ygcGooQTUTeuSS7Czk6Noc-_D4BsXe97Lq86uzlN5m3-JTWKsutd44IpjVBFzeu0l9RQm8ojiCMs3FM2x_XlWzOHodc');">
                    </div>
                    <h4 class="font-bold">Studio Beats Pro</h4>
                    <p class="text-primary font-bold mt-1">7.500.000₫</p>
                    <p class="text-[10px] text-gray-400 mt-2 uppercase font-medium">Dựa trên xu hướng</p>
                </div>
                <div class="min-w-[280px] snap-start bg-white dark:bg-white/5 p-4 rounded-xl border border-transparent hover:border-primary transition-all">
                    <div class="aspect-video bg-cover bg-center rounded-lg mb-4"
                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCEw9D9tBP0Y5ICsquYYtjRFOLKYTZipIrzjYOsRJc-WlAviyQnHlKmXJvWIEqykSf0SVlQcxtpmhfm2n_DK5r0XvwzzPlll3IUPSYBrw8coo4VBAIGL8c7hvMTW1AkCHuxipEm0kuT3lvTtdQif9Cj4UB-s2WBlX1H68qOONkg2o8Z6wpZwbPlCZime6_5not2J-5adjtaIzTm-vfX5CrwnGm7574ylneRYB3c9bFIYTz-mF5s3v3b6fKqTvYTy2rz47V8PvJLeLw');">
                    </div>
                    <h4 class="font-bold">Active ANC Buds</h4>
                    <p class="text-primary font-bold mt-1">3.990.000₫</p>
                    <p class="text-[10px] text-gray-400 mt-2 uppercase font-medium">Phụ kiện yêu thích</p>
                </div>
            </div>
        </section>
    </main>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Lấy token bảo mật của Laravel
    const csrfToken = '{{ csrf_token() }}';

    // Bắt sự kiện cho tất cả các nút "Thêm vào giỏ nhanh"
    document.querySelectorAll('.btn-add-cart-quick').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.getAttribute('data-product-id');
            const originalHtml = this.innerHTML;
            
            // Hiệu ứng xoay xoay đang load
            this.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">refresh</span>';
            this.classList.add('pointer-events-none', 'opacity-70');

            // Gửi AJAX lên CartController
            fetch('{{ route("client.cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: '', // Bỏ trống vì đây là SP thường
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                // Phục hồi lại nút
                this.innerHTML = '<span class="material-symbols-outlined text-[20px]">check</span>';
                this.classList.remove('pointer-events-none', 'opacity-70');
                this.classList.replace('bg-black', 'bg-green-500'); // Đổi màu xanh lá báo thành công
                
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.replace('bg-green-500', 'bg-black');
                }, 2000);

                if (data.success) {
                    // Update số lượng giỏ hàng trên Header (Nếu có class này)
                    const cartBadges = document.querySelectorAll('.bg-primary.text-black.rounded-full');
                    cartBadges.forEach(badge => badge.innerText = data.cart_count);
                    
                    alert('Đã thêm sản phẩm vào giỏ hàng!');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                this.innerHTML = originalHtml;
                this.classList.remove('pointer-events-none', 'opacity-70');
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            });
        });
    });
});
</script>
@endsection