<?php

namespace Betta\Foundation\Http;

use Betta\Models\Document;
use Illuminate\Http\Request;
use App\Http\Traits\HandlesUploads;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

abstract class ResourceDocumentController extends Controller
{
    use HandlesUploads;

    /**
     * Inject implementation
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $parent;

    /**
     * Let's Abstract into using the same domain for resource and views
     *
     * @var string
     */
    protected $resource = '';

    /**
     * Variable name for the parent
     *
     * @var string
     */
    protected $parentName = 'parent';

    /**
     * Fetch these relations at all times
     *
     * @var array
     */
    protected $relations = [
        'createdBy', 'contexts'
    ];

    /**
     * Display a listing of the resource.
     *
     * @param  int $parent
     * @return Illuminate\Http\Response
     */
    public function index($parent)
    {
        # Resolve parent
        $parent = $this->parent($parent);
        # Eager-load data
        $documents = $parent->documents->load($this->relations);
        # Pick the View
        $view = "{$this->resource}.index";
        # Render
        return view($view)->with(compact('documents'))->with($this->parentName, $parent);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int $parent
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function create($parent, Document $document)
    {
        # resolve parent model
        $parent = $this->parent($parent);
        # Pick the View
        $view = "{$this->resource}.create";
        # Render
        return view($view)->with(compact('parent','document'))->with($this->parentName, $parent);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  int $parent
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $parent)
    {
        # Create New Document instance
        $document = $this->document->newInstance( $request->input() );
        # Fill the file data, if provided
        $document->fill( $this->handleFile($request->file, false) );
        # save the Document
        $document->save();
        # Attach to Profile
        $this->parent($parent)->documents()->attach($document);
        # Return
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $parent
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function show($parent, Document $document)
    {
        $parent = $this->parent($parent);
        # Pick the View
        $view = "{$this->resource}.show";
        # Render
        return view( $view )->with(compact('parent', 'document'))->with($this->parentName, $parent);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $parent
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function edit($parent, Document $document)
    {
        $parent = $this->parent($parent);
        # Pick the View
        $view = "{$this->resource}.edit";
        # Render
        return view( $view )->with(compact('parent', 'document'))->with($this->parentName, $parent);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Manage\Document\UpdateRequest  $request
     * @param  int $parent
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $parent, Document $document)
    {
        # Update Document from request
        $document->update($request->input());
        # Update File data, if provided
        if($request->file){
            if($file = $this->handleFile($request->file, false)){
                $document->update($file);
            }
        }
        # Redirect
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @param  int $parent
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $parent, Document $document)
    {
        # remove record
        $this->parent($parent)->documents()->detach($document);
        # return
        return redirect()->back();
    }

    /**
     * Resolve the Parent from request
     *
     * @param  int $id
     * @return Illuminate\Database\Eloquent\Model
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     */
    protected function parent($id)
    {
        return app($this->parent)->findOrFail($id);
    }
}
