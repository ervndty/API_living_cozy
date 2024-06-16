<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SebastianBergmann\CodeCoverage\Report\Html\CustomCssFile;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('status');
            $table->string('price');
            $table->string('item_name');
            $table->string('customer_first_name');
            $table->string('customer_email');
            $table->string('checkout_link');
            $table->timestamps();
            // $table->increments('payment_id');
            // $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('order_id');
            // $table->decimal('amount', 10, 2);
            // $table->string('payment_method', 50);
            // $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            // $table->timestamps();

            // $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            // $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};