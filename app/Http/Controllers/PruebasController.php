<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Contacto;
use App\Models\Servicios;

class PruebasController extends Controller
{
    //
    public function index(){
    	$titulo = "animales";
    	$animales = ['Perro','Gato','Tigre','popo'];

    	return view('pruebas.index',array(
    		'titulo' => $titulo,
    		'animales' => $animales
    	));
    }
    
    public function testOrm(){
        $contactos = Contacto::all();
        //var_dump($contactos);
        foreach($contactos as $contact){
            echo $contact->id."<br>";
            echo $contact->nombre."<br>";
            echo $contact->correo."<br>";
            echo $contact->telefono."<br>";
            echo $contact->servicio_id."<br>";
            echo $contact->descripcion."<br>";
            echo $contact->fecha_cotizacion."<br>";
            echo "id servicio: ".$contact->servicios->id."<br>";
            echo $contact->servicios->nombre_servicio."<br>";
            echo "<hr>";
        }
        die();
    }
    
    
    
}
