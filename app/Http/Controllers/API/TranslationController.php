<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TranslationRequest;
use App\Http\Requests\TranslationStoreRequest;
use App\Http\Requests\TranslationUpdateRequest;
use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    protected $translation;
    protected $locale;

    public function __construct(Translation $translation, Locale $locale) {
        $this->middleware('auth:sanctum');
        $this->translation = $translation;
        $this->locale = $locale;
    }
    /**
     * Display a result according to locale and of the resource.
     */
    public function search($locale, TranslationRequest $request) {
        $result = Translation::whereHas('locale', function ($query) use ($locale) {
            $query->where('code', $locale);
        })
        ->where('key', 'like', "%{$request->input('key', '')}%")
        ->limit(1000)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $result
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TranslationStoreRequest $request) {
        return response()->json(
            Translation::create($request->all()), 201
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TranslationUpdateRequest $request, Translation $translation) {

        $translation->update($request->all());
        return response()->json($translation, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Translation $translation)
    {
        $translation->delete();
        return response()->json(null, 204);
    }

    /**
     * Export Translation To JSON
     */

    public function exportToJson() {
        return response()->streamDownload(function () {
            echo json_encode(Translation::all()->toArray());
        }, 'translations.json');
    }
}
