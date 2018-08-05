<?php

namespace Betta\Services\Generator\Drivers;

use PHPExcel_Settings;
use PHPExcel_IOFactory;
use Illuminate\Filesystem\Filesystem;
use Betta\Services\Generator\Interfaces\TemplateInterface;

class ExcelTemplate implements TemplateInterface
{
    /**
     * Result could be converted to Document
     */
    use DocumentTrait;

    /**
     * Share the Tempalte
     * @var TemplateProcessor
     */
    private $template;

    /**
     * Resulting Path
     *
     * @var string
     */
    public $path;

    /**
     * Nice Name of the file
     *
     * @var string
     */
    public $file;

    /**
     * File stream
     *
     * @var string
     */
    public $stream;

    /**
     * Class Constructor
     *
     * @param string $path
     */
    public function __construct($path)
    {
        # Use PCLZip rather than ZipArchive to read the Excel2007 OfficeOpenXML file
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);

        $this->template = PHPExcel_IOFactory::load($path);
    }

    /**
     * Return the PhpExcel
     *
     * @return PhpExcel
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Merge the Word Document Template as Path and Save it to saveAs
     *
     * @param  string $path
     * @param  array  $data
     * @param  string $saveAs
     * @return
     */
    public function merge($data = array())
    {
        foreach ($data as $sheetName => $sheet) {
            $activeSheet = $this->template->getSheetByName($sheetName);
            foreach ($sheet as $cell => $value) {
                $activeSheet->setCellValue($cell, $value);
            }
        }

        return $this;
    }

    /**
     * Save the template somewhere
     * @param  string $saveAs
     * @return Object
     */
    public function save($saveAs, $format = 'Excel2007')
    {
        $objWriter = PHPExcel_IOFactory::createWriter($this->template, $format);

        $objWriter->save($saveAs);

        $this->finalize($saveAs)

        return $this;

        #return (object)[
        #    'path'   => $saveAs,
        #    'file'   => $saveAs without md5,
        #    'stream' => $this->filesystem->get($saveAs)
        #];
    }

    /**
     * Finalize the properties
     *
     * @param  string $saveAs
     * @return Void
     */
    protected function finalize($saveAs)
    {
        # assign as properties
        $this->path   = $saveAs;

        # Nice Name of the file
        $this->file   = $this->getDocumentLabel();

        # Steam
        $this->stream = $this->filesystem->get($saveAs);
    }
}
