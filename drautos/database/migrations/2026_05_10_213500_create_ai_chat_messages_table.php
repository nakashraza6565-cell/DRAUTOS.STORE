<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiChatMessagesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ai_chat_messages')) {
            Schema::create('ai_chat_messages', function (Blueprint $row) {
                $row->id();
                $row->unsignedBigInteger('user_id');
                $row->string('role'); // 'user' or 'assistant'
                $row->text('content');
                $row->json('tool_calls')->nullable();
                $row->json('tool_results')->nullable();
                $row->timestamps();
                
                $row->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ai_chat_messages');
    }
}
