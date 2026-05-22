<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductSummaryResource;
use App\Models\Categorie;
use App\Support\SanitizesInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // Returns the full category list.
    public function index()
    {
        try {
            $categories = Categorie::withCount(['produits' => fn ($query) => $query->where('status', 'active')])
                ->whereHas('produits', fn ($query) => $query->where('status', 'active'))
                ->orderBy('nom')
                ->get()
                ->map(fn ($categorie) => (new CategoryResource($categorie))->resolve());

            return response()->json([
                'success' => true,
                'data' => $categories,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des categories',
            ], 500);
        }
    }

    // Returns the categories with the most products.
    public function populaires()
    {
        try {
            $categories = Categorie::withCount(['produits' => fn ($query) => $query->where('status', 'active')])
                ->whereHas('produits', fn ($query) => $query->where('status', 'active'))
                ->orderByDesc('produits_count')
                ->get()
                ->map(fn ($categorie) => (new CategoryResource($categorie))->resolve());

            return response()->json([
                'success' => true,
                'data' => $categories,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des categories populaires',
            ], 500);
        }
    }

    // Returns one category with a small product preview.
    public function show($id)
    {
        try {
            $categorie = $this->findCategory($id);
            $categorie->load(['produits' => fn ($query) => $query->with('categorie')->where('status', 'active')->latest()->limit(8)]);
            $categorie->loadCount(['produits' => fn ($query) => $query->where('status', 'active')]);

            return response()->json([
                'success' => true,
                'data' => (new CategoryResource($categorie))->resolve(),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Categorie non trouvee',
            ], 404);
        }
    }

    // Returns paginated products for one category.
    public function products(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'sometimes|integer|min:1|max:50',
            'search' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $categorie = $this->findCategory($id);
            $query = $categorie->produits()->with('categorie')->where('status', 'active');

            if ($request->filled('search')) {
                $search = $request->string('search')->toString();
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('nom', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            }

            $produits = $query->latest()->paginate((int) $request->get('per_page', 12));
            $produits->getCollection()->transform(fn ($produit) => (new ProductSummaryResource($produit))->resolve());

            return response()->json([
                'success' => true,
                'data' => $produits,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des produits de la categorie',
            ], 500);
        }
    }

    // Creates a new category.
    public function store(Request $request)
    {
        $request->merge($this->normalizedInput($request));

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100|unique:categories,nom',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $categorie = Categorie::create([
                'nom' => $request->nom,
                'slug' => $request->slug ?: str($request->nom)->slug()->toString(),
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categorie creee avec succes',
                'data' => (new CategoryResource($categorie))->resolve(),
            ], 201);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la creation',
            ], 500);
        }
    }

    // Updates an existing category.
    public function update(Request $request, $id)
    {
        $request->merge($this->normalizedInput($request));

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:100|unique:categories,nom,'.$id.',id_categorie',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,'.$id.',id_categorie',
            'description' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $categorie = Categorie::findOrFail($id);
            $categorie->update($request->only(['nom', 'slug', 'description']));

            return response()->json([
                'success' => true,
                'message' => 'Categorie mise a jour',
                'data' => (new CategoryResource($categorie))->resolve(),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise a jour',
            ], 500);
        }
    }

    // Deletes a category when it has no linked products.
    public function destroy($id)
    {
        try {
            $categorie = Categorie::findOrFail($id);

            if ($categorie->produits()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer : des produits sont associes a cette categorie',
                ], 400);
            }

            $categorie->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categorie supprimee avec succes',
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
            ], 500);
        }
    }

    // Accepts either a numeric id or a slug.
    protected function findCategory(string $id): Categorie
    {
        return is_numeric($id)
            ? Categorie::findOrFail((int) $id)
            : Categorie::where('slug', $id)->firstOrFail();
    }

    protected function normalizedInput(Request $request): array
    {
        $data = [];

        if ($request->has('nom')) {
            $data['nom'] = SanitizesInput::plain($request->input('nom'), 100);
        }

        if ($request->has('slug')) {
            $data['slug'] = SanitizesInput::plain($request->input('slug'), 255);
        }

        if ($request->has('description')) {
            $data['description'] = SanitizesInput::paragraph($request->input('description'), 2000);
        }

        return $data;
    }
}
