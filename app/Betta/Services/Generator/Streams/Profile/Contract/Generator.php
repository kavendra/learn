<?php

namespace Betta\Services\Generator\Streams\Profile\Contract;

use Exception;
use Betta\Models\Contract;
use Betta\Models\ProfileRateCard;
use Betta\Foundation\Helpers\Strings;
use Betta\Services\Generator\Drivers\WordTemplate;
use Betta\Services\Generator\Streams\Foundation\AbstractGenerator;

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
    protected $relations = ['profile', 'w9', 'rateCard'];

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
        $this->setArguments($arguments);

        # If we can can get the Contract from arguments, return the handling result
        if ($contract = $this->getContract()) {
            return $this->process($contract);
        }

        return false;
    }

    /**
     * Return contract from DB
     *
     * @param  array $arguments
     * @return Contract | Exception: ModelNotFound
     */
    protected function getContract()
    {
        if ($contract = $this->argument('contract') AND $contract instanceOf Contract) {
            return $contract->load($this->relations);
        }

        return $this->contract->with($this->relations)->findOrFail($this->argument('id', 0));
    }

    /**
     * Process the actual Contract into a document
     *
     * @return array.. ?
     */
    protected function process(Contract $contract)
    {
        # make new template
        $template = new WordTemplate( $this->getTemplate($contract) );
        # return new merged template as a steam
        $file = $template->merge($this->getMergeData($contract)->toArray())
                         ->save($this->getSavePath($contract))
                         ->convertToPdf();
        # resolve
        return $file;
    }

    /**
     * Resolve the Template Path of the contract
     *
     * @return string
     */
    protected function getTemplate(Contract $contract)
    {
        return data_get($contract->template, 'uri');
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
        $safeLabel = Strings::ncSlug($contract->contract_type_label, ' ');
        # render
        return "{$safeLabel} Contract - {$contract->profile->preferred_name_degree}.docx";
    }

    /**
     * Return merge data from the Contract
     *
     * @param  Contract $contract
     * @return MergeData
     */
    protected function getMergeData(Contract $contract)
    {
        return (new MergeData($contract))->fill();
    }
}
