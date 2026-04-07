<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // 1. Nhóm Cấu hình Chung (General Settings)
            [
                'key' => 'site_name',
                'value' => json_encode('BeePhone - Luxury Store'),
                'type' => 'text'
            ],
            [
                'key' => 'site_logo',
                'value' => json_encode('/images/logo.png'),
                'type' => 'image'
            ],
            [
                'key' => 'site_favicon',
                'value' => json_encode('/images/favicon.ico'),
                'type' => 'image'
            ],
            [
                'key' => 'hotline',
                'value' => json_encode('0123456789'),
                'type' => 'text'
            ],
            [
                'key' => 'email_contact',
                'value' => json_encode('contact@beephone.com'),
                'type' => 'text'
            ],
            [
                'key' => 'address',
                'value' => json_encode('123 Street, City'),
                'type' => 'text'
            ],
            [
                'key'   => 'maintenance_mode',
                'value' => json_encode([
                    'is_enabled' => false,
                    'end_at'     => '2026-04-06 08:00:00',
                    'message'    => 'BeePhone đang nâng cấp hệ thống định kỳ.'
                ]),
                'type'  => 'json'
            ],

            // 2. Nhóm Header
            [
                'key' => 'header_top_bar',
                'value' => json_encode('Khuyến mãi ngập tràn - Săn sale tức thì'),
                'type' => 'text'
            ],
            [
                'key' => 'header_menu',
                'value' => json_encode([
                    [
                        "id" => 171234567,
                        "title" => "Trang chủ",
                        "url" => "/"
                    ],
                    [
                        "id" => 171234588,
                        "title" => "Điện thoại",
                        "url" => "/dien-thoai",
                        "children" => [
                            ["id" => 171234599, "title" => "iPhone", "url" => "/iphone"],
                            ["id" => 171234600, "title" => "Samsung", "url" => "/samsung"]
                        ]
                    ],
                    [
                        "id" => 171234611,
                        "title" => "Tin tức",
                        "url" => "/tin-tuc"
                    ]
                ]),
                'type' => 'json'
            ],
            [
                'key' => 'header_sticky',
                'value' => json_encode('1'),
                'type' => 'boolean'
            ],

            // 3. Nhóm Footer (Kiểu WP)
            [
                'key' => 'footer_widgets',
                'value' => json_encode([
                    [
                        'title' => 'Về chúng tôi',
                        'links' => [
                            ['label' => 'Giới thiệu', 'url' => '/about'],
                            ['label' => 'Tuyển dụng', 'url' => '/careers'],
                        ]
                    ],
                    [
                        'title' => 'Chính sách',
                        'links' => [
                            ['label' => 'Chính sách bảo hành', 'url' => '/warranty'],
                            ['label' => 'Chính sách đổi trả', 'url' => '/returns'],
                        ]
                    ],
                    [
                        'title' => 'Mạng xã hội',
                        'links' => [
                            ['label' => 'Facebook', 'url' => 'https://facebook.com'],
                            ['label' => 'Youtube', 'url' => 'https://youtube.com'],
                        ]
                    ]
                ]),
                'type' => 'json'
            ],
            [
                'key' => 'footer_copyright',
                'value' => json_encode('© 2026 BeePhone. All rights reserved.'),
                'type' => 'text'
            ],
            [
                'key' => 'footer_payment_methods',
                'value' => json_encode([
                    '/images/payments/vnpay.png',
                    '/images/payments/visa.png',
                    '/images/payments/momo.png'
                ]),
                'type' => 'json'
            ],
        ];

        foreach ($settings as $setting) {
            \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type'  => $setting['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
