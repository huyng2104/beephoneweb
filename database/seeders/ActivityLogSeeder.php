<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Lấy danh sách User ID để làm người thực hiện hành động
        $userIds = User::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->warn('Nên có dữ liệu bảng users trước khi seed activity_logs!');
            return;
        }

        $logs = [];
        $models = ['App\Models\User', 'App\Models\Product', 'App\Models\Post'];
        $actions = ['create', 'update', 'delete', 'login', 'change_password'];

        for ($i = 0; $i < 100; $i++) {
            $action = $faker->randomElement($actions);
            $modelType = $faker->randomElement($models);

            $logs[] = [
                'user_id'    => $faker->randomElement($userIds),
                'action'     => $action,
                'model_type' => in_array($action, ['login', 'logout']) ? null : $modelType,
                'model_id'   => in_array($action, ['login', 'logout']) ? null : $faker->numberBetween(1, 50),
                'description' => $this->generateDescription($action, $faker),
                'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('activity_logs')->insert($logs);
    }

    /**
     * Hàm phụ trợ tạo mô tả ngẫu nhiên cho log
     */
    private function generateDescription($action, $faker)
    {
        return match ($action) {
            'create'          => "Đã tạo mới một bản ghi nội dung.",
            'update'          => "Đã cập nhật thông tin tại trường " . $faker->word,
            'delete'          => "Đã xóa bản ghi vĩnh viễn khỏi hệ thống.",
            'login'           => "Đăng nhập thành công từ thiết bị " . $faker->linuxPlatformToken,
            'change_password' => "Đã thay đổi mật khẩu bảo mật.",
            default           => "Hành động hệ thống không xác định.",
        };
    }
}
