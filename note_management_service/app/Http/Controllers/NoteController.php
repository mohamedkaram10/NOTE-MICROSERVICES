<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Jobs\NoteCreated;
use App\Jobs\NoteDeleted;
use App\Jobs\NoteUpdated;
use App\Models\Note;
use App\Repositories\NoteRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NoteController extends Controller
{
    /**
     * Response trait to handle return responses.
     */
    use ResponseTrait;

    /**
     * Note Repository class.
     *
     * @var Note
     */
    public $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $note = $this->noteRepository->getAll();

            return $this->responseSuccess(NoteResource::collection($note), 'Note List Fetch Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function indexAll(Request $request): JsonResponse
    {
        try {
            $note = $this->noteRepository->getPaginatedData($request->perPage);

            return $this->responseSuccess(NoteResource::collection($note), 'Note List Fetched Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateNoteRequest $request): JsonResponse
    {
        try {
            $note = $this->noteRepository->create($request->all());

            NoteCreated::dispatch($note);

            return $this->responseSuccess(
            new NoteResource($note), 'New Note Created Successfully !', 201);
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $note = $this->noteRepository->getByID($id);
            if (is_null($note)) {
                return $this->responseError(null, 'Note Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseSuccess(new NoteResource($note), 'Note Details Fetch Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateNoteRequest $request, $id): JsonResponse
    {
        try {
            $note = $this->noteRepository->update($id, $request->all());
            if (is_null($note)) {
                return $this->responseError(null, 'Note Not Found', Response::HTTP_NOT_FOUND);
            }

            NoteUpdated::dispatch($note);


            return $this->responseSuccess(new NoteResource($note), 'Note Updated Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $note = $this->noteRepository->getByID($id);
            if (empty($note)) {
                return $this->responseError(null, 'Note Not Found', Response::HTTP_NOT_FOUND);
            }


            $deleted = $this->noteRepository->delete($id);
            if (!$deleted) {
                return $this->responseError(null, 'Failed to delete the Note.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            NoteDeleted::dispatch($deleted);


            return $this->responseSuccess($note, 'Note Deleted Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
