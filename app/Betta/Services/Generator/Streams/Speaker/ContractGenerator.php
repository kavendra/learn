<?php

namespace Betta\Services\Generator\Streams\Speaker;

use Betta\Models\Contract;
use Betta\Models\ProfileRateCard;
use Illuminate\Support\MessageBag;
use Betta\Services\Generator\Drivers\WordTemplate;

class ContractGenerator
{
    /**
     * Bind the Implementation
     *
     * @var Betta\Models\Contract
     */
    protected $contract;

    /**
     * Keep the Errors in a MessageBag
     *
     * @var Illuminate\Support\MessageBag
     */
    protected $errors;

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
        # If we can can get the Contract from arguments, return the handling result
        if ($contract = $this->getContract($arguments)) {
            return $this->process($contract);
        }
        # return errors
        return $this->getErrors();
    }

    /**
     * Return contract from DB
     *
     * @param  array $arguments
     * @return Contract | Exception: ModelNotFound
     */
    protected function getContract($arguments = [])
    {
        if ($contract = array_get($arguments, 'contract') and $contract instanceOf Contract) {
            return $contract->load($this->relations);
        }

        if (!$id = array_get($arguments, 'id')) {
            # Add an error
            $this->errors->push('No Contract provided');

            return false;
        }

        # Resolve Contract from DB
        return $this->contract->with($this->relations)->findOrFail($id);
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
        $template = new WordTemplate( $this->getTemplate($contract) );

        # return new merged template as a steam
        $file = $template->merge($this->getMergeData($contract))
                         ->save($this->getSavePath($contract));

        return $file;
    }

    /**
     * Resolve the Template Path from the last attached template of the ContractType
     *
     * @return string
     */
    protected function getTemplate(Contract $contract)
    {
        if ($path = data_get($contract->contractType, 'template.uri')){
            return $path;
        }

        # Add an error
        $this->errors->push('No Template provided');

        return false;
    }

    /**
     * The path where to save the file to
     *
     * @param  Contract $contract
     * @return string
     */
    protected function getSavePath(Contract $contract)
    {
        return storage_path( "{$this->storagePath}/". $this->getFileName($contract) );
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

    /**
     * Merge the data
     *
     * @param  Contract $contract
     * @return Array
     */
    protected function getMergeData(Contract $contract)
    {
        # Init the Data array
        $data = array();
        # tune the definitions aagainst the model
        foreach ($this->getDefinitions() as $mergeKey => $definition) {
            array_set($data, $mergeKey, object_get($contract, $definition));
        }
        # Add supplemental data
        $data = $this->injectAdditionalData($data, $contract);
        # Retrun
        return $data;
    }

    /**
     * Map the definitions to properties
     *
     * @return Array
     */
    protected function getDefinitions()
    {
        return [
            'CONTRACT_ID' => 'id',
            'FORMAL_NAME' => 'w9.formal_name',
            'FORMAL_NAME_DEGREE' => 'w9.formal_name_degree',
            'BUSINESS_NAME' => 'w9.formal_business_name',
            'ADDRESS_LINE' => 'w9.form_address',
            'ADDRESS_CITY' => 'w9.form_city',
            'ADDRESS_STATE' => 'w9.form_state',
            'ADDRESS_ZIP' => 'w9.form_zip',
            'LAST_NAME' => 'profile.last_name',
            'SPECIALTY' => 'profile.hcpProfile.degree',
            'PROFILE_DEGREE' => 'w9.profile_degree',
        ];
    }

    /**
     * Inject additional Data into the MergeData array
     *
     * @param  array  $mergeData
     * @param  Contract $contract
     * @return array
     */
    protected function injectAdditionalData($mergeData, Contract $contract)
    {
        $data = array(

            'USD' => '$',
            'ID' => $contract->id,
            'CONTRACT_START_DATE' => $contract->valid_from->format('F j, Y'),
            'CONTRACT_END_DATE' => $contract->valid_to->format('F j, Y'),
            'SALUTATION' => data_get($contract->w9, 'salutation') ?: 'Dr.',
            'CURRENT_DATE' => date('F j, Y'),
            'SUPPORT_PHONE' => config('fls.support_phone'),
            'ADDRESS_CITY_STATE_ZIP' => data_get($contract->w9, 'city_state_zip', ''),

            # Special Rates
            'AUDIO' => $this->firstRate($contract->rateCard, 'teleconference_rate'),
            'TRAINING' => $this->firstRate($contract->rateCard, 'training_rate'),
            'CONGRESS_ACTIVITY' => $this->firstRate($contract->rateCard, 'congress_activity'),

            'FIRST_RATE_UP_200'    => $this->firstRate($contract->rateCard, 'rate_up200'),
            'FIRST_RATE_UP_1000'   => $this->firstRate($contract->rateCard, 'rate_up1000'),
            'FIRST_RATE_UP_3000'   => $this->firstRate($contract->rateCard, 'rate_up3000'),
            'FIRST_RATE_UP_7000'   => $this->firstRate($contract->rateCard, 'rate_up7000'),
            'FIRST_RATE_OVER_7000' => $this->firstRate($contract->rateCard, 'rate_over7000'),

            'MULTI_RATE2_UP_200'    => $this->secondRate($contract->rateCard, 'rate_up200'),
            'MULTI_RATE2_UP_1000'   => $this->secondRate($contract->rateCard, 'rate_up1000'),
            'MULTI_RATE2_UP_3000'   => $this->secondRate($contract->rateCard, 'rate_up3000'),
            'MULTI_RATE2_UP_7000'   => $this->secondRate($contract->rateCard, 'rate_up7000'),
            'MULTI_RATE2_OVER_7000' => $this->secondRate($contract->rateCard, 'rate_over7000'),

            'MULTI_RATE3_UP_200'    => $this->thirdRate($contract->rateCard, 'rate_up200'),
            'MULTI_RATE3_UP_1000'   => $this->thirdRate($contract->rateCard, 'rate_up1000'),
            'MULTI_RATE3_UP_3000'   => $this->thirdRate($contract->rateCard, 'rate_up3000'),
            'MULTI_RATE3_UP_7000'   => $this->thirdRate($contract->rateCard, 'rate_up7000'),
            'MULTI_RATE3_OVER_7000' => $this->thirdRate($contract->rateCard, 'rate_over7000'),

            'LIVE_TRAINING_RATE_UP_200'    => $this->firstRate($contract->rateCard, 'live_training_rate_up200'),
            'LIVE_TRAINING_RATE_UP_1000'   => $this->firstRate($contract->rateCard, 'live_training_rate_up1000'),
            'LIVE_TRAINING_RATE_UP_3000'   => $this->firstRate($contract->rateCard, 'live_training_rate_up3000'),
            'LIVE_TRAINING_RATE_UP_7000'   => $this->firstRate($contract->rateCard, 'live_training_rate_up7000'),
            'LIVE_TRAINING_RATE_OVER_7000' => $this->firstRate($contract->rateCard, 'live_training_rate_over7000'),
        );

        return array_merge($mergeData, $data);
    }


    /**
     * Get the First rate
     *
     * @param  ProfileRateCard $card
     * @return SUM two rates
     */
    protected function firstRate(ProfileRateCard $card, $type='rate_up200', $formatted = true)
    {
        $val = data_get($card, "{$type}.honorarium_rate_1", 0);

        return $formatted ? $this->format($val) : $val;
    }


    /**
     * Get the second rate
     *
     * @param  ProfileRateCard $card
     * @return SUM two rates
     */
    protected function secondRate(ProfileRateCard $card, $type='rate_up200', $formatted = true)
    {
        $val = data_get($card, "{$type}.honorarium_rate_1", 0)
             + data_get($card, "{$type}.honorarium_rate_2", 0);

        return $formatted ? $this->format($val) : $val;
    }


    /**
     * Get the third rate
     *
     * @return SUM three rates
     */
    protected function thirdRate(ProfileRateCard $card, $type='rate_up200', $formatted = true)
    {
        $val = $this->secondRate($card, $type, false)
             + data_get($card, "{$type}.honorarium_rate_3", 0);

        return $formatted ? $this->format($val) : $val;
    }


    /**
     * Format the rate
     *
     * @param  mixed $value
     * @return string
     */
    protected function format($value)
    {
        return '$ '. number_format($value, 2);
    }
}
