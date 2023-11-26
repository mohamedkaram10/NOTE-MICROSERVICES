<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_notes_list()
    {
        $note1 = Note::factory()->create();
        $note2 = Note::factory()->create();
        $response = $this->getJson('/api/notes');

        $response->assertJsonFragment([
            'title' => $note1->title,
            'content' => $note1->content
        ]);
        $response->assertJsonCount(2, 'data');
    }

    public function testStoreNoteSuccessful()
    {
        $note = [
            'title' => 'note title',
            'content' => 'note desc',
        ];

        $response = $this->postJson('/api/notes', $note);
        $response->assertStatus(201);
        $response->assertJson($note);

    }

    public function test_note_invalid_store_returns_error()
    {
        $note = [
            'title' => '',
            'content' => 'note desc',
        ];
        $response = $this->postJson('/api/notes', $note);

        $response->assertStatus(422);
        $response->assertJson($note);
    }

    public function testNoteUpdateSuccessful()
    {
        $noteData = [
            'title' => 'note title',
            'content' => 'note desc',
        ];
        $note = Note::create($noteData);

        $response = $this->putJson('/api/notes/'.$note->id, [
            'title' => 'note title update',
            'content' => 'note desc update',
        ]);
        $response->assertOk();
        $response->assertJsonMissing($noteData);
    }

    public function testNoteDeleteRestrictedByAuth()
    {
        $note = Note::factory()->create();
        $response = $this->deleteJson('/api/notes/'.$note->id);

        $response->assertUnauthorized();
    }
}
