<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Libro;

use Illuminate\Http\UploadedFile;

use Carbon\Carbon;

class LibroController extends Controller{

    public function index()
    {
        $datosLibro = Libro::all();

        return response()->json($datosLibro);
    }

    public function guardar(Request $request){
        $datosLibro = new Libro;
        if($request->hasFile('imagen')){
            $nombreArchivoOriginal = $request->file('imagen')->getClientOriginalName();
            $nuevoNombre = Carbon::now()->timestamp."_".$nombreArchivoOriginal;
            $carpetaDestino = './upload/';
            $request->file('imagen')->move($carpetaDestino, $nuevoNombre);
            $datosLibro->titulo = $request->titulo;
            $datosLibro->imagen = ltrim($carpetaDestino, '.').$nuevoNombre; // ltrim le quita el "." a './upload/' para inicar que ya se esta en la carpeta, ademas, le cncatenamos el nombre para guardar la ruta de la imagen 
            $datosLibro->save();
        }
        return response()->json($nuevoNombre);
    }
    public function ver($id){
        $datosLibro = new Libro;
        $datosEncontrados = $datosLibro->find($id);
        return response()->json($datosEncontrados);
    }

    public function eliminar($id){
        $datosLibro = Libro::find($id);
        if($datosLibro){
            $rutaArchivo = base_path('public').$datosLibro->imagen; // base_path busca la carpeta public y eso lo concatenamos con la url de la imagen
            if(file_exists($rutaArchivo)){// Si existe el archivo
                unlink($rutaArchivo);// Borra el archivo
                echo "Registro {$id} Borrado";
            }
            $datosLibro->delete();
        }else{
            return response()->json("No se encuentran los datos del id:{$id}");
        }
    }

    public function actualizar(Request $request, $id){
        $datosLibro = Libro::find($id);

        if($request->hasFile('imagen')){

            if($datosLibro){
                $rutaArchivo = base_path('public').$datosLibro->imagen;
                if(file_exists($rutaArchivo)){
                    unlink($rutaArchivo);
                }
                $datosLibro->delete();
            }

            $nombreArchivoOriginal = $request->file('imagen')->getClientOriginalName();
            $nuevoNombre = Carbon::now()->timestamp."_".$nombreArchivoOriginal;
            $carpetaDestino = './upload/';
            $request->file('imagen')->move($carpetaDestino, $nuevoNombre);
            $datosLibro->imagen = ltrim($carpetaDestino, '.').$nuevoNombre; // ltrim le quita el "." a './upload/' para inicar que ya se esta en la carpeta, ademas, le cncatenamos el nombre para guardar la ruta de la imagen 
            $datosLibro->save();
        }

        if($request->input('titulo')){
            $datosLibro->titulo = $request->input('titulo');
        }

        $datosLibro->save();

        return response()->json($datosLibro);
    }
}