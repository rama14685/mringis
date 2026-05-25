<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photobox_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('token', 10)->unique(); // 5 karakter token sesi
            $table->enum('status', ['active', 'used', 'expired'])->default('active');
            $table->foreignId('frame_id')->nullable()->constrained('frames')->nullOnDelete();
            $table->string('result_image')->nullable(); // path kolase akhir
            $table->json('slot_images')->nullable(); // array path foto per slot
            $table->timestamp('printed_at')->nullable();
            $table->integer('price')->default(10000);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photobox_sessions');
    }
};
