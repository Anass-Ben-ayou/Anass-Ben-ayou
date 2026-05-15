<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function monthExpression(): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%m', date_commande)"
            : 'MONTH(date_commande)';
    }

    private function yearExpression(): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y', date_commande)"
            : 'YEAR(date_commande)';
    }

    // Statistiques globales
    public function stats()
    {
        try {
            $stats = [
                'total_clients' => Client::count(),
                'total_commandes' => Commande::count(),
                'total_produits' => Produit::count(),
                'total_categories' => Categorie::count(),
                'chiffre_affaires' => Commande::where('statut', 'livree')->sum('total'),
                'commandes_en_attente' => Commande::where('statut', 'en_attente')->count(),
                'commandes_payees' => Commande::where('statut', 'payee')->count(),
                'commandes_expediees' => Commande::where('statut', 'expediee')->count(),
                'commandes_livrees' => Commande::where('statut', 'livree')->count(),
                'produits_en_stock' => Produit::sum('stock'),
                'produits_rupture' => Produit::where('stock', 0)->count(),
                'produits_stock_faible' => Produit::where('stock', '<', 5)->where('stock', '>', 0)->count(),
                'valeur_stock' => Produit::sum(DB::raw('prix * stock')),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des statistiques',
            ], 500);
        }
    }

    // Dernières commandes
    public function recentOrders()
    {
        try {
            $commandes = Commande::with('client')
                ->latest()
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $commandes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des commandes',
            ], 500);
        }
    }

    // Meilleurs produits
    public function topProducts()
    {
        try {
            $topProducts = Produit::withCount('ligneCommandes')
                ->withSum('ligneCommandes', 'quantite')
                ->orderBy('ligne_commandes_sum_quantite', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topProducts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des top produits',
            ], 500);
        }
    }

    // Ventes par mois
    public function monthlySales()
    {
        try {
            $yearExpression = $this->yearExpression();
            $monthExpression = $this->monthExpression();

            $sales = Commande::where('statut', 'livree')
                ->select(
                    DB::raw("{$yearExpression} as annee"),
                    DB::raw("{$monthExpression} as mois"),
                    DB::raw('SUM(total) as total_ventes'),
                    DB::raw('COUNT(*) as nombre_commandes')
                )
                ->groupBy('annee', 'mois')
                ->orderBy('annee', 'desc')
                ->orderBy('mois', 'desc')
                ->limit(12)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sales,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des ventes mensuelles',
            ], 500);
        }
    }
}
