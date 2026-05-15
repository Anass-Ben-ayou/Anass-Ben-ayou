<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LignePanier;
use App\Models\Panier;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    // Récupérer le panier
    public function getCart(Request $request)
    {
        try {
            $panier = Panier::with(['lignePaniers.produit.categorie'])
                ->where('id_client', $request->user()->id_client)
                ->first();

            if (! $panier || $panier->lignePaniers->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'items' => [],
                        'total' => 0,
                        'total_items' => 0,
                    ],
                ]);
            }

            $items = [];
            $total = 0;
            $totalItems = 0;

            foreach ($panier->lignePaniers as $ligne) {
                $sousTotal = $ligne->quantite * $ligne->produit->prix;
                $total += $sousTotal;
                $totalItems += $ligne->quantite;

                $items[] = [
                    'id_ligne' => $ligne->id_ligne_panier,
                    'produit' => $ligne->produit,
                    'quantite' => $ligne->quantite,
                    'prix_unitaire' => $ligne->produit->prix,
                    'sous_total' => $sousTotal,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $items,
                    'total' => $total,
                    'total_items' => $totalItems,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement du panier',
            ], 500);
        }
    }

    // Ajouter au panier
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_produit' => 'required|exists:produits,id_produit',
            'quantite' => 'required|integer|min:1|max:99',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $produit = Produit::whereKey($request->id_produit)->firstOrFail();

            // Vérifier le stock
            if ($produit->stock < $request->quantite) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuffisant. Il reste {$produit->stock} unité(s).",
                ], 400);
            }

            $panier = Panier::firstOrCreate(
                ['id_client' => $request->user()->id_client],
                ['date_creation' => now()]
            );

            $ligne = LignePanier::where('id_panier', $panier->id_panier)
                ->where('id_produit', $request->id_produit)
                ->first();

            if ($ligne) {
                $nouvelleQuantite = $ligne->quantite + $request->quantite;
                if ($produit->stock < $nouvelleQuantite) {
                    return response()->json([
                        'success' => false,
                        'message' => "Quantité totale trop élevée. Stock disponible: {$produit->stock}",
                    ], 400);
                }
                $ligne->quantite = $nouvelleQuantite;
                $ligne->save();
            } else {
                LignePanier::create([
                    'quantite' => $request->quantite,
                    'id_panier' => $panier->id_panier,
                    'id_produit' => $request->id_produit,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté au panier',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout au panier',
            ], 500);
        }
    }

    // Mettre à jour la quantité
    public function updateQuantity(Request $request, $id_ligne)
    {
        $validator = Validator::make($request->all(), [
            'quantite' => 'required|integer|min:1|max:99',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $ligne = LignePanier::with(['produit', 'panier'])->findOrFail($id_ligne);

            if ($ligne->panier->id_client !== $request->user()->id_client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'etes pas autorise a modifier cette ligne',
                ], 403);
            }

            // Vérifier le stock
            if ($ligne->produit->stock < $request->quantite) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuffisant. Il reste {$ligne->produit->stock} unité(s).",
                ], 400);
            }

            $ligne->quantite = $request->quantite;
            $ligne->save();

            return response()->json([
                'success' => true,
                'message' => 'Quantité mise à jour',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
            ], 500);
        }
    }

    // Supprimer du panier
    public function removeFromCart(Request $request, $id_ligne)
    {
        try {
            $ligne = LignePanier::with('panier')->findOrFail($id_ligne);

            if ($ligne->panier->id_client !== $request->user()->id_client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'etes pas autorise a supprimer cette ligne',
                ], 403);
            }

            $ligne->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produit retiré du panier',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
            ], 500);
        }
    }

    // Vider le panier
    public function clearCart(Request $request)
    {
        try {
            $panier = Panier::where('id_client', $request->user()->id_client)->first();

            if ($panier) {
                LignePanier::where('id_panier', $panier->id_panier)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Panier vidé avec succès',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du vidage du panier',
            ], 500);
        }
    }

    // Compteur du panier
    public function cartCount(Request $request)
    {
        try {
            $panier = Panier::where('id_client', $request->user()->id_client)->first();

            if (! $panier) {
                return response()->json([
                    'success' => true,
                    'data' => ['count' => 0],
                ]);
            }

            $count = LignePanier::where('id_panier', $panier->id_panier)->sum('quantite');

            return response()->json([
                'success' => true,
                'data' => ['count' => $count],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => ['count' => 0],
            ], 500);
        }
    }
}
