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
        Schema::table('campaigns', function (Blueprint $table) {
            //
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
        Schema::table('campaigns', function (Blueprint $table) {
            //
            $table->dropColumn('email_campaign_id');
            $table->dropColumn('email_template_id');
            $table->dropColumn('sms_template_id');
            $table->dropColumn('wp_template_id');
        });
    }
};
