<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catalogo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  string  $catalogName
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     */

    private function createResponse($data = null, $message = 'Exito', $success = true, $numCode = 0)
    {
        return response()->json([
            'NUMCODE' => $numCode,
            'STRMESSAGE' => $message,
            'RESPONSE' => $data,
            'SUCCESS' => $success,
        ]);
    }

    public function index($catalogName, Request $request)
    {
        try {
            $cacheKey = "catalog_{$catalogName}_{$request->input('search', '')}";

            if ($catalogName == 'pvDocumentosPredefinidos') {
                $cacheKey .= "_{$request->input('modulo', '')}";
            }

            $catalogs = Cache::remember($cacheKey, 60, function () use ($catalogName, $request) {
                $model = new Catalogo;
                $model->setTable($catalogName);
                $query = $model->newQuery();

                if ($request->has('search')) {
                    $query->where('Nombre', 'LIKE', "%{$request->input('search')}%");
                }

                $query->where('deleted', 0)->orderBy('FechaCreacion', 'desc');

                return $query->get();
            });

            return $this->createResponse($catalogs, 'Datos obtenidos con éxito');
        } catch (\Exception $e) {
            return $this->createResponse(null, $e->getMessage(), false, 500);
        }
    }

    /**
     * Get model instance for a catalog.
     *
     * @param  string $catalogName
     * @return \App\Models\Catalogo
     */
    protected function getModelForCatalog($catalogName)
    {
        Log::info("Nombre de la tabla del catálogo controlador: " . $catalogName);
        $model = new Catalogo;
        $model->setCustomTable($catalogName); // Asegúrate de que esto está correctamente implementado
        return $model;
    }

    /**
     * Show the specified resource.
     *
     * @param  string  $catalogName
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($catalogName, $id)
    {
        $model = $this->getModelForCatalog($catalogName);
        $item = $model::findOrFail($id);

        return response()->json($item);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  string  $catalogName
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $catalogName)
    {
        try {
            $model = new Catalogo;
            $model->setTable($catalogName);
            $newRecord = $model->create($request->all());

            return $this->createResponse($newRecord, 'Registro creado con éxito');
        } catch (\Exception $e) {
            return $this->createResponse(null, $e->getMessage(), false, 500);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  string  $catalogName
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($catalogName, Request $request, $id)
    {
        try {
            $model = new Catalogo;
            $model->setTable($catalogName);
            $query = $model->newQuery();

            $query->where('id', $id)->update($request->all());

            return $this->createResponse(null, 'Registro actualizado con éxito');
        } catch (\Exception $e) {
            return $this->createResponse(null, $e->getMessage(), false, 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $catalogName
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($catalogName, $id)
    {
        try {
            $model = new Catalogo;
            $model->setTable($catalogName);
            $query = $model->newQuery();

            $affected = $query->where('id', $id)->update(['deleted' => 1]);

            if ($affected) {
                return $this->createResponse(null, 'Registro marcado como eliminado con éxito');
            } else {
                return $this->createResponse(null, 'El registro no pudo ser eliminado', false);
            }
        } catch (\Exception $e) {
            return $this->createResponse(null, $e->getMessage(), false, 500);
        }
    }

    /**
     * Get model instance for a catalog.
     *
     * @param  string $catalogName
     * @return \App\Models\Catalogo
     */
}
