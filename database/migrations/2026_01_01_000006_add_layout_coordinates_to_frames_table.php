<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * layout_coordinates menyimpan format JSON:
     * {
     *   "ref_w": 600,      // lebar canvas referensi saat admin mendefinisikan slot (pixel)
     *   "ref_h": 800,      // tinggi canvas referensi (pixel)
     *   "slots": [
     *     {"x": 50, "y": 100, "w": 200, "h": 300},   // pixel absolut
     *     {"x": 300, "y": 150, "w": 200, "h": 200}
     *   ]
     * }
     *
     * Ketika rendering, koordinat discaling sesuai ukuran container tampilan.
     */
    public function up(): void
    {
        Schema::table('frames', function (Blueprint $table) {
            $table->json('layout_coordinates')->nullable()->after('slot_layout')
                ->comment('JSON: {ref_w, ref_h, slots:[{x,y,w,h}]} dalam pixel absolut');
        });
    }

    public function down(): void
    {
        Schema::table('frames', function (Blueprint $table) {
            $table->dropColumn('layout_coordinates');
        });
    }
};
