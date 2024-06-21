<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Models\Facade\FirebirdDB;
use App\Models\Firebird;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    public function grid(Request $request)
    {
        $params = (Object)$request->all();
        try {
            DB::beginTransaction();
            $catalogo = FirebirdDB::grid($params);
            DB::commit();
            return response($catalogo);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    public function consulta(Request $request)
    {
        $params = (Object)$request->all();
        try {
            DB::beginTransaction();
            $catalogo = FirebirdDB::consultaExtensa($params);
            DB::commit();
            return response($catalogo);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }
}
