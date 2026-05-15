<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Paiement;

class AdminOrderController extends Controller
{
    public function orders()
    {
        $orders = Commande::with(['client', 'paiement', 'livraison'])
            ->latest('date_commande')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function payments()
    {
        $payments = Paiement::with(['commande.client'])
            ->latest('date_paiement')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }
}
