<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->decimal('total', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('final_total', 12, 2);
            $table->enum('status', ['completed', 'cancelled']);
            $table->timestamp('sold_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
