<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add a new column
            $table->boolean('is_deleted')->default(false);

            // Rename the existing column
            $table->renameColumn('google_id', 'googleAuthId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the newly added column
            $table->dropColumn('is_deleted');

            // Rename the column back to its original name
            $table->renameColumn('googleAuthId', 'google_id');
        });
    }
};
