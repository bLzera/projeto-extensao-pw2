<?php

use App\Models\Producer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producers', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('farm_name');
        });

        // Backfill: gera slug para os produtores já existentes.
        Producer::whereNull('slug')->get()->each->save();
    }

    public function down(): void
    {
        Schema::table('producers', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
