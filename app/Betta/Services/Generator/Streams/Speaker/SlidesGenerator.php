<?php

namespace Betta\Services\Generator\Streams\Speaker;

use Betta\Models\Document;
use Betta\Models\ProgramSpeaker;
use Betta\Services\Generator\Merges;

class SlidesGenerator
{
    use Merges;

    /**
     * Bind the Implementation
     *
     * @var Betta\Models\ProgramSpeaker
     */
    protected $speaker;

    /**
     * List the relations to load with program
     *
     * @var array
     */
    protected $relations = [
        'program.presentations.documents.metas'
    ];

    /**
     * Create new instance of the class
     *
     * @param ProgramSpeaker $speaker
     */
    public function __construct(ProgramSpeaker $speaker)
    {
        $this->speaker = $speaker;
    }

    /**
     * Handle document generation
     *
     * @param  array  $arguments
     * @return array
     */
    public function handle($arguments = [])
    {
        # If we can can get the Speaker from arguments, return the handling result
        if ($speaker = $this->getSpeaker($arguments)) {
            return $this->process($speaker);
        }

        throw new \Exception("Speaker is not found", 500);
    }

    /**
     * Resolve ProgramSpeaker
     *
     * @param  array $arguments
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException if the model is not found
     * @return ProgramSpeaker
     */
    protected function getSpeaker($arguments = [])
    {
        if ($speaker = array_get($arguments, 'speaker') AND $speaker instanceOf ProgramSpeaker) {
            return $speaker->load($this->relations);
        }

        if (!$id = array_get($arguments, 'id')) {
            # Add an error
            $this->errors->push('No Program Speaker provided');

            return false;
        }

        # Resolve Speaker from DB
        return $this->speaker->with($this->relations)->findOrFail($id);
    }

    /**
     * Get all the documents that are visible for the speaker
     *
     * @see    Betta\Models\Traits\HasMetas
     * @param  Betta\Models\ProgramSpeaker $speaker
     * @return Collection
     */
    protected function process(ProgramSpeaker $speaker)
    {
        return  $speaker->program
                        ->filtered_presentations
                        ->where('brand_id', $speaker->brand_id)
                        ->pluck('documents')
                        ->collapse()
                        ->transform(function($document) use($speaker){
                            return $document->is_mergeable ? $this->merge($document, $this->getMergeData($speaker)) : $document;
                        });
    }

    /**
     * Do the merge data
     *
     * @param  ProgramSpeaker $speaker
     * @return array
     */
    protected function getMergeData(ProgramSpeaker $speaker)
    {
        return [
            'SPEAKER_NAME' => $speaker->preferred_name_degree,
            'PROGRAM_DATE' => $speaker->program->full_date,
        ];
    }
}
