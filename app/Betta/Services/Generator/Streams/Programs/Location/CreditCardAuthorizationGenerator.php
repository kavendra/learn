<?php

namespace Betta\Services\Generator\Streams\Programs\Location;

use Exception;
use Betta\Models\Program;

class CreditCardAuthorizationGenerator
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program;
     */
    protected $program;

    /**
     * Class constructor
     *
     * @param Program $program
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
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
        if ($program = $this->getProgram($arguments)) {
            return $this->process($program);
        }

        throw new Exception("Speaker is not found", 500);
    }

    /**
     * Obain credit card authorizations and return it
     *
     * @param  Program $program
     * @return Collection
     */
    protected function process(Program $program)
    {
        $all = collect([]);
        # only get the information from primary location
        $authorization = data_get($program->primaryLocation, 'credit_card_authorization');
        # Attach if present
        if($authorization){
            $all->push($authorization);
        }
        # Return
        return $all;
    }

    /**
     * Obtain the data for generator
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
}
