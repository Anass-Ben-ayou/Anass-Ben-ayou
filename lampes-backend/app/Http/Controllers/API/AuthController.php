<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCodeMail;
use App\Models\Client;
use App\Models\Panier;
use App\Models\PasswordResetCode;
use App\Services\JwtService;
use App\Support\SanitizesInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Recoit le service JWT utilise pour les sessions de connexion.
    public function __construct(protected JwtService $jwtService) {}

    // Envoie un cookie CSRF pour les formulaires publics et les requetes connectees.
    public function csrfToken()
    {
        $token = Str::random(64);

        return response()->json([
            'success' => true,
            'data' => [
                'csrf_token' => $token,
            ],
        ])->withCookie($this->makeCsrfCookie($token));
    }

    // Inscrit un nouveau client et cree son panier dans une transaction.
    public function register(Request $request)
    {
        $request->merge([
            'email' => SanitizesInput::email($request->input('email')),
            'nom' => SanitizesInput::plain($request->input('nom'), 100),
            'prenom' => SanitizesInput::plain($request->input('prenom'), 100),
            'telephone' => SanitizesInput::plain($request->input('telephone'), 20),
        ]);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:clients,email|max:255',
            'password' => ['required', 'confirmed', 'max:72', Password::min(8)->letters()->numbers()],
            'telephone' => 'required|string|max:20|regex:/^[0-9 +().-]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $client = DB::transaction(function () use ($request) {
                $client = Client::create([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'email' => $request->email,
                    'mot_de_passe' => Hash::make($request->password),
                    'telephone' => $request->telephone,
                    'role' => 'user',
                    'date_inscription' => now(),
                ]);

                Panier::create([
                    'id_client' => $client->id_client,
                    'date_creation' => now(),
                ]);

                return $client;
            });

            return $this->authenticatedResponse($client, 'Inscription reussie', 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription',
            ], 500);
        }
    }

    // Connecte un client et renouvelle son jeton de session.
    public function login(Request $request)
    {
        $request->merge([
            'email' => SanitizesInput::email($request->input('email')),
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|max:72',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $client = Client::where('email', $request->email)->first();

            if (! $client || ! Hash::check($request->password, $client->mot_de_passe)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect',
                ], 401);
            }

            return $this->authenticatedResponse($client, 'Connexion reussie');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la connexion',
            ], 500);
        }
    }

    // Envoie un code de reinitialisation sans reveler si l email existe.
    public function forgotPassword(Request $request)
    {
        $request->merge([
            'email' => SanitizesInput::email($request->input('email')),
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $genericMessage = 'Si cet email existe, un code a ete envoye.';

        try {
            $client = Client::where('email', $request->email)->first();

            if ($client) {
                PasswordResetCode::where('email', $request->email)->delete();

                $code = (string) random_int(100000, 999999);

                PasswordResetCode::create([
                    'email' => $request->email,
                    'code_hash' => Hash::make($code),
                    'expires_at' => now()->addMinutes(10),
                ]);

                Mail::to($request->email)->send(new PasswordResetCodeMail($code));
            }

            return response()->json([
                'success' => true,
                'message' => $genericMessage,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Forgot password email send failed.', [
                'email' => $request->email,
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => true,
                'message' => $genericMessage,
            ]);
        }
    }

    // Verifie que le code de reinitialisation est valide et non expire.
    public function verifyResetCode(Request $request)
    {
        $request->merge([
            'email' => SanitizesInput::email($request->input('email')),
            'code' => SanitizesInput::plain($request->input('code'), 6),
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $resetCode = PasswordResetCode::where('email', $request->email)
            ->latest()
            ->first();

        if (! $resetCode || $resetCode->used_at || $resetCode->expires_at->isPast() || ! Hash::check($request->code, $resetCode->code_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Le code est invalide ou expire.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Code verifie avec succes.',
        ]);
    }

    // Remplace le mot de passe apres validation du code de reinitialisation.
    public function resetPassword(Request $request)
    {
        $request->merge([
            'email' => SanitizesInput::email($request->input('email')),
            'code' => SanitizesInput::plain($request->input('code'), 6),
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'code' => 'required|digits:6',
            'password' => ['required', 'confirmed', 'max:72', Password::min(8)->letters()->numbers()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $client = Client::where('email', $request->email)->first();
        $resetCode = PasswordResetCode::where('email', $request->email)
            ->latest()
            ->first();

        if (
            ! $client ||
            ! $resetCode ||
            $resetCode->used_at ||
            $resetCode->expires_at->isPast() ||
            ! Hash::check($request->code, $resetCode->code_hash)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Le code est invalide ou expire.',
            ], 422);
        }

        try {
            $client->forceFill([
                'mot_de_passe' => Hash::make($request->password),
            ])->save();

            $resetCode->used_at = now();
            $resetCode->save();

            PasswordResetCode::where('email', $request->email)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe reinitialise avec succes.',
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de reinitialiser le mot de passe.',
            ], 500);
        }
    }

    // Supprime le jeton stocke et expire les cookies d authentification.
    public function logout(Request $request)
    {
        try {
            $request->user()->forceFill([
                'api_token' => null,
            ])->save();

            return response()->json([
                'success' => true,
                'message' => 'Deconnexion reussie',
            ])
                ->withCookie($this->expireCookie(config('security.auth_cookie_name', 'access_token')))
                ->withCookie($this->expireCookie(config('security.csrf_cookie_name', 'XSRF-TOKEN')));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la deconnexion',
            ], 500);
        }
    }

    // Retourne le profil du client connecte.
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    // Met a jour les champs modifiables du profil connecte.
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if ($request->has('telephone')) {
            $request->merge([
                'telephone' => SanitizesInput::plain($request->input('telephone'), 20),
            ]);
        }

        foreach (['nom', 'prenom'] as $field) {
            if ($request->has($field)) {
                $request->merge([
                    $field => SanitizesInput::plain($request->input($field), 100),
                ]);
            }
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'telephone' => 'sometimes|string|max:20|regex:/^[0-9 +().-]+$/',
            'password' => ['sometimes', 'confirmed', 'max:72', Password::min(8)->letters()->numbers()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if ($request->has('nom')) {
                $user->nom = trim((string) $request->nom);
            }

            if ($request->has('prenom')) {
                $user->prenom = trim((string) $request->prenom);
            }

            if ($request->has('telephone')) {
                $user->telephone = $request->telephone;
            }

            if ($request->has('password')) {
                $user->mot_de_passe = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profil mis a jour',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise a jour',
            ], 500);
        }
    }

    // Construit la reponse de connexion avec de nouveaux cookies JWT et CSRF.
    protected function authenticatedResponse(Client $client, string $message, int $status = 200)
    {
        $jwtId = Str::random(64);
        $jwt = $this->jwtService->generateForClient($client, $jwtId);
        $csrfToken = Str::random(64);

        $client->forceFill([
            'api_token' => hash('sha256', $jwtId),
        ])->save();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'client' => $client->fresh(),
            ],
        ], $status)
            ->withCookie($this->makeAuthCookie($jwt))
            ->withCookie($this->makeCsrfCookie($csrfToken));
    }

    // Cree le cookie d authentification HTTP-only.
    protected function makeAuthCookie(string $token)
    {
        return cookie(
            config('security.auth_cookie_name', 'access_token'),
            $token,
            config('security.jwt_ttl_minutes', 120),
            '/',
            config('security.cookie_domain'),
            config('security.cookie_secure', false),
            true,
            false,
            config('security.cookie_same_site', 'lax')
        );
    }

    // Cree le cookie CSRF lisible utilise par le frontend.
    protected function makeCsrfCookie(string $token)
    {
        return cookie(
            config('security.csrf_cookie_name', 'XSRF-TOKEN'),
            $token,
            config('security.jwt_ttl_minutes', 120),
            '/',
            config('security.cookie_domain'),
            config('security.cookie_secure', false),
            false,
            false,
            config('security.cookie_same_site', 'lax')
        );
    }

    // Cree un cookie expire avec les memes parametres de securite.
    protected function expireCookie(string $name)
    {
        return cookie(
            $name,
            '',
            -1,
            '/',
            config('security.cookie_domain'),
            config('security.cookie_secure', false),
            $name === config('security.auth_cookie_name', 'access_token'),
            false,
            config('security.cookie_same_site', 'lax')
        );
    }
}
