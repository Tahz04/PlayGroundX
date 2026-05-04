<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Arena;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class SeedArenaImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'arenas:seed-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động thêm logo và ảnh mô tả mẫu cho tất cả các sân hiện có trong DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu tải ảnh mẫu...');

        $localImages = [];
        $files = Storage::disk('public')->files('arenas');
        
        foreach ($files as $file) {
            if (str_contains($file, 'football_pitch_') && str_ends_with($file, '.png')) {
                $localImages[] = $file;
            }
        }

        if (count($localImages) < 3) {
            $this->error('Không tìm thấy đủ ảnh football_pitch_ trong thư mục storage/app/public/arenas. Cần ít nhất 3 ảnh, hiện có: ' . count($localImages));
            return;
        }

        $this->info('Đã chuẩn bị xong ' . count($localImages) . ' ảnh sân bóng cực nét. Tiến hành cập nhật Database...');

        $arenas = Arena::all();
        $count = 0;

        foreach ($arenas as $arena) {
            $randomKeys = array_rand($localImages, 3);
            
            $arena->image = $localImages[$randomKeys[0]];
            $arena->image_1 = $localImages[$randomKeys[1]];
            $arena->image_2 = $localImages[$randomKeys[2]];
            
            $arena->save();
            $count++;
        }

        $this->info("Thành công! Đã cập nhật ảnh đẹp cho $count sân bóng.");
    }
}
