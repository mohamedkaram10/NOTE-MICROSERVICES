<?php

namespace App\Repositories;

use App\Models\Note;
use App\Interfaces\CrudInterface;
use Illuminate\Contracts\Pagination\Paginator;

class NoteRepository implements CrudInterface
{

    /**
     * Get All Notes.
     *
     * @return collections Array of Note Collection
     */
    public function getAll(): Paginator
    {
        return Note::orderBy('id', 'desc')
            ->paginate(10);
    }

    /**
     * Get Paginated Note Data.
     *
     * @return collections Array of Note Collection
     */
    public function getPaginatedData($perPage): Paginator
    {
        $perPage = isset($perPage) ? intval($perPage) : 12;

        return Note::orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create New Note.
     *
     * @return object Note Object
     */
    public function create(array $data)
    {
        return Note::create($data);
    }

    /**
     * Get Note Detail By ID.
     *
     * @return void
     */
    public function getByID($id)
    {
        return Note::find($id);
    }

    /**
     * Update Note By ID.
     *
     * @return object Updated Note Object
     */
    public function update(int $id, array $data)
    {
        $note = Note::findOrFail($id);

        // If everything is OK, then update.
        $note->update($data);

        // Finally return the updated Note.
        return $note;
    }

    /**
     * Delete Note.
     *
     * @return bool true if deleted otherwise false
     */
    public function delete(int $id): bool
    {
        $note = Note::findOrFail($id);

        $note->delete($note);

        return true;
    }
}
