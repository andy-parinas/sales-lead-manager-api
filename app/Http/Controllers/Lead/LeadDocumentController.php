<?php

namespace App\Http\Controllers\Lead;

use App\Document;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCollection;
use App\Lead;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class LeadDocumentController extends ApiController
{

    private $documentRepository;

    public function __construct(DocumentRepositoryInterface $documentRepository)
    {
        $this->middleware('auth:sanctum');
        $this->documentRepository = $documentRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Lead $lead)
    {
        $documents = $this->documentRepository->getAllByLeadId($this->getRequestParams(), $lead->id);

        return $this->showApiCollection(new DocumentCollection($documents));

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Lead $lead)
    {
        //Gate::authorize('non-staff-users');

        $data = $this->validate($request, [
                'file' => 'required|file',
                'title' => 'required',
                'type' => 'required',
                'description' => 'sometimes'
            ]
        );

        $filename = time() . "_" . preg_replace("/\s+/", "_", strtolower($data['title']));

        $path = $request->file->storeAs('files', $filename);
//        $path = $request->file->store('files');

        $data['path'] = $path;

        $document = $lead->documents()->create($data);

        return $this->showOne($document, Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param Lead $lead
     * @param Document $document
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(Lead $lead, Document $document)
    {

        return response()->download(storage_path("app/" . $document->path), $document->title, ['Content-type' => $document->type]);


    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead, Document $document)
    {
        Gate::authorize('non-staff-users');

        Storage::delete(storage_path("app/" . $document->path));

        $document->delete();

        return $this->showOne($document);

    }
}
