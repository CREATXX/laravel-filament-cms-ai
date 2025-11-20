<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Veritabanı seed işlemi başlıyor...');
        $this->command->newLine();

        $this->call([
            SettingSeeder::class,
            PageSeeder::class,
            PostSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Tüm seed işlemleri tamamlandı!');
        $this->command->info('📊 Oluşturulan veriler:');
        $this->command->info('   - Ayarlar: Genel site ayarları');
        $this->command->info('   - Sayfalar: 3 demo sayfa');
        $this->command->info('   - Blog Yazıları: 4 demo yazı (3 yayında, 1 taslak)');
    }
}
