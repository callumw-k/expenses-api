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
        //
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
            $table->string('total_amount')->nullable()->change();
            $table->renameColumn('file_path', 'image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('description')->change();
            $table->string('total_amount')->change();
            $table->renameColumn('image_path', 'file_path');
        });
    }
};
