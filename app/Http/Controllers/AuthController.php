<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'email' => 'required|email',
                    'password' => 'required|string',
                ],
                [
                    'required' => 'O campo :attribute é obrigatório',
                    'string' => 'O campo :attribute precisa ser uma string',
                    'email' => 'O campo :attribute precisa ser um email válido'
                ]
            );

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'E-mail e/ou senha inválido.'
                ], Response::HTTP_UNAUTHORIZED);
            };

            $token = $user->createToken($user->email)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login feito com sucesso',
                'data' => $user,
                'token' => $token
            ], Response::HTTP_OK);

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
}
