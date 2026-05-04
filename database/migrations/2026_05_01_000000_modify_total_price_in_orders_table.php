<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Increase precision to support large totals (e.g., billions)
        DB::statement("ALTER TABLE `orders` MODIFY `total_price` DECIMAL(15,2) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to a smaller precision (adjust if your previous precision was different)
        DB::statement("ALTER TABLE `orders` MODIFY `total_price` DECIMAL(8,2) NOT NULL");
    }
};