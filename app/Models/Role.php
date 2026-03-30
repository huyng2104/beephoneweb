<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
class Role extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name',
        'description',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('role')
            ->logOnlyDirty();
    }
    public function permissions(){
        return $this->belongsToMany(Permission::class,'role_permissions');
    }
    public function users(){
        return $this->hasMany(User::class);
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
