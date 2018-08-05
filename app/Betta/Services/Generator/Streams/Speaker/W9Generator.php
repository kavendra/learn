<?php

namespace Betta\Services\Generator\Streams\Speaker;

use Betta\Models\ProfileW9;
use Betta\Services\Generator\Drivers\PdfTemplate;
use Betta\Services\Generator\Foundation\AbstractGenerator;

class W9Generator extends AbstractGenerator
{
    /**
     * Bind the Implementation
     *
     * @var Betta\Models\ProfileW9
     */
    protected $profileW9;


    /**
     * List the relations to load with W9
     *
     * @var array
     */
    protected $relations = [];


    /**
     * Locate the Template for the W9
     *
     * @var string
     */
    protected $template = 'app/templates/speaker/w9_2014.pdf';


    /**
     * Transform (or not) the merge keys?
     *
     * @var boolean
     */
    protected $transformKeys = true;


    /**
     * Location to store the file
     *
     * @var string
     */
    protected $storagePath = 'app/private';


    /**
     * Create New Instance of the class
     *
     * @param ProfileW9 $profileW9
     */
    public function __construct(ProfileW9 $profileW9)
    {
        $this->profileW9 = $profileW9;
        $this->template = storage_path($this->template);
    }


    /**
     * Handle document generation
     *
     * @param  array  $arguments
     * @return array
     */
    public function handle($arguments = [])
    {
        # If we can can get the ProfileW9 from arguments, return the handling result
        if ($profileW9 = $this->getProfileW9($arguments)) {
            return $this->process($profileW9);
        }

        # return errors
        return $this->getErrors();
    }


    /**
     * Return ProfileW9 from DB
     *
     * @param  array $arguments
     * @return ProfileW9 | Exception: ModelNotFound
     */
    protected function getProfileW9($arguments = [])
    {
        if ($profileW9 = array_get($arguments, 'profileW9') AND $profileW9 instanceOf ProfileW9) {
            return $profileW9->load($this->relations);
        }

        if (!$id = array_get($arguments, 'id')) {
            # Add an error
            $this->getErrors()->add('errors', 'No Record ID provided');

            return false;
        }

        # Resolve Profile W9 record from DB
        return $this->profileW9->with($this->relations)->findOrFail($id);
    }


    /**
     * Process the W9 into File
     *
     * @param  Betta\Models\ProfileW9 $profileW9
     * @return Array
     */
    protected function process(ProfileW9 $profileW9)
    {
        # make new template
        $template = new PdfTemplate( $this->template );

        # return new merged template as a steam
        $file = $template->merge($this->getMergeData($profileW9), true, $this->transformKeys)
                         ->save($this->getSavePath($profileW9));

        return $file;
    }


    /**
     * The path where to save the file to
     *
     * @param  ProfileW9 $profileW9
     * @return string
     */
    protected function getSavePath(ProfileW9 $profileW9)
    {
        return storage_path( "{$this->storagePath}/". $this->getFileName($profileW9) );
    }


    /**
     * Compile a Name for the Resulting document
     *
     * @param  ProfileW9 $profileW9
     * @return string
     */
    protected function getFileName(ProfileW9 $profileW9)
    {
        return "W9 {$profileW9->profile->preferred_name_degree}.pdf";
    }


    /**
     * Merge the data
     *
     * @param  ProfileW9 $profileW9
     * @return Array
     */
    protected function getMergeData(ProfileW9 $profileW9)
    {
        # Init the Data array
        $data = array();

        # tune the definitions aagainst the model
        foreach ($this->getDefinitions() as $mergeKey => $definition) {
            array_set($data, $mergeKey, object_get($profileW9, $definition));
        }

        # Add supplemental data
        $data = $this->injectAdditionalData($data, $profileW9);

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
            'salutation'             => 'salutation',
            'profile_degree'         => 'profile_degree',
            'formal_name'            => 'formal_name',
            'formal_business_name'   => 'formal_business_name',
            'form_address'           => 'form_address',
            'form_city'              => 'form_city',
            'form_state'             => 'form_state',
            'form_zip'               => 'form_zip',
            'form_accounts'          => 'form_accounts',

            'classification_i'       => 'is_classification_individual',
            'classification_c'       => 'is_classification_c_corp',
            'classification_s'       => 'is_classification_s_corp',
            'classification_p'       => 'is_classification_partnership',
            'classification_t'       => 'is_classification_trust',
            'classification_l'       => 'is_classification_llc',
            'classification_l_other' => 'is_classification_llc_modifier',
            'classification_o'       => 'is_classification_other',
            'classification_o_other' => 'is_classification_other_modifier',

            'exempt_code'            => 'exempt_code',
            'exempt_code_fatca'      => 'exempt_code_fatca',
        ];
    }


    /**
     * Inject additional Data into the MergeData array
     *
     * @param  array  $mergeData
     * @param  ProfileW9 $profileW9
     * @return array
     */
    protected function injectAdditionalData($mergeData, ProfileW9 $profileW9)
    {
        $data = array(
            'CURRENT_DATE' => date('F j, Y'),
            'ssn_1' => array_get( $profileW9->ssn_array, 0, ''),
            'ssn_2' => array_get( $profileW9->ssn_array, 1, ''),
            'ssn_3' => array_get( $profileW9->ssn_array, 2, ''),
            'ssn_4' => array_get( $profileW9->ssn_array, 3, ''),
            'ssn_5' => array_get( $profileW9->ssn_array, 4, ''),
            'ssn_6' => array_get( $profileW9->ssn_array, 5, ''),
            'ssn_7' => array_get( $profileW9->ssn_array, 6, ''),
            'ssn_8' => array_get( $profileW9->ssn_array, 7, ''),
            'ssn_9' => array_get( $profileW9->ssn_array, 8, ''),

            'ein_1' => array_get( $profileW9->ein_array, 0, ''),
            'ein_2' => array_get( $profileW9->ein_array, 1, ''),
            'ein_3' => array_get( $profileW9->ein_array, 2, ''),
            'ein_4' => array_get( $profileW9->ein_array, 3, ''),
            'ein_5' => array_get( $profileW9->ein_array, 4, ''),
            'ein_6' => array_get( $profileW9->ein_array, 5, ''),
            'ein_7' => array_get( $profileW9->ein_array, 6, ''),
            'ein_8' => array_get( $profileW9->ein_array, 7, ''),
            'ein_9' => array_get( $profileW9->ein_array, 8, ''),
        );

        return array_merge($mergeData, $data);
    }

}
