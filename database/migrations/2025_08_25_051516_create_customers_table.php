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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('ref_no');
            $table->string('card_no');
            $table->string('aan_no');
            $table->string('account_no');
            $table->string('amount');
            $table->date('date_filing');
            $table->string('reason');
            $table->string('address');
            $table->date('notice_date');
            $table->string('email');
            $table->unsignedBigInteger('campaign_id')->index('customers_campaign_id_foreign');
            $table->timestamps();
            $table->string('phone')->nullable();
            $table->string('message_id')->nullable();
            $table->string('email_status', 45)->nullable()->default('Pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
