<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TranslationRequest;
use App\Http\Requests\TranslationStoreRequest;
use App\Http\Requests\TranslationUpdateRequest;
use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Http\Request;


/**
 * @OA\Info(
 *      title="Translation Management API",
 *      version="1.0.0",
 *      description="API for managing translations across multiple locales"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="BearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * )
 */
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
     * @OA\Get(
     *      path="/api/translations/search/{locale}",
     *      operationId="searchTranslations",
     *      tags={"Translations"},
     *      summary="Search translations by locale and key",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="locale",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="key",
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Translations found",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Translation")
     *          )
     *      )
     * )
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

     /**
     * @OA\Post(
     *      path="/api/translations",
     *      operationId="storeTranslation",
     *      tags={"Translations"},
     *      summary="Create a new translation",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"locale_id", "key", "content"},
     *              @OA\Property(property="locale_id", type="integer", example=1),
     *              @OA\Property(property="key", type="string", example="welcome_message"),
     *              @OA\Property(property="content", type="string", example="Welcome to our platform"),
     *              @OA\Property(property="tags", type="string", example="web")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Translation created successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Translation")
     *      )
     * )
     */
    public function store(TranslationStoreRequest $request) {
        return response()->json(
            Translation::create($request->all()), 201
        );
    }

    /**
     * @OA\Put(
     *      path="/api/translations/{id}",
     *      operationId="updateTranslation",
     *      tags={"Translations"},
     *      summary="Update an existing translation",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="content", type="string"),
     *              @OA\Property(property="tags", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Translation updated successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Translation")
     *      )
     * )
     */
    public function update(TranslationUpdateRequest $request, Translation $translation) {

        $translation->update($request->all());
        return response()->json($translation, 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/translations/{id}",
     *      operationId="deleteTranslation",
     *      tags={"Translations"},
     *      summary="Delete a translation",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Translation deleted successfully"
     *      )
     * )
     */
    public function destroy(Translation $translation)
    {
        $translation->delete();
        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *      path="/api/translations/export",
     *      operationId="exportTranslations",
     *      tags={"Translations"},
     *      summary="Export translations as JSON",
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Translations exported successfully",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Translation")
     *          )
     *      )
     * )
     */

    public function exportToJson() {
        return response()->streamDownload(function () {
            echo json_encode(Translation::all()->toArray());
        }, 'translations.json');
    }
}
