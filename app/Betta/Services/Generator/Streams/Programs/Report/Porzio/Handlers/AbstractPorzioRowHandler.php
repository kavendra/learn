<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Porzio\Handlers;

use Betta\Services\Generator\Foundation\AbstratRowHandler;

abstract class AbstractPorzioRowHandler extends AbstratRowHandler
{
    /**
     * List Brands \ Products
     */
    use Brands;

    /**
     * Offload empty and non-critical items to a shared Trait
     */
    use SharedValues;

    /**
     * Extract Location
     *
     * @uses Betta\Models\Address
     */
    use SpendLocation;

    /**
     * Empty values that are reserves for all
     */
    use ReservedValues;

    /**
     * Decide who is the Recipient:
     *
     * @uses Betta\Models\Profile
     * @uses Betta\Models\Registration
     */
    use Recipient;

    /**
     * Display Field information
     *
     * @uses Betta\Models\Profile
     */
    use Field;

    /**
     * Locate where the Address is stored, and return values from it
     *
     * @uses Betta\Models\Address
     * @uses Betta\Models\Registration
     */
    use RecipientAddress;

    /**
     * Offshore Costs that are not tracked in application
     */
    use OffshoreCosts;

    /**
     * Offload Material Counts
     *
     */
    use Materials;

    /**
     * Offload Third Party
     *
     */
    use ThirdParty;

    /**
     * Offload Third Party
     *
     */
    use Payment;

    /**
     * Reportable items
     *
     * @see CommonPorzioRowTrait
     * @var Array
     */
    protected $keys = [
        'Transaction Type',
        'Spend Source Code',
        'Event Id',
        'Expense Id',
        'Spend Date',
        'Spend Entry Date',
        'Spend Location Or Destination Name',
        'Spend Location Or Destination Address 1',
        'Spend Location Or Destination Address 2',
        'Spend Location Or Destination City',
        'Spend Location Or Destination State',
        'Spend Location Or Destination Zip Code',
        'Spend Location Or Destination Zip Code Ext',
        'Spend Location Or Destination Type',
        'Spend Product 1 Or Therapeutic Area',
        'Spend Product 2 Or Therapeutic Area',
        'Spend Product 3 Or Therapeutic Area',
        'Spend Product 4 Or Therapeutic Area',
        'Spend Product 5 Or Therapeutic Area',
        'Spend Purpose Primary',
        'Spend Purpose Secondary',
        'Spend Nature Or Type',
        'Spend Payment Method',
        'Spend Amount Pro Rata',
        'Spend Amount Total Cost',
        'Reserved For Future Use 1',
        'Attendees Client Employees',
        'Total Recipients',
        'Sales Rep Id',
        'Sales Rep Last Name',
        'Sales Rep First Name',
        'Recipient Number',
        'Recipient License State',
        'Recipient State License Number',
        'Recipient Dea Number',
        'Reserved For Future Use2',
        'Recipient Last Name',
        'Recipient First Name',
        'Recipient Middle Name',
        'Recipient Title',
        'Recipient Suffix Name',
        'Recipient Designation',
        'Recipient Specialty',
        'Recipient Type',
        'Recipient Territory',
        'Recipient Affiliated Institution Name',
        'Recipient Address Line 1',
        'Recipient Address Line 2',
        'Recipient City',
        'Recipient State',
        'Recipient Zip Code',
        'Recipient Zip Code Ext',
        'Recipient Phone Number',
        'Recipient Fax Number',
        'Recipient Email',
        'Comments',
        'Reserved For Future Use3',
        'Single Recipient Indicator',
        'Vendor',
        'Customer Expense Report Name',
        'Recipient Npi Number',
        'Recipient Country',
        'Spend Location Or Destination Country',
        'Recipient Address Line 3',
        'Non-Us Spend Total Cost',
        'Non-Us Currency Id',
        'Non-Us Conversion Rate',
        'Detail Flex Field 1',
        'Detail Flex Field 2',
        'Detail Flex Field 3',
        'Detail Flex Field 4',
        'Detail Flex Field 5',
        'Detail Flex Field 6',
        'Recipient Me Number',
        'Tax Id Number',
        'Reserved For Future Use4',
        'Reserved For Future Use5',
        'Reserved For Future Use6',
        'Reserved For Future Use7',
        'Company Code',
        'No Show Did Not Consume Count',
        'Spend Owner',
        'Payment Contextual Information',
        'Physician Ownership Indicator',
        'Third Party Payee Type',
        'Third Party Payee Name',
        'Educational Reference Material Item Name',
        'Quantity Of Educational Reference Material',
        'Unit Cost Per Educational Reference Material',
        'Detail Flex Field 7',
        'Detail Flex Field 8',
        'Detail Flex Field 9',
        'Detail Flex Field 10',
        'Header Flex Field 1',
        'Header Flex Field 2',
        'Header Flex Field 3',
        'Header Flex Field 4',
        'Header Flex Field 5',
        'Header Flex Field 6',
        'Header Flex Field 7',
        'Header Flex Field 8',
        'Header Flex Field 9',
        'Header Flex Field 10',
        'Number Of Payments Reflected',
        'Reserved For Future Use8',
    ];

    /**
     * Uniform Date format
     *
     * @var string
     */
    protected $dateFormat = 'Ymd';
}
