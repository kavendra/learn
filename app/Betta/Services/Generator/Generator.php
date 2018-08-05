<?php

namespace Betta\Services\Generator;

class Generator
{
    /**
     * Bind the application
     *
     * @var
     */
    protected $app;

    /**
     * Name of the varialbe in the Input Array to hold the value of the Key
     *
     * @var string
     */
    protected $key = 'type';

    /**
     * Implementation of the Generator Handler
     *
     * @var class
     */
    protected $handler;

    /**
     * List of items, where Key is corresponding to the name of the Generator class
     *
     * @var Array
     */
    protected $generators = array(
        # Program Generators
        'program/summary'                   => 'Programs\SummaryGenerator',
        'program/sign_in_sheet'             => 'Programs\SignInSheetGenerator',
        'program/checklist'                 => 'Programs\ChecklistGenerator',
        'program/invitation'                => 'Programs\InvitationGenerator',
        'program/invitation/printed'        => 'Programs\PrintedInvitationGenerator',
        'program/vcalendar'                 => 'Programs\VcalendarGenerator',
        'rep/slides'				        => 'Field\RepSlidesGenerator',
        'program/missing_receipt_affidavit' => 'Programs\MissingReceiptAffidavitGenerator',
        'program/location/ccauthorization'  => 'Programs\Location\CreditCardAuthorizationGenerator',
        'program/location/fls_cc_authorization'  => 'Programs\Location\FlsCardAuthorization\Generator',
        'program/caterer/ccauthorization'  => 'Programs\Caterer\CreditCardAuthorizationGenerator',
        'program/caterer/fls_cc_authorization'  => 'Programs\Caterer\FlsCardAuthorization\Generator',
        'program/location/menu'                 => 'Programs\Location\MenuGenerator',
        # Report Exception Generators
        # 'report/exceptions/grid'     => 'Exceptions\GridReport',

        'conferenceschedule/vcalendar'                 => 'Conference\ScalendarGenerator',
        'affiliatemeetingschedule/acalendar'                 => 'Conference\AcalendarGenerator',

        # Speaker Report Generators
        'speaker/speaker-lists'               => 'Speaker\Report\SpeakerList\SpeakerListsReport',
        'report/speaker/contract'             => 'Speaker\ContractReport',
        'report/speaker/active-nomination'    => 'Speaker\Report\ActiveNominationReport',
        'report/speaker/max-cap'              => 'Speaker\Report\MaxCapReport',
        # 'report/speakers/details'           => 'Speakers\DetailsReport',
        # 'report/speakers/honorarium-due'    => 'Speakers\HonorariumDueReport',
        'speaker/speaker-contact-information' => 'Speaker\SpeakerContactInformationReport',
        'speaker/training'                    => 'Speaker\Report\Training\TrainingReport',
        'speaker/nomination'                  => 'Speaker\NominationReport',
        'speaker/active-speaker-contact'      => 'Speaker\Report\ActiveSpeakerContract\Report',
        # Retiring in favor of profile/contract/generic
        # 'speaker/contract'                    => 'Speaker\ContractGenerator',
        'speaker/report/profile-detail'       => 'Speaker\Report\ProfileDetailReport',
        'speaker/thank_you_letter'            => 'Speaker\ThankYou\Generator',
        # 'report/speakers/payments'          => 'Speakers\PaymentsReport',
        # 'report/speakers/short-list'        => 'Speakers\ShortListReport',
        # 'report/speakers/survey'            => 'Speakers\SurveyReport',
        # 'report/speakers/utilization'       => 'Speakers\UtilizationReport',
        # 'report/speakers/utilization-field' => 'Speakers\UtilizationFieldReport',
        # 'report/speakers/training'          => 'Speakers\TrainingReport',
        'speaker/slides'                      => 'Speaker\SlidesGenerator',

        # Speaker W9
        'speaker/w9' => 'Speaker\W9Generator',

        # Speaker Expense Form
        'speaker/expense-form' => 'Speaker\ExpenseFormGenerator',

        # Speaker Utilization Report
        'speaker/utilization'                 => 'Speaker\Report\Utilization\UtilizationReport',
        'speaker/annual-utilization'          => 'Speaker\Report\AnnualUtilization\Report',
        'speaker/report/optimization'          => 'Speaker\Report\Optimization\Report',

        # Brand Honorarium Report
        'speaker/payment'                     => 'Speaker\PaymentsReport',

        # Brand Honorarium Report
        'speaker/brand-honorarium'            => 'Speaker\Report\BrandHonorarium\Report',

        # Survey Evaluation Report
        'program/evaluation'                  => 'Programs\Evaluation',

		# Brand Honorarium Report
        'speaker/speaker-honorarium'          => 'Speaker\SpeakerHonorarium',

        # Honorarium Rates Grid
        'grids/speaker-honorarium-rate'       => 'Grids\SpeakerHonorariumRate',

        # Usage Reports
        'usage/business-review' => 'Usage\BusinessReview\Report',
        'usage/compliance' => 'Usage\Report\Compliance\ComplianceReport',
        'usage/field-portal' => 'Usage\FieldPortal\Report',
        'usage/isignin-app'=> 'Usage\IsigninApp\Report',
        'usage/speaker-portal' => 'Usage\SpeakersPortal\Report',

        # Alignment Report
        'field/alignment'                     => 'Field\AlignmentReport',

        # Alignment Report
        'report/field/roster'                 => 'Field\RosterReport',

        # Utilization Report
        'field/utilization'                   => 'Field\UtilizationReport',

        # HCP Spend Report
        'hcp/spend'                           => 'Hcp\SpendReport',

        # Exception Grid Report
        'exception/grid'                      => 'Exceptions\GridReport',

        # Program Reports
        # Deprecated
        # 'program/attendee'                    => 'Programs\AttendeesReport',
        # 'report/programs/management-fee' => 'Programs\ManagementFeeReport',
        # 'report/programs/shipping-grid'  => 'Programs\ShippingGridReport',
        'program/materials-shipping-grid'     => 'Programs\MaterialsShippingGridReport',
        'program/speaker-program-tracker'     => 'Programs\Report\SpeakerProgramTracker\Report',
        'program/close-out-and-reconciled'    => 'Programs\CloseOutAndReconciledReport',
        'program/list'                        => 'Programs\ListReport',


        # Management Fee Report
        'program/management-fee'              => 'Programs\Report\ManagementFee\ManagementFeeReport',

        # Program Attendee Report
        'program/attendee'                    => 'Programs\Report\Attendee\Report',

        # Program Reports
        'program/attendee-hcp'                => 'Programs\Report\AttendeeHcp\Report',

        # Chase Report
        #
        # Return the information on all items that need to be looked at by PMs daily
        'program/report/chase'                => 'Programs\Report\Chase\Report',
        'core/report/chase'                   => 'Core\Report\Chase\Report',

        # Pre-Porzio Report
        'program/report/porzio'               => 'Programs\Report\Porzio\PorzioReport',

        # Task List Report
        'program/task-list'                   => 'Programs\TaskListReport',

        # Brand Dashboard Report
        'management/dashboard'                => 'Management\DashboardReport',

        # HCP Reports

        # Field Reports
        # 'report/rep/alignment'       => 'Rep\AlignmentReport',

        # Grid Reports
        # Version 2.2.0
        'grids/master'                        => 'Grids\Master\Report',

        # Grid Reports
        # Version 2.1
        'grids/cost'  => 'Grids\Cost\Report',

        'grids/core'                          => 'Grids\Core\Report',

        # conference Generators
        'conference/summary' => 'Conference\SummaryGenerator',

        # conference Generators
        'conference/invoicegen' => 'Conference\InvoiceGenerator',
        'conference/invoicegenbulk' => 'Conference\InvoiceGeneratorBulk',

        # Conference Reports
        'transaction/list-report'  => 'Transaction\Lists\Report',

        # Conference schedule Report
        'conference/schedule-list-report' => 'Conference\ScheduleList\Report',

        # Conference nomination Report
        'conference/nomination-list-report' => 'Conference\NominationList\Report',

        # Conference Registration Report
        'conference/registration-list-report' => 'Conference\RegistrationList\Report',

        # Conference  Affiliate Meeting Report
        'conference/affiliate-list-report'    => 'Conference\AffiliateMeetingList\Report',

        # Conference  Housing Report
        'conference/housing-list-report'    => 'Conference\HousingList\Report',

        # Conference Reports
        'conference/chase-report'  => 'Conference\Chase\Report',

		# Conference Reports
        'conference/cost-grid'  	=> 'Conference\CostGridReport',

		# Conference Reports
        'conference/closeout'  		=> 'Conference\CloseoutReport',

        # Conference Invoice Reports
        'conference/invoice'  => 'Conference\Invoice\Report',
        'conference/invoice-preparation'  => 'Conference\InvoicePreparationReport',
         # Invoice Reports
        'invoice/summary'          => 'Invoice\SummaryReport',

        # Financial projection
        'financial-projection'          => 'FinancialProjection\Report',

        # Ticket Reports
        'ticket/master' => 'Ticket\Master\MasterTicketReport',
        # Profile Generators
        'profile/contract/generic'  => 'Profile\Contract\Generic\Generator',
        'profile/debarment/status'  => 'Profile\Debarment\StatusGenerator',
        # Consultant Generator
        'report/consultant/grid'  => 'Consutlant\Grid\Report',
        # Profile Generators
        'report/management/cost/weekly-summary'  => 'Management\Cost\WeeklySummary\Report',
        'engagement/thank-you' => 'Engagement\ThankYou\Generator',
    );

    /**
     * IoC the Class to the generator and call it
     *
     * @param  array  $arguments
     * @return Generator->handle( ) | Generator
     */
    public function make($input = array(), $handle= true)
    {
        $generator = app()->make( $this->matchClass($input) );

        # Auto-handle generator
        return $handle ? $generator->handle( array_get($input, 'arguments', []) ) : $generator;
    }

    /**
     * Return the class name if exists, or throw an exception
     *
     * @param  array $input
     * @return string
     */
    protected function matchClass($input)
    {
        return $this->getNamespacedClass( array_get($this->generators, $this->getKey($input), '') );
    }

    /**
     * Return registered type of generator
     *
     * @param  array $input
     * @return string
     */
    protected function getKey($input, $default = '')
    {
        return array_get( (array)$input, $this->key, $default);
    }

    /**
     * Return namespaced Class
     *
     * @param  string $class
     * @return string
     */
    protected function getNamespacedClass($class)
    {
        return $this->getNamespace().$class;
    }

    /**
     * Return Namespace ending with \
     *
     * @return string
     */
    protected function getNamespace()
    {
        return object_get($this, 'namespace', __NAMESPACE__.'\\Streams\\');
    }
}
