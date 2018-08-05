<?php

namespace Betta\Services\Generator\Streams\Field;

use Exception;
use Betta\Models\Program;
use Illuminate\Support\MessageBag;
use Betta\Services\Generator\Merges;

class RepSlidesGenerator
{
    use Merges;
    /**
     * Share Errors
     *
     * @var Collection
     */
    protected $errors;

    /**
     * Location to store the file
     *
     * @var string
     */
    protected $storagePath = 'app/temp';

    /**
     * Class constructor
     *
     * @param Engagement $engagement
     * @param MessageBag $bag
     */
    public function __construct(Program $program, MessageBag $bag)
    {
        # implementations
        $this->errors  = $bag;
        $this->program = $program;
    }

    /**
     * Handling should be simple and should return instance of Document
     *
     * @param  array  $arguments
     * @return Instance
     */
    public function handle($arguments = array())
    {
        # If we can can get the profielContract from arguments, return the handling result
        if (!$program = $this->getProgram($arguments)) {
            # return errors
            return $this->getErrors();
        }

        # set the value to internal pointer
        return $this->process($program);
    }

    /**
     * Return errors
     *
     * @return Collection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Obtain the data for populating the WordDocument
     *
     * @param  array $arguments
     * @return Program
     */
    protected function getProgram($arguments)
    {
        if ($program = array_get($arguments, 'program') AND $program instanceOf Program) {
            return $program;
        }

        if ($id = array_get($arguments, 'id')) {
            return $this->program->findOrFail($id);
        }

        throw new Exception("No Program Provided for generator", 500);
    }

    /**
     * List all presentation documents that may be filtered to Speaker
     *
     * @see    Betta\Models\Traits\HasMetas
     * @param  Program $program
     * @return File
     */
    protected function process(Program $program)
    {
        return $program->filtered_presentations
                       ->pluck('documents')
                       ->collapse()
                       ->transform(function($document) use($program){
                            return $document->is_mergeable ? $this->merge($document, $this->getMergeData($program)) : $document;
                        });
    }

    /**
     * Merge the Document
     *
     * @param  Program $program
     * @return array
     */
    protected function getMergeData(Program $program)
    {
        $speaker = $program->primarySpeakers->first();

        return [
            'SPEAKER_NAME' => data_get($speaker, 'preferred_name_degree'),
            'PROGRAM_DATE' => $program->full_date,
        ];
    }
}
