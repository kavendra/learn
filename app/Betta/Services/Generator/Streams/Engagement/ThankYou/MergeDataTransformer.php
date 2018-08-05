<?php

namespace Betta\Services\Generator\Streams\Engagement\ThankYou;

use Carbon\Carbon;
use Betta\Models\Engagement;
use Betta\Services\Generator\Streams\CommonMergeFields;
use Betta\Services\Generator\Foundation\AbstratRowHandler;
use Betta\Services\Generator\Streams\Profile\MergesProfileData;
use Betta\Services\Generator\Streams\Engagement\MergesEngagementData;

class MergeDataTransformer extends AbstratRowHandler
{
    use CommonMergeFields;
    use MergesProfileData;
    use MergesEngagementData;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Engagement
     */
    protected $engagement;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'current_date',
        'engagement_id',
        'engagement_brand',
        'engagement_date_long',
        'engagement_end_date',
        'engagement_start_date',
        'engagement_label',
        'engagement_type_label',
        'engagement_status_label',
        'engagement_location_name',
        'engagement_location_city_state',
        'engagement_location_city_state_zip',
        'engagement_manager_name',
        'engagement_manager_phone',
        'engagement_manager_email',
        'profile_id',
        'profile_preferred_name_degree',
        'profile_preferred_name',
        'profile_first_name',
        'profile_last_name',
        'profile_street_address',
        'profile_city_state',
        'profile_city_state_zip',
        #
        'support_phone',
        'support_email',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'profile',
        'engagement',
    ];

    /**
     * Common Date Format
     *
     * @var string
     */
    protected $dateFormat = 'F j, Y';

    /**
     * Create new class instance
     *
     * @param Betta\Models\Engagement $engagement
     */
    public function __construct(Engagement $engagement)
    {
        $this->engagement = $engagement;
        # Set hidden attributes
        $this->setAttribute('profile', $engagement->profile);
    }
}
