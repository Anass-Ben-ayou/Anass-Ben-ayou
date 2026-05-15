<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\Commande;
use App\Support\SanitizesInput;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->merge([
            'commentaire' => SanitizesInput::paragraph($request->input('commentaire'), 1500),
        ]);

        $validator = Validator::make($request->all(), [
            'id_produit' => 'required|exists:produits,id_produit',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string|min:10|max:1500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $hasPurchasedProduct = Commande::query()
                ->where('id_client', $request->user()->id_client)
                ->whereIn('statut', ['livree', 'payee'])
                ->whereHas('ligneCommandes', function ($query) use ($request) {
                    $query->where('id_produit', $request->id_produit);
                })
                ->exists();

            if (! $hasPurchasedProduct) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez acheter ce produit avant de laisser un avis',
                ], 403);
            }

            $existe = Avis::where('id_client', $request->user()->id_client)
                ->where('id_produit', $request->id_produit)
                ->exists();

            if ($existe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez deja laisse un avis pour ce produit',
                ], 400);
            }

            $avis = Avis::create([
                'note' => $request->note,
                'commentaire' => $request->commentaire,
                'date_avis' => now(),
                'id_client' => $request->user()->id_client,
                'id_produit' => $request->id_produit,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avis ajoute avec succes',
                'data' => $avis->load('client'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de l\'avis',
            ], 500);
        }
    }

    public function index($id_produit)
    {
        $validator = Validator::make(['id_produit' => $id_produit], [
            'id_produit' => 'required|integer|exists:produits,id_produit',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $avis = Avis::with('client')
                ->where('id_produit', $id_produit)
                ->latest()
                ->paginate(10);

            $noteMoyenne = Avis::where('id_produit', $id_produit)->avg('note') ?? 0;
            $totalAvis = Avis::where('id_produit', $id_produit)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'avis' => $avis,
                    'note_moyenne' => round($noteMoyenne, 1),
                    'total_avis' => $totalAvis,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des avis',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        if ($request->has('commentaire')) {
            $request->merge([
                'commentaire' => SanitizesInput::paragraph($request->input('commentaire'), 1500),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'note' => 'sometimes|integer|min:1|max:5',
            'commentaire' => 'sometimes|string|min:10|max:1500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $avis = Avis::findOrFail($id);

            if ($avis->id_client !== $request->user()->id_client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'etes pas autorise a modifier cet avis',
                ], 403);
            }

            if ($request->has('note')) {
                $avis->note = $request->note;
            }

            if ($request->has('commentaire')) {
                $avis->commentaire = $request->commentaire;
            }

            $avis->save();

            return response()->json([
                'success' => true,
                'message' => 'Avis mis a jour avec succes',
                'data' => $avis,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Avis non trouve',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise a jour',
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $avis = Avis::findOrFail($id);

            if ($avis->id_client !== $request->user()->id_client && ! $request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'etes pas autorise a supprimer cet avis',
                ], 403);
            }

            $avis->delete();

            return response()->json([
                'success' => true,
                'message' => 'Avis supprime avec succes',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Avis non trouve',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
            ], 500);
        }
    }
}
