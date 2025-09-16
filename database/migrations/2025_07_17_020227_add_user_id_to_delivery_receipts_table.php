<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar se a coluna já existe
        if (!Schema::hasColumn('delivery_receipts', 'user_id')) {
            Schema::table('delivery_receipts', function (Blueprint $table) {
                $table->foreignId('user_id')->after('company_id')->nullable()->constrained()->onDelete('cascade');
            });
        }
        
        // Atualizar registros existentes com o primeiro usuário disponível de cada empresa
        DB::statement("
            UPDATE delivery_receipts dr
            SET user_id = (
                SELECT u.id 
                FROM users u 
                WHERE u.company_id = dr.company_id 
                LIMIT 1
            )
            WHERE user_id IS NULL
        ");
        
        // Se a coluna já existir sem constraint, adicionar constraint
        if (!DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'delivery_receipts' 
            AND COLUMN_NAME = 'user_id' 
            AND REFERENCED_TABLE_NAME = 'users'
        ")) {
            Schema::table('delivery_receipts', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
