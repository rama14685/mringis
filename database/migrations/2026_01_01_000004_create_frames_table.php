<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frames', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('overlay_image')->nullable(); // path relatif dari storage/public
            $table->string('thumbnail')->nullable();
            $table->integer('slot_count')->default(4); // 1, 2, 4, 6
            $table->json('slot_layout')->nullable(); // [{x, y, w, h}] dalam persentase
            $table->integer('price')->default(10000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frames');
    }
};
