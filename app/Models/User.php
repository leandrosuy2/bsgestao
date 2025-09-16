<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /**
     * Relacionamento com integração de pagamento Sicredi
     */
    public function paymentIntegration()
    {
        return $this->hasOne(UserPaymentIntegration::class);
    }
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relacionamento com papéis
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Verificar se o usuário é admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar se o usuário tem um papel específico
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->exists();
        }

        return $this->roles()->where('id', $role->id)->exists();
    }

    /**
     * Verificar se o usuário tem uma permissão específica
     */
    public function hasPermission($permission)
    {
        // Admin tem todas as permissões
        if ($this->isAdmin()) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                if (is_string($permission)) {
                    $query->where('slug', $permission);
                } else {
                    $query->where('id', $permission->id);
                }
            })
            ->exists();
    }
    
    /**
     * Buscar funcionário associado por email
     * Nota: Não há relacionamento direto entre User e Employee via FK
     */
    public function getEmployeeByEmail()
    {
        return Employee::where('company_id', $this->company_id)
                      ->where('email', $this->email)
                      ->where('active', true)
                      ->first();
    }
    
    // public function employee()
    // {
    //     return $this->hasOne(Employee::class);
    // }
    /**
     * Verificar se o usuário tem permissão em um módulo
     */
    public function hasModulePermission($module)
    {
        // Admin tem todas as permissões
        if ($this->isAdmin()) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($module) {
                $query->where('module', $module);
            })
            ->exists();
    }

    /**
     * Obter todas as permissões do usuário
     */
    public function getAllPermissions()
    {
        // Admin tem todas as permissões
        if ($this->isAdmin()) {
            return Permission::all();
        }

        return Permission::whereHas('roles', function ($query) {
            $query->whereHas('users', function ($subQuery) {
                $subQuery->where('user_id', $this->id);
            });
        })->get();
    }
}
