<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];
    public function permissions(){
        return $this->belongsToMany(Role::class,'role_permissions');
    }
    public function getNameRoleAttribute(){
        if($this->name == 'admin'){
            return 'Quản trị viên';
        }
        if($this->name == 'staff'){
            return 'Nhân viên';
        }
        return 'Người dùng';
    }
}
