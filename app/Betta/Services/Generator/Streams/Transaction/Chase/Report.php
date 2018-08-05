<?php

namespace Betta\Services\Generator\Streams\Conference\Chase;

use Auth;
use Maatwebsite\Excel\Excel;
use Betta\Models\Conference;
use Betta\Models\ConferenceToField;
use Betta\Models\ConferenceToLiterature;
use Betta\Services\Generator\Foundation\AbstractReport;
use Betta\Composers\Chase\Program\Unclaimed\UnclaimedBuilder;
use App\Http\Controllers\Program\Scopes\AbstractScopesController;

class Report extends AbstractReport{

    /**
     * Bind the implementation
     *
     * @var Maatwebsite\Excel\Excel
     */
    protected $excel;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Conference
     */
    protected $conference;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ConferenceToField
     */
    protected $boothbadges;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ConferenceToLiterature
     */
    protected $materials;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Chase Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Chase Report';

    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        'brands',
        'reps',
        'conferenceStatus',
    ];

    /**
     * Variables to cache the tabs
     *
     * @var Illuminate\Support\Collection
     */
    protected $_unclaimed;
    protected $_unconfirmed;
    protected $_requested_materials;
    protected $_candy;
    protected $_booth_badges;
    protected $_payment_pending_release;
    protected $_closeout;
    protected $_drafts;
    protected $_pending_approval;

    /**
     * chase areas
     *
     * @var array
     */
    protected $chaseAreas = [
        [
            "Name"        =>  "Unclaimed",
            "Description" =>  "Conference without Conference Coordinator",
            "Issue Count" => 0,
        ],
        [
            "Name"        =>  "Unconfirmed (in progress)",
            "Description" =>  "Approved Conference without association confirmation",
            "Issue Count" => 0,
        ],
        [
            "Name"        =>  "Requested Materials",
            "Description" =>  "Marketing literature that has NOT been sent",
            "Issue Count" => 0,
        ],
        [
            "Name"        =>  "Candy",
            "Description" =>  "Candy that has NOT been sent",
            "Issue Count" => 0,
        ],
        [
            "Name"        =>  "Booth Badges",
            "Description" =>  "List of reps that will be attending - association has NOT confirmed",
            "Issue Count" => 0,
        ],
        [
            "Name"        =>  "Payment - pending release",
            "Description" =>  "Payment for exhibitor fee - Association has NOT confirmed",
            "Issue Count" => 0,
        ],
        [
            "Name"        =>  "Closeout",
            "Description" =>  "Completed conferences without a successful CLOSEOUT",
            "Issue Count" => 0,
        ],
        [
            "Name"        =>  "Drafts",
            "Description" =>  "Conference request that has not been completed by requestor",
            "Issue Count" => 0,
        ],
        [
            "Name"        =>  "Pending Approval",
            "Description" =>  "Conference request that has not been APPROVED by Manager or Brand Team",
            "Issue Count" => 0,
        ],
    ];

    /**
     * Formats of the resulting tabs
     *
     * @var array
     */
    protected $formats = [
        'Unclaimed' => [
            'B' =>  self::AS_DATE,
            'C' =>  self::AS_DATE,
            'H' =>  self::AS_CURRENCY,
        ],
        'Unconfirmed' => [
            'B' =>  self::AS_DATE,
            'C' =>  self::AS_DATE,
            'H' =>  self::AS_CURRENCY,
        ],
        'Requested Materials' => [
            'C' =>  self::AS_DATE,
            'D' =>  self::AS_DATE,
        ],
        'Candy' => [
            'C' =>  self::AS_DATE,
            'D' =>  self::AS_DATE,
        ],
        'Booth Badges' => [
            'C' =>  self::AS_DATE,
            'D' =>  self::AS_DATE,
        ],
        'Payment - Pending Release' => [
            'B' =>  self::AS_DATE,
            'C' =>  self::AS_DATE,
            'H' =>  self::AS_CURRENCY,
        ],
        'Closeout' => [
            'B' =>  self::AS_DATE,
            'C' =>  self::AS_DATE,
            'H' =>  self::AS_CURRENCY,
        ],
        'Drafts' => [
            'B' =>  self::AS_DATE,
            'C' =>  self::AS_DATE,
            'H' =>  self::AS_CURRENCY,
        ],
        'Pending Approval' => [
            'B' =>  self::AS_DATE,
            'C' =>  self::AS_DATE,
            'H' =>  self::AS_CURRENCY,
        ],
    ];

    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, Conference $conference, ConferenceToField $boothbadges, ConferenceToLiterature $materials)
    {
        $this->excel = $excel;
        $this->conference = $conference;
        $this->boothbadges = $boothbadges;
        $this->materials = $materials;
    }

    /**
     * Produce the report
     *
     * @return Excel
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            # Produce the tab
            # @todo exctract tabs into their own classes
            $excel->sheet('Summary', function ($sheet) {
                $sheet->freezeFirstRow()
                      ->setColumnFormat($this->getFormats() )
                      ->fromArray( $this->getSummary()->toArray() )
                      ->setAutoFilter();

                $this->setHeaderStyle($sheet);
            });

            $excel->sheet('Unclaimed', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('Unclaimed') )
                      ->fromArray( $this->getUnclaimed()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();

                $this->setHeaderStyle($sheet);
            });

            $excel->sheet('Unconfirmed (in progress)', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('Unconfirmed') )
                      ->fromArray( $this->getUnconfirmed()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();

                $this->setHeaderStyle($sheet);
            });

            $excel->sheet('Requested Materials', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('Requested Materials') )
                      ->fromArray( $this->getRequestedMaterials()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();

                $this->setHeaderStyle($sheet);
            });

            $excel->sheet('Candy', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('Candy') )
                      ->fromArray( $this->getCandy()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();

                $this->setHeaderStyle($sheet);
            });

            $excel->sheet('Booth Badges', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('Booth Badges') )
                      ->fromArray( $this->getBoothBadges()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();

                $this->setHeaderStyle($sheet);
            });

            $excel->sheet('Payment - Pending Release', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('Payment - Pending Release') )
                      ->fromArray( $this->getPaymentPendingRelease()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();

                $this->setHeaderStyle($sheet);
            });

            $excel->sheet('Closeout', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('Closeout') )
                      ->fromArray( $this->getCloseout()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();

                $this->setHeaderStyle($sheet);
            });

            $excel->sheet('Drafts', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('Drafts') )
                      ->fromArray( $this->getDrafts()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();

                $this->setHeaderStyle($sheet);
            });

            $excel->sheet('Pending Approval', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('Pending Approval') )
                      ->fromArray( $this->getPendingApproval()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();

                $this->setHeaderStyle($sheet);
            });

            $this->includeSqlTab($excel);

            # Make the first sheet active
            $excel->setActiveSheetIndex(0);

        })->store('xlsx', $this->getReportPath(), true);
    }


    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function loadMergeData($arguments)
    {
        return $this->conference->with($this->relations)->latest()->get();
    }

    /**
     * Resolve User from container
     *
     * @return User | null
     */
    protected function getUser()
    {
        return auth()->user();
    }

    /**
     * Return Visible Brands of the User
     *
     * @return Collection
     */
    protected function getActiveBrands()
    {
        return object_get($this->getUser(), 'profile.active_brands', collect([]));
    }

    protected function getSummary()
    {
        return collect($this->chaseAreas)->transform(function($chaseArea){
                    $chaseArea["Issue Count"] = $this->getIssueCount($chaseArea["Name"]);
                    return $chaseArea;
                });
    }

    protected function getIssueCount($chaseAreaName)
    {
        switch ($chaseAreaName) {
            case 'Unclaimed':
                return $this->unclaimedList()->count();
                break;

            case 'Unconfirmed (in progress)':
                return $this->unconfirmedList()->count();
                break;

            case 'Requested Materials':
                return $this->requestedMaterialsList()->count();
                break;

            case 'Candy':
                return $this->candyList()->count();
                break;

            case 'Booth Badges':
                return $this->boothBadgesList()->count();
                break;

            case 'Payment - pending release':
                return $this->paymentPendingReleaseList()->count();
                break;

            case 'Closeout':
                return $this->closeoutList()->count();
                break;

            case 'Drafts':
                return $this->draftsList()->count();
                break;

            case 'Pending Approval':
                return $this->pendingApprovalList()->count();
                break;

            default:
                return "0";
                break;
        }

        return 0;
    }

    /**
     * get transformed array of unclaimed conferences
     *
     * @return Array
     */
    protected function getUnclaimed()
    {
        return $this->unclaimedList()->transform(function($conference){
            return (new Handlers\UnclaimedRow($conference))->fill();
        });
    }

    /**
     * get list of unclaimed conferences
     *
     * @return Collection
     */
    protected function unclaimedList()
    {
        $this->_unclaimed = $this->_unclaimed ? $this->_unclaimed :  $this->loadUnclaimedList();

        return $this->_unclaimed;
    }

    /**
     * Load list of unclaimed conferences
     *
     * @return Collection
     */
    protected function loadUnclaimedList()
    {
        return $this->conference
                    ->unclaimed()
                    ->byBrand( $this->getActiveBrands() )
                    ->doesntHave('pms')
                    ->with($this->relations)
                    ->orderBy('start_date')->get();
    }

    /**
     * get transformed array of unconfirmed conferences
     *
     * @return Array
     */
    protected function getUnconfirmed()
    {
        return $this->unconfirmedList()->transform(function($conference){
            return (new Handlers\UnconfirmedRow($conference))->fill();
        });
    }

    /**
     * get list of unconfirmed conferences
     *
     * @return Collection
     */
    protected function unconfirmedList()
    {
        $this->_unconfirmed = $this->_unconfirmed ? $this->_unconfirmed :  $this->loadUnconfirmedList();
        return $this->_unconfirmed;
    }

    /**
     * Load list of unconfirmed conferences
     *
     * @return Collection
     */
    protected function loadUnconfirmedList()
    {
        return $this->conference
                    ->byBrand( $this->getActiveBrands() )
                    ->unconfirmed()
                    ->with($this->relations)
                    ->orderBy('start_date')->get();
    }

    /**
     * get transformed array of conferences where literature was requested but not sent
     *
     * @return Array
     */
    protected function getRequestedMaterials()
    {
        return $this->requestedMaterialsList()->transform(function($conference){
            return (new Handlers\RequestedMaterailsRow($conference))->fill();
        });
    }

    /**
     * get list of conferences where material(literature) was requested but not sent
     *
     * @return Collection
     */
    protected function requestedMaterialsList()
    {
        $this->_requested_materials = $this->_requested_materials ?  :  $this->loadMaterialsList();
        return $this->_requested_materials;
    }

    /**
     * Load list of conferences where material(literature) was requested but not sent
     *
     * @return Collection
     */
    protected function loadMaterialsList()
    {
        return $this->conference
                    ->whereHas('literatures', function($query){
                        $query->where('material_status', 0);
                    })
                    ->with($this->relations)
                    ->byBrand( $this->getActiveBrands() )
                    ->orderBy('start_date')->get();
    }

    /**
     * get list of transfomed array of conferences where candy was requested
     * remember this is different from the candyList in the summary page as requirements are different
     * Please refer to the task file https://frictionless.teamwork.com/#files/4873366
     *
     * @return Collection
     */
    protected function getCandy()
    {
        return $this->candyList()->transform(function($conference){
            return (new Handlers\CandyRow($conference))->fill();
        });
    }

    /**
     * get list of conferences where Candy was requested but not sent()
     *
     * @return Collection
     */
    protected function candyList()
    {
        $this->_candy = $this->_candy ?  :  $this->loadcandyList();
        return $this->_candy;
    }

    /**
     * Load list of conferences where Candy was requested but not sent()
     *
     * @return Collection
     */
    protected function loadcandyList()
    {
        return $this->conference
                    ->whereIsCandy(1)
                    ->whereCandyStatus(0)
                    ->byBrand( $this->getActiveBrands() )
                    ->with($this->relations)
                    ->orderBy('start_date')->get();
    }

    /**
     * get list of conferences where association has NOT confirmed reps that will be attending
     *
     * @return Collection
     */
    protected function getBoothBadges()
    {
        return $this->boothBadgesList()->transform(function($conference){
            return (new Handlers\BoothBadgesRow($conference))->fill();
        });
    }

    /**
     * get list of conferences where association has NOT confirmed reps that will be attending
     *
     * @return Collection
     */
    protected function boothBadgesList()
    {
        $this->_booth_badges = $this->_booth_badges ?  :  $this->loadBoothBadges();
        return $this->_booth_badges;
    }

    /**
     * Load list of conferences where association has NOT confirmed reps that will be attending
     *
     * @return Collection
     */
    protected function loadBoothBadges()
    {
        return $this->conference
                    ->whereHas('reps', function($query){
                        $query->where('badge_status', 1);
                    })
                    ->byBrand( $this->getActiveBrands() )
                    ->with($this->relations)
                    ->orderBy('start_date')->get();
    }

    /**
     * get list of transformed arrays for conferences where Association has Not confirmed payment for exhibitor fee
     *
     * @return Collection
     */
    protected function getPaymentPendingRelease()
    {
        return $this->paymentPendingReleaseList()->transform(function($conference){
            return (new Handlers\PaymentPendingRow($conference))->fill();
        });
    }

    /**
     * get list of conferences where Association has NOT confirmed  Payment for exhibitor fee
     *
     * @return Collection
     */
    protected function paymentPendingReleaseList()
    {
        $this->_payment_pending_release = $this->_payment_pending_release ?  :  $this->loadPaymentPendingReleaseList();
        return $this->_payment_pending_release;
    }

    /**
     * Load list of conferences where Association has NOT confirmed  Payment for exhibitor fee
     *
     * @return Collection
     */
    protected function loadPaymentPendingReleaseList()
    {

        return $this->conference
                    ->whereHas('payments', function($query){
                        $query->where('payment_status_id', 2);
                    })
                    ->with($this->relations)
                    ->byBrand( $this->getActiveBrands() )
                    ->orderBy('start_date')->get();
    }

    /**
     * get list of transformed array of conferences which are completed but not closed out
     *
     * @return Collection
     */
    protected function getCloseout()
    {
        return $this->closeoutList()->transform(function($conference){
            return (new Handlers\CloseoutRow($conference))->fill();
        });
    }

    /**
     * get list of dompleted conferences without a successful CLOSEOUT
     *
     * @return Collection
     */
    protected function closeoutList()
    {
        $this->_closeout = $this->_closeout ?  :  $this->loadCloseoutList();
        return $this->_closeout;
    }

    /**
     * Load list of dompleted conferences without a successful CLOSEOUT
     *
     * @return Collection
     */
    protected function loadCloseoutList()
    {
        return $this->conference
                    ->completed()
                    ->with($this->relations)
                    ->byBrand( $this->getActiveBrands() )
                    ->orderBy('start_date')->get();
    }

    /**
     * get transformed of array of lists which are in draft status
     *
     * @return Collection
     */
    protected function getDrafts()
    {
        return $this->draftsList()->transform(function($conference){
            return (new Handlers\DraftRow($conference))->fill();
        });
    }

    /**
     * get list of conferences request that has not been completed by requestor
     *
     * @return Collection
     */
    protected function draftsList()
    {
        $this->_drafts = $this->_drafts ?  :  $this->loadDrafts();
        return $this->_drafts;
    }

    /**
     * Load list of conferences request that has not been completed by requestor
     *
     * @return Collection
     */
    protected function loadDrafts()
    {
        return $this->conference
                    ->draft()
                    ->with($this->relations)
                    ->byBrand( $this->getActiveBrands() )
                    ->orderBy('start_date')->get();
    }

    /**
     * get list of transformed array of conferences which are not approved by managers or brand i.e. in submitted status
     *
     * @return Collection
     */
    protected function getPendingApproval()
    {
        return $this->pendingApprovalList()->transform(function($conference){
            return (new Handlers\PendingApprovalRow($conference))->fill();
        });
    }

    /**
     * get list of Conference request that has not been APPROVED by Manager or Brand Team
     *
     * @return Collection
     */
    protected function pendingApprovalList()
    {
        $this->_pending_approval = $this->_pending_approval ?  :  $this->loadPendingApproval();
        return $this->_pending_approval;
    }

    /**
     * Load list of Conference request that has not been APPROVED by Manager or Brand Team
     *
     * @return Collection
     */
    protected function loadPendingApproval()
    {
        return $this->conference
                    ->submitted()
                    ->byBrand( $this->getActiveBrands() )
                    ->with($this->relations)
                    ->orderBy('start_date')->get();
    }

    /**
      * Set Header Style
      *
      * @param $sheet
      * @return Excel
      */
    protected function setHeaderStyle($sheet)
    {
        $sheet->row(1, function($row) {
            $row->setFontWeight('bold');
            $row->setBackground('#D3D3D3');
        });
        return $sheet;
    }

    /**
     * Return formats for the Tabs
     *
     * @param  string $tab
     * @return array
     */
    public function getFormats( $tab = '')
    {
        return data_get($this->formats, $tab, []);
    }

}
