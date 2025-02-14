<?php

namespace Tests\Feature;

use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Locale;
use App\Models\User;
use Tests\TestCase;

class TestTranslation extends TestCase
{
    protected $user;
    protected $locale;
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->locale = Locale::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    /** @test */
    public function it_can_create_a_translation() {
        $response = $this->postJson('/api/translations', [
            'locale_id' => $this->locale->id,
            'key' => 'welcome_message',
            'content' => 'Welcome to our platform',
            'tags' => 'web'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'locale_id', 'key', 'content', 'tags']);
    }

    /** @test */
    public function it_can_update_a_translation() {
        $translation = Translation::factory()->create(['locale_id' => $this->locale->id]);

        $response = $this->putJson("/api/translations/{$translation->id}", [
            'content' => 'Updated Content',
            'tags' => 'mobile'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['content' => 'Updated Content']);
    }

    /** @test */
    public function it_can_delete_a_translation() {
        $translation = Translation::factory()->create(['locale_id' => $this->locale->id]);

        $response = $this->deleteJson("/api/translations/{$translation->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }

    /** @test */
    public function it_can_search_translations() {
        Translation::factory()->create([
            'key' => 'test_key',
            'content' => 'Searchable content',
            'locale_id' => $this->locale->id
        ]);

        $response = $this->getJson('/api/translations/search/' . $this->locale->code . '?key=test_key');

        $response->assertStatus(200)
            ->assertJsonFragment(['content' => 'Searchable content']);
    }

    /** @test */
    public function it_can_export_translations_to_json() {
        Translation::factory(10)->create(['locale_id' => $this->locale->id]);

        $response = $this->actingAs($this->user, 'sanctum')->get('/api/translations/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=translations.json');
    }
}
