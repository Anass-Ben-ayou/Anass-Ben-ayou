<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Panier;
use App\Support\SanitizesInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index()
    {
        try {
            $users = Client::query()
                ->orderByDesc('id_client')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $users,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des utilisateurs',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->merge($this->normalizedInput($request));

        $validator = Validator::make($request->all(), [
            'name' => 'required_without_all:nom,prenom|string|max:200',
            'nom' => 'required_without:name|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'email' => 'required|email|unique:clients,email|max:255',
            'telephone' => 'nullable|string|max:20|regex:/^[0-9 +().-]+$/',
            'role' => 'required|in:admin,user',
            'password' => ['required', 'confirmed', 'max:72', Password::min(8)->letters()->numbers()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $client = Client::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone ?: '',
                'role' => $request->role,
                'mot_de_passe' => Hash::make($request->password),
                'date_inscription' => now(),
            ]);

            Panier::create([
                'id_client' => $client->id_client,
                'date_creation' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur cree avec succes',
                'data' => $client->fresh(),
            ], 201);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la creation de l utilisateur',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->merge($this->normalizedInput($request));

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:200',
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'email' => 'sometimes|email|max:255|unique:clients,email,'.$id.',id_client',
            'telephone' => 'nullable|string|max:20|regex:/^[0-9 +().-]+$/',
            'role' => 'sometimes|in:admin,user',
            'password' => ['sometimes', 'confirmed', 'max:72', Password::min(8)->letters()->numbers()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $client = Client::findOrFail($id);

            if ((int) $request->user()->id_client === (int) $client->id_client && $request->input('role') === 'user') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas retirer votre propre role administrateur.',
                ], 422);
            }

            if ($request->has('nom')) {
                $client->nom = $request->nom;
            }

            if ($request->has('prenom')) {
                $client->prenom = $request->prenom;
            }

            if ($request->has('email')) {
                $client->email = $request->email;
            }

            if ($request->exists('telephone')) {
                $client->telephone = $request->telephone ?: '';
            }

            if ($request->has('role')) {
                $client->role = $request->role;
            }

            if ($request->filled('password')) {
                $client->mot_de_passe = Hash::make($request->password);
            }

            $client->save();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis a jour avec succes',
                'data' => $client->fresh(),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise a jour de l utilisateur',
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $client = Client::findOrFail($id);

            if ((int) $request->user()->id_client === (int) $client->id_client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas supprimer votre propre compte administrateur.',
                ], 422);
            }

            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur supprime avec succes',
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l utilisateur',
            ], 500);
        }
    }

    protected function normalizedInput(Request $request): array
    {
        $data = [];

        if ($request->has('name')) {
            $data['name'] = SanitizesInput::plain($request->input('name'), 200);
            [$prenom, $nom] = $this->splitName($data['name']);

            if (! $request->has('nom')) {
                $data['nom'] = $nom;
            }

            if (! $request->has('prenom')) {
                $data['prenom'] = $prenom;
            }
        }

        if ($request->has('nom')) {
            $data['nom'] = SanitizesInput::plain($request->input('nom'), 100);
        }

        if ($request->has('prenom')) {
            $data['prenom'] = SanitizesInput::plain($request->input('prenom'), 100);
        }

        if ($request->has('email')) {
            $data['email'] = SanitizesInput::email($request->input('email'));
        }

        if ($request->has('telephone')) {
            $data['telephone'] = SanitizesInput::plain($request->input('telephone'), 20);
        }

        if ($request->has('role')) {
            $data['role'] = str_replace('client', 'user', trim((string) $request->input('role')));
        }

        return $data;
    }

    protected function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];

        if (count($parts) <= 1) {
            return ['', SanitizesInput::plain($parts[0] ?? '', 100)];
        }

        $prenom = array_shift($parts);

        return [
            SanitizesInput::plain($prenom, 100),
            SanitizesInput::plain(implode(' ', $parts), 100),
        ];
    }
}
