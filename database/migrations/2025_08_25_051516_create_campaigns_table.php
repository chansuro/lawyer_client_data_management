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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('sms');
            $table->string('email');
            $table->string('whatsapp');
            $table->date('sent_on')->nullable();
            $table->timestamps();
            $table->date('sent_on_whatsapp')->nullable();
            $table->date('sent_on_email')->nullable();
            $table->string('email_campaign_id')->nullable();
            $table->integer('email_template_id')->nullable();
            $table->integer('sms_template_id')->nullable();
            $table->integer('wp_template_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
