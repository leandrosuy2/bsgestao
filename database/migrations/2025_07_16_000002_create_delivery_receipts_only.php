<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Verificar se a tabela delivery_receipts existe
        if (!Schema::hasTable('delivery_receipts')) {
            Schema::create('delivery_receipts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('supplier_name');
                $table->string('supplier_cnpj', 18);
                $table->string('supplier_contact')->nullable();
                $table->date('delivery_date');
                $table->enum('status', ['pending', 'in_progress', 'finalized'])->default('pending');
                $table->integer('total_items')->default(0);
                $table->integer('checked_items')->default(0);
                $table->integer('progress_percentage')->default(0);
                $table->text('notes')->nullable();
                $table->foreignId('finalized_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('finalized_at')->nullable();
                $table->timestamps();
                
                $table->index(['company_id', 'status']);
                $table->index('delivery_date');
            });
        }

        // Verificar se a tabela delivery_receipt_items existe
        if (!Schema::hasTable('delivery_receipt_items')) {
            Schema::create('delivery_receipt_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('delivery_receipt_id')->constrained()->onDelete('cascade');
                $table->string('product_name');
                $table->string('product_code', 100)->nullable();
                $table->decimal('expected_quantity', 10, 2);
                $table->decimal('received_quantity', 10, 2)->default(0);
                $table->boolean('checked')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index('delivery_receipt_id');
                $table->index('checked');
            });
        }

        // Inserir permissões apenas se não existirem
        $permissions = [
            ['name' => 'Ver romaneios', 'slug' => 'delivery_receipts.view', 'description' => 'Permite visualizar lista de romaneios'],
            ['name' => 'Gerenciar romaneios', 'slug' => 'delivery_receipts.manage', 'description' => 'Permite criar, editar e excluir romaneios'],
            ['name' => 'Ver clientes', 'slug' => 'customers.view', 'description' => 'Permite visualizar lista de clientes'],
            ['name' => 'Gerenciar clientes', 'slug' => 'customers.manage', 'description' => 'Permite criar, editar e excluir clientes'],
            ['name' => 'Ver fornecedores', 'slug' => 'suppliers.view', 'description' => 'Permite visualizar lista de fornecedores'],
            ['name' => 'Gerenciar fornecedores', 'slug' => 'suppliers.manage', 'description' => 'Permite criar, editar e excluir fornecedores'],
        ];

        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')->where('slug', $permission['slug'])->exists();
            if (!$exists) {
                DB::table('permissions')->insert($permission);
            }
        }

        // Atribuir permissões para todas as roles apenas se não existirem
        $roles = ['user', 'estoquista', 'manager', 'admin'];
        $permissionSlugs = [
            'delivery_receipts.view', 'delivery_receipts.manage',
            'customers.view', 'customers.manage',
            'suppliers.view', 'suppliers.manage'
        ];

        foreach ($roles as $roleSlug) {
            $role = DB::table('roles')->where('slug', $roleSlug)->first();
            if ($role) {
                foreach ($permissionSlugs as $permissionSlug) {
                    $permission = DB::table('permissions')->where('slug', $permissionSlug)->first();
                    if ($permission) {
                        $exists = DB::table('role_permission')
                            ->where('role_id', $role->id)
                            ->where('permission_id', $permission->id)
                            ->exists();
                            
                        if (!$exists) {
                            DB::table('role_permission')->insert([
                                'role_id' => $role->id,
                                'permission_id' => $permission->id
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('delivery_receipt_items');
        Schema::dropIfExists('delivery_receipts');
        
        // Remover permissões
        $permissionSlugs = [
            'delivery_receipts.view', 'delivery_receipts.manage',
            'customers.view', 'customers.manage',
            'suppliers.view', 'suppliers.manage'
        ];
        
        DB::table('permissions')->whereIn('slug', $permissionSlugs)->delete();
    }
};
