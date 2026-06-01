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
        Schema::table('group_chats', function (Blueprint $table) {
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            // In case a message only has a voice note, message can be nullable
            $table->text('message')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_chats', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_type']);
        });
    }
};
