<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ab_test_variants', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('targeting_ratio')->default(1);
            $table->foreignId('ab_test_id')->constrained('ab_tests')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ab_test_variants');
    }
};
