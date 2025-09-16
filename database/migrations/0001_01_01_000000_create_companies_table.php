<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('trial_start')->nullable();
            $table->timestamp('trial_end')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('paid_until')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('address_number')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('responsible_email')->nullable();
            $table->string('responsible_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
