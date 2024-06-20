<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ABTest;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ab_tests', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->unique();
            $table->enum('status', [ABTest::STATUS_NOT_STARTED, ABTest::STATUS_RUNNING, ABTest::STATUS_STOPPED])->default(ABTest::STATUS_NOT_STARTED);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ab_tests');
    }
};
