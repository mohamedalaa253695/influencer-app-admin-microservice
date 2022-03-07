<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function permissions()
    {
        // dd($this->belongsToMany(Permission::class, 'role_permission'));
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }
}
