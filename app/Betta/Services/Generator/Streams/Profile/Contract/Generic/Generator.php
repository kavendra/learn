<?php

namespace Betta\Services\Generator\Streams\Profile\Contract\Generic;

use Betta\Models\Contract;
use Betta\Models\ProfileRateCard;
use Betta\Services\Generator\Drivers\WordTemplate;
use Betta\Services\Generator\Foundation\AbstractGenerator;

class Generator extends AbstractGenerator
{
    /**
     * Bind the Implementation
     *
     * @var Betta\Models\Contract
     */
    protected $contract;

    /**
     * List the relations to load with Contract
     *
     * @var array
     */
    protected $relations = [
        'w9',
        'profile',
        'rateCard',
    ];

    /**
     * The Contract Template
     *
     * @var string
     */
    protected $template;

    /**
     * Location to store the file
     *
     * @var string
     */
    protected $storagePath = 'app/private';

    /**
     * Create new instance of the class
     *
     * @param Contract $contract
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * Handle document generation
     *
     * @param  array  $arguments
     * @return array
     */
    public function handle($arguments = [])
    {
        # If we can can get the Contract from arguments, return the handling result
        if ($contract = $this->getContract($arguments)) {
            return $this->process($contract);
        }
        throw new GeneratorException("No Contract Found", 404);

    }

    /**
     * Return contract from DB
     *
     * @param  array $arguments
     * @return Contract | Exception: ModelNotFound
     */
    protected function getContract($arguments = [])
    {
        if ($contract = data_get($arguments, 'contract') and $contract instanceOf Contract) {
            return $contract->load($this->relations);
        }
        # Resolve Contract from DB
        return $this->contract->with($this->relations)->findOrFail(data_get($arguments, 'id'));
    }

    /**
     * Process the Contract into File
     *
     * @param  Betta\Models\Contract $contract
     * @return Array
     */
    protected function process(Contract $contract)
    {
        # make new template
        $template = new WordTemplate($this->getTemplate($contract));
        # return new merged template as a steam
        $file = $template->merge($this->data($contract)->toArray())
                         ->save($this->getSavePath($contract));

        return $file;
    }

    /**
     * Handle the data resolution
     *
     * @param  Contract $contract
     * @return Betta\Services\Generator\Streams\Profile\Contract\Generic\MergeData
     */
    protected function data(Contract $contract)
    {
        return with( new MergeData($contract) )->fill();
    }

    /**
     * Resolve the Template Path from the last attached template of the ContractType
     *
     * @return string
     */
    protected function getTemplate(Contract $contract)
    {
        return data_get($contract->contractType, 'template.uri');
    }

    /**
     * The path where to save the file to
     *
     * @param  Contract $contract
     * @return string
     */
    protected function getSavePath(Contract $contract)
    {
        return storage_path("{$this->storagePath}/". $this->getFileName($contract));
    }

    /**
     * Compile a Name for the Resulting document
     *
     * @see    Str:slug()
     * @param  Contract $contract
     * @return string
     */
    protected function getFileName(Contract $contract)
    {
        # make a safe file name
        $safeLabel = nc_slug($contract->contract_type_label, ' ');
        # render
        return "{$safeLabel} Contract - {$contract->profile->preferred_name_degree}.docx";
    }
}
