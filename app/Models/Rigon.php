<?php

namespace App\Models;

use App\Models\Directorate as ModelsDirectorate;

use Illuminate\Database\Eloquent\Model;

class Rigon extends Model
{
    protected $table='rigons';
    protected $fillable = ['id','rigon_name','directorate_id'];

    public  $timestamps = false;
    public function agent()
    {
        return $this->hasMany(Agent::class, 'rigons_id','id');
    }
    public function directorate()
    {
        return $this->belongsTo(ModelsDirectorate::class,'directorate_id');
    }
    public function observer()
    {
        return $this->hasMany(Observer::class, 'rigons_id','id');
    }
    public function citizen()
    {
        return $this->hasMany(Citizen::class, 'rigons_id','id');
    }
    public function gaz_Logs()
    {
        return $this->hasMany(gaz_Logs::class, 'rigons_id','id');
    }
    

}
