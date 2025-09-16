<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('initial_amount', 12, 2);
            $table->decimal('final_amount', 12, 2)->nullable();
            $table->enum('status', ['open', 'closed']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_registers');
    }
};
