<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->boolean('is_textbook')->default(false)->after('is_repository');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->string('borrower_nis', 50)->nullable()->after('borrower_name')->index();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('borrower_nis');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('is_textbook');
        });
    }
};
