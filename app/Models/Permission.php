<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'module',
        'description',
    ];

    /**
     * Relacionamento com papéis
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Escopo para permissões de um módulo específico
     */
    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Escopo para permissões por slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}
