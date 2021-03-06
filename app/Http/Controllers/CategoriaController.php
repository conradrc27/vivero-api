<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use Response;
use Exception;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Categoria::where('delete',1)->get();
        return response()->json(['status'=>'ok', 'message'=> 'Registro de categorías', 'data'=>$categories], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->input('nombre') || !$request->input('descripcion'))
        {
            return response()->json(['status'=>'error','code'=>402, 'message'=> 'Faltan datos necesarios para el proceso de registro.'],402);
        }
        $categoryDuplicate=Categoria::where('nombre', $request->input('nombre'))->orderBy('id_categoria', 'DESC')->first();
        
        if($categoryDuplicate==null || $categoryDuplicate!=null && $categoryDuplicate->delete==2){            
            try{
                $nuevaCategoria=Categoria::create($request->all());
            }catch(Exception $e){
                return response()->json(['status'=>'error','error'=>$e,'code'=>400,'message'=>'Ah ocurrido un error al intentar agregar la categoría'],400);
            }
        }
        else{
            return response()->json(['status'=>'error','code'=>409,'message'=>'Ya existe una categoría con el mismo nombre.'],409);
        }
        
        return $response = Response::make(json_encode(['status'=>'success','code'=>201,'message'=> 'La categoría ha sido agregada con éxito.','data'=> $nuevaCategoria]), 201);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_categoria)
    {
        if(!$id_categoria){
            return response()->json(['status'=>'error','code'=>422, 'message'=>'Faltan valores para completar el proceso.'],422);
        }
        
        $categoria=Categoria::find($id_categoria);
		if (!$categoria){
			return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra registrado el categoría'])],404);
		}
		return response()->json(['status'=>'ok','data'=>$categoria],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_categoria)
    {
        $categoria= Categoria::find($id_categoria);
        if(!$categoria)
        {
            return response()->json(['errors'=> array(['code'=>404, 'message'=> 'No se encuentra la categoria'])]);
        }

        // Listado de campos recibidos teóricamente.
		$nombre=$request->input('nombre');
		$descripcion=$request->input('descripcion');
		$imagen=$request->input('imagen');
        $estado=$request->input('estado');
        // Necesitamos detectar si estamos recibiendo una petición PUT o PATCH.
		// El método de la petición se sabe a través de $request->method();
		if ($request->method() === 'PATCH')
		{
			// Creamos una bandera para controlar si se ha modificado algún dato en el método PATCH.
			$bandera = false;
            // Actualización parcial de campos.
			if ($nombre)
			{
				$categoria->nombre = $nombre;
				$bandera=true;
			}

			if ($descripcion)
			{
				$categoria->descripcion = $descripcion;
				$bandera=true;
			}

			if ($imagen)
			{
				$categoria->imagen = $imagen;
				$bandera=true;
			}
            if ($estado)
			{
				$categoria->estado = $estado;
				$bandera=true;
			}

			if ($bandera)
			{
				// Almacenamos en la base de datos el registro.
				$categoria->save();
				return response()->json(['status'=>'success','message'=>'La categoría se modificó con éxito.','data'=>$categoria], 200);
			}
            else
			{
				// Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
				// Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
				return response()->json(['code'=>304,'message'=>'No se ha modificado ningún dato de la categoría.'],304);
			}
		}
        
        // Si el método es PUT y tendremos que actualizar todos los datos.
		if (!$nombre || !$descripcion || !$imagen )
		{
			// Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
			return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan valores para completar el proceso.'])],422);
		}

        $categoria->nombre = $nombre;
		$categoria->descripcion = $descripcion;
		$categoria->imagen = $imagen;
        $categoria->estado = $estado;

		// Almacenamos en la base de datos el registro.
		$categoria->save();
		return response()->json(['status'=>'success','message'=> 'La categoría se modificó con éxito.','data'=>$categoria], 200);
    }


    /**
     * delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id_categoria
     * @return \Illuminate\Http\Response
     */
    public function delete($id_categoria)
    {
        $categoria= Categoria::find($id_categoria);
        if(!$categoria)
        {
            return response()->json(['status'=>'error', 'code'=>404, 'message'=> 'No se encuentra la categoria.'],404);
        }

        if($categoria->estado == 2)
        {
            $categoria->estado = 1;
            $categoria->save();
            return Response::make(json_encode(['status'=>'success','code'=>200,'message'=>'La categoría fue dada de alta con éxito.','data'=>$categoria]), 200);
        }
        else{
            $categoria->estado = 2;
        $categoria->save();
        return Response::make(json_encode(['status'=>'success','code'=>200,'message'=>'La categoría fue dada de baja con éxito.','data'=>$categoria]), 200);
        }
    }
    
    
    /**
     * delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id_categoria
     * @return \Illuminate\Http\Response
     */
    public function deleteCategory($id_categoria)
    {
        $categoria= Categoria::find($id_categoria);
        if(!$categoria)
        {
            return response()->json(['status'=>'error', 'code'=>404, 'message'=> 'No se encuentra la categoria.'],404);
        }
        $categoria->estado = 2;
        $categoria->delete = 2;
        $categoria->save();
        return Response::make(json_encode(['status'=>'success','code'=>200,'message'=>'La categoría fue eliminada con éxito.','data'=>$categoria]), 200);
        
        // else{
        //     $categoria->estado = 1;
        // $categoria->save();
        // return Response::make(json_encode(['status'=>'success','code'=>200,'message'=>'','data'=>$categoria]), 200);
        // }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $categoria = Categoria::find($id);
            $categoria->delete();
            return Response::make(json_encode(['status'=>'success','code'=>200,'message'=>'La categoría fue eliminada con éxito.','data'=>$categoria]), 200);   
        } catch (Exception $th) {
            return Response::make(json_encode(['status'=>'error','code'=>422,'message'=>'Ocurrió un error al intentar eliminar la categoría.', 'errord'=> $th]), 422);
        }        
    }
}