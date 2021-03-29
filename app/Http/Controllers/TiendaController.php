<?php

namespace App\Http\Controllers;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TiendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Visualiza todas las tiendas registradas, incluyendo un mensaje.
        $tiendas = Tienda::all();
        return response()->json([
            "message" => 'La consulta fue realizada con éxito',
            "data" => $tiendas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Valida que todos los campos requeridos esten llenos. 
        $request->validate([
            'nombre' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'email' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'zoom' => 'required',
            'linkimagen' => 'required|image|max:4096'
        ]);

        // Pasa la imagen del espacio temporal donde se guarda normalmente a la ruta indicada
        // Ademas se concatena la hora y fecha en la que se sube la imagen
        // Y limpiar el nombre de espacion y guiones para no sobreescribir archivos.
        $imagen = $request -> file('linkimagen');
        $nombre = $imagen -> getClientOriginalName();
        $nombre = str_replace('-','_', $nombre);
        $hora = now()->format('Y-m-d-H-i-s__');
        $nombre = $hora.$nombre;
        $nombre = str_replace(' ','', $nombre);

        Storage::disk('tiendas')->put($nombre,  \File::get($imagen));

        // Se crea una nueva variable con los datos de la Tienda y se guarda el nombre de la imagen que
        // se utilizara posteriormente para buscar la imagen cuando se necesite visualizar en una vista.
        $tienda = $request->all();
        $tienda['linkimagen'] = $nombre;

        //Crea la nueva tienda en la BD con los campos previamente validados y envia un mensaje. 
        $tiendanueva = Tienda::create($tienda);
        return response()->json([
            "message" => 'La nueva tienda fue registrada con éxito',
            "data" => $tiendanueva
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tienda  $tienda
     * @return \Illuminate\Http\Response
     */
    public function show(Tienda $tienda)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tienda  $tienda
     * @return \Illuminate\Http\Response
     */
    public function edit(Tienda $tienda)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tienda  $tienda
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tienda $tienda)
    {
        // Valida que se haya colocado el id de la tienda que se requiere modificar
        $request->validate([
            'id' => 'required',
        ]);

        // Busca que efectivamente exista la tienda seleccionada y guarda los campos minimos
        // solicitados en una variable.
        $tiendaact = Tienda::findOrFail($request->id);
        $tiendaact->nombre = $request->nombre;
        $tiendaact->telefono = $request->telefono;
        $tiendaact->email = $request->email;

        // Actualiza los datos de la tienda y envia un mensaje exitoso. 
        $tiendaact->save();
        return response()->json([
            "message" => 'Los datos de la tienda fueron actualizados con éxito',
            "data" => $tiendaact
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tienda  $tienda
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // Valida que se haya colocado el id de la tienda que se requiere eliminar
        $request->validate([
            'id' => 'required',
        ]);
        
        // Se busca si el id de la tienda suministrado existe, sino lanza un error. 
        // Se elimina la tienda de la base de datos y se coloca un mensaje exitoso. 
        $tiendaeli = Tienda::findOrFail($request->id);
        Tienda::destroy($tiendaeli->id);
        return response()->json([
            "message" => 'Los datos de la tienda fueron eliminados con éxito',
        ]);
    }
}


