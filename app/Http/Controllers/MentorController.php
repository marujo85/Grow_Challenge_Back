<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class MentorController extends Controller
{

    public function index()
    {
        $mentors = Mentor::all();
        return $mentors;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                [
                    'name' => 'required|string',
                    'email' => 'required|email|unique:users,email',
                    'cpf' => 'required|cpf|unique:users,cpf',
                ],
                [
                    'required' => 'O campo :attribute é obrigatório',
                    'string' => 'O campo :attribute precisa ser uma string',
                    'email' => 'O campo :attribute precisa ser um email válido',
                    'cpf' => 'O campo :attribute precisa ser um cpf válido',
                    'unique' => 'O campo :attribute já está em uso'
                ]
                ]);

                $mentor = Mentor::create($request->all());

                return response()->json([
                    'success' => true,
                    'message' => 'Mentor cadastrado com sucesso',
                    'data' => $mentor
                ], Response::HTTP_CREATED);

        } catch (ValidationException $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno, tente novamente',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        try {
            $mentor = Mentor::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Mentor encontrado',
                'data' => $mentor
                ], Response::HTTP_OK);
        } catch(ModelNotFoundException $error){
            return response()->json([
                'success' => false,
                'message' => 'ID do mentor, não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, Int $id)
    {
        try{
            $request->validate([
                [
                    'name' => 'required|string',
                    'email' => 'required|email'
                ],
                [
                    'required' => 'O campo :attribute é obrigatório',
                    'string' => 'O campo :attribute precisa ser uma string',
                    'email' => 'O campo :attribute precisa ser um email válido'
                ]
                ]);

            $mentor = Mentor::findOrFail($id);

            $mentor->fill([
                'name' => $request->name,
                'email' => $request->email
            ]);

            $mentor->save();

            return response()->json(['success' => true,
            'message' => "Mentor editado!",
            'data' => $mentor
        ], Response::HTTP_OK);

        }catch(\Exception $error){
            return response()->json(['success' => false, 'msg' => $error->getMessage()], 400);
         }
    }

    public function destroy(Int $id)
    {
        try{
            $mentor = Mentor::findOrFail($id);

            $mentor->delete();

            return response()->json(['success' => true, 'mgs' => "Mentor excluido com sucesso!"]);

        } catch(ModelNotFoundException $error){
            return response()->json([
                'success' => false,
                'message' => 'ID do mentor, não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
