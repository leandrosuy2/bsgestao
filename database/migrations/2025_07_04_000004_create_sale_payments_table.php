<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->string('payment_type'); // dinheiro, cartao, pix, etc
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_payments');
    }
};
