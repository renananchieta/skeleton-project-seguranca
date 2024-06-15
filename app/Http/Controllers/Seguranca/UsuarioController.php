<?php

namespace App\Http\Controllers\Seguranca;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seguranca\UsuariosRequest;
use App\Http\Requests\Seguranca\UsuariosUpdateRequest;
use App\Http\Resources\Seguranca\UsuariosResource;
use App\Models\Seguranca\SegPerfilDB;
use App\Models\Seguranca\SegPerfilUsuario;
use App\Models\Seguranca\Usuario;
use App\Models\Seguranca\UsuarioDB;
use App\Models\Seguranca\UsuarioRegras;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = (Object)$request->all();
        $data = UsuarioDB::gridUsuarios($params);
        return response(UsuariosResource::collection($data),200);
    }

    public function create()
    {
        $comboPerfil = SegPerfilDB::comboPerfil(Auth::user());
        return response([
                'perfis' => $comboPerfil
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UsuariosRequest $request)
    {
        $data = $request->valid();
        try{
            DB::beginTransaction();
            $usuario = UsuarioRegras::cadastrarUsuario($data);
            DB::commit();
            return response([
                'message' => 'Usuario cadastrado com sucesso.',
                'usuario' => $usuario->nome,
            ], 201);
        } catch(Exception $e) {
            DB::commit();
            return response([
                'erro' => 'Erro ao tentar realizar esta operação.', 
                'mensagem' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $usuario)
    {
        $usuario = Usuario::with('perfis')->findOrFail($usuario);
        return response(new UsuariosResource($usuario), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UsuariosUpdateRequest $request, Usuario $usuario)
    {
        $data = $request->valid();
        try{
            DB::beginTransaction();
            $usuario = UsuarioRegras::atualizarUsuario($data, $usuario); 
            DB::commit();
            return response([
                'message' => 'Usuário alterado com sucesso.', 
                'data' => new UsuariosResource($usuario)
            ], 200);
        } catch(Exception $e) {
            DB::rollBack();
            return response([
                'erro' => 'Erro ao tentar realizar esta operação.', 
                'mensagem' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $usuario)
    {
        try{
            DB::beginTransaction();
            $usuario->delete();
            DB::commit();
            return response('Operação realizada com sucesso', 200);
        } catch(Exception $e) {
            DB::rollBack();
            return response([
                'erro' => 'Erro ao tentar realizar esta operação.', 
                'mensagem' => $e->getMessage()
            ], 500);
        }
    }
}
