<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com permissões
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Relacionamento com usuários
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    /**
     * Verificar se o papel tem uma permissão específica
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->where('slug', $permission)->exists();
        }

        return $this->permissions()->where('id', $permission->id)->exists();
    }

    /**
     * Verificar se o papel tem permissão em um módulo
     */
    public function hasModulePermission($module)
    {
        return $this->permissions()->where('module', $module)->exists();
    }

    /**
     * Escopo para papéis ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
