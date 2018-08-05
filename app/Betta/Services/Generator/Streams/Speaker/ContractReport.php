<?php

namespace Betta\Services\Generator\Streams\Speaker;

use Betta\Models\Brand;
use Betta\Models\Contract;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class ContractReport extends AbstractReport
{
    /**
     * Bind the implementation
     *
     * @var
     */
    protected $excel;

    /**
     * Bind the implementation
     *
     * @var Model
     */
    protected $brand;

    /**
     * Bind the implementation
     *
     * @var Model
     */
    protected $contract;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Speaker Contract Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Speaker Contracts and their information';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = true;

    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        'profile',
        'documents',
        'contractType.brand'
    ];

    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Contract $contract
     * @return Void
     */
    public function __construct(Excel $excel, Brand $brand, Contract $contract)
    {
        $this->excel    = $excel;
        $this->brand    = $brand;
        $this->contract = $contract;
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
					$inBrand = array_get($this->arguments, 'inBrand' );
					$inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id');
                    # For brand build a tab and their contracts
                    foreach($inBrand as $brand_id){
                        # Produce the tab
                        # @todo exctract tabs into their own classes
                        $brand = $this->brand->whereId($brand_id)->first();

                        $excel->sheet($this->sanitizeSheetName($brand->label), function ($sheet) use ($brand) {
                            $sheet->loadView('reports.speaker.contract.report')
                                  ->with('contracts',  $this->filterBrand($this->candidates, $brand) )
                                  ->setAutoFilter()
                                  ->freezeFirstRow()
                                  ->setColumnFormat( $this->getFormats() );
                        });
                    }

                    # Set the includeSql = true to have SQL Printout tab
                    # includeSql should NEVER be true for production reports
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
        return $this->contract->has('profile')->with($this->relations)->get();
    }

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @return array
     */
    public function getFormats()
    {
        return [
            'I' => static::AS_DATE,
			'L' => static::AS_DATE,
            'N' => static::AS_DATE,
            'O:T' => static::AS_CURRENCY,
        ];
    }

    /**
     * Filter the candidates by brand ID
     *
     * @param  Brand      $brand
     * @param  Collection $candidates
     * @return Collection
     */
    protected function filterBrand($candidates, $brand)
    {
        return $candidates->filter(function ($candidates) use ($brand){
            return data_get($candidates, 'contractType.brand_id') == $brand->id;
        });
    }
}
