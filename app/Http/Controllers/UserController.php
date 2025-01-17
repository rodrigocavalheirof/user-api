<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Criar usuário
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    // Listar todos os usuários
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    // Atualizar usuário
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $id,
            'password' => 'string|min:8',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'email', 'password']));

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json($user);
    }

    // Deletar usuário
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }
}

use App\Http\Controllers\UserController;

Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);
Route::put('users/{id}', [UserController::class, 'update']);
Route::delete('users/{id}', [UserController::class, 'destroy']);

?>