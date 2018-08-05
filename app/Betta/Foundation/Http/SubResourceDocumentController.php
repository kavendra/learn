<?php

namespace Betta\Foundation\Http;

use Betta\Models\Document;
use Illuminate\Http\Request;
use App\Http\Traits\HandlesUploads;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

abstract class SubResourceDocumentController extends Controller
{
    use HandlesUploads;

    /**
     * Inject implementation
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $parent;

    /**
     * Inject implementation of secondary resource
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $owner;

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
     * Variable name for the secondary resource
     *
     * @var string
     */
    protected $ownerName = 'owner';

    /**
     * Set true if you want to attach document to both contexts
     *
     * @var boolean
     */
    protected $attachToParent = false;

    /**
     * Set true if you want to detach document to both contexts
     *
     * @var boolean
     */
    protected $detachFromParent = false;

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
     * @param  int $owner
     * @return Illuminate\Http\Response
     */
    public function index($parent, $owner)
    {
        # Resolve parent
        $parent = $this->parent($parent);
        # Resolve Owner
        $owner = $this->owner($owner);
        # Eager-load data
        $documents = $owner->documents->load($this->relations);
        # Pick the View
        $view = "{$this->resource}.index";
        # Render
        return view( $view )->with(compact('documents'))
                            ->with($this->parentName, $parent)
                            ->with($this->ownerName, $owner);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int $parent
     * @param  int $owner
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function create($parent, $owner, Document $document)
    {
        # resolve parent model
        $parent = $this->parent($parent);
        # Resolve Owner
        $owner = $this->owner($owner);
        # Pick the View
        $view = "{$this->resource}.create";
        # Render
        return view( $view )->with(compact('parent','owner','document'))
                            ->with($this->parentName, $parent)
                            ->with($this->ownerName, $owner);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  int $parent
     * @param  int $owner
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $parent, $owner)
    {
        # Create New Document instance
        $document = $this->document->newInstance($request->input());
        # Fill the file data, if provided
        $document->fill( $this->handleFile($request->file, false));
        # save the Document
        $document->save();
        # Attach to Owner
        $this->owner($owner)->documents()->attach($document);
        # Attach to Parent?
        if($this->attachToParent){
            $this->parent($parent)->documents()->attach($document);
        }
        # Return
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $parent
     * @param  int $owner
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function show($parent, $owner, Document $document)
    {
        $parent = $this->parent($parent);
        $owner = $this->owner($owner);
        # Pick the View
        $view = "{$this->resource}.show";
        # Render
        return view( $view )->with(compact('parent', 'owner', 'document'))
                            ->with($this->parentName, $parent)
                            ->with($this->ownerName, $owner);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $parent
     * @param  int $owner
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function edit($parent, $owner, Document $document)
    {
        $parent = $this->parent($parent);
        $owner = $this->owner($owner);
        # Pick the View
        $view = "{$this->resource}.edit";
        # Render
        return view( $view )->with(compact('parent', 'owner', 'document'))
                            ->with($this->parentName, $parent)
                            ->with($this->ownerName, $owner);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Manage\Document\UpdateRequest  $request
     * @param  int $parent
     * @param  int $owner
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $parent, $owner, Document $document)
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
     * @param  int $owner
     * @param  Betta\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $parent, $owner, Document $document)
    {
        # remove record
        $this->owner($owner)->documents()->detach($document);
        # Detach if requested
        if($this->detachFromParent){
            $this->parent($parent)->documents()->detach($document);
        }
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

    /**
     * Resolve the Parent from request
     *
     * @param  int $id
     * @return Illuminate\Database\Eloquent\Model
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     */
    protected function owner($id)
    {
        return app($this->owner)->findOrFail($id);
    }
}
