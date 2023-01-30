<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicios extends Model
{
    use HasFactory;
    protected $table = 'servicios';
    //relacion uno a muhcos
    /****************si sirve***************************/
    public function contacto(){
        return $this->hasMany('App\Models\Contacto');
    }
    /*************************************************/
    /*public function contacto(){
    return $this->belongsTo('App\Models\Contacto','servicio_id');
    }*/
}
