<?php

namespace Betta\Services\Generator\Handlers;

use Illuminate\Filesystem\Filesystem;
use Betta\Services\Generator\Drivers\WordTemplate;
use Betta\Services\Generator\Handlers\TemplateProcessor;

class AcumuneInvitationHandler extends WordTemplate
{
    /**
     * Replace processor with extended version, implementation of
     *
     * @var \PhpOffice\PhpWord\TemplateProcessor
     */
    protected $processor = TemplateProcessor::class;

    /**
     * Merge the Word Document Template as Path and Save it to saveAs
     *
     * @param  string $path
     * @param  array  $data
     * @param  string $saveAs
     * @return Instsance
     */
    public function merge($data = array(), $offset = null)
    {
        # do the merge via parent
        parent::merge($data);

        # Additional replacings
        $this->replaceMapPlaceholder($data);

        $this->replaceLinkPlaceholder($data);

        # return
        return $this;
    }

    /**
     * Replace the image
     *
     * @param  Array $data
     * @return Void
     */
    protected function replaceMapPlaceholder($data)
    {
        if( $remote = data_get($data, 'LOCATION_MAP') AND $path = $this->getFile($remote)){
            $this->template->setImageValue( 'image1.png', $path);
        }
    }


    /**
     * Replace the link placeholder
     *
     * @param  Array $data
     * @return Void
     */
    protected function replaceLinkPlaceholder($data)
    {
        if($link = data_get($data, 'LOCATION_URL')){
            $this->template->setLinkValue('LOCATION_URL', $link);
        }
    }

    /**
     * Try to fetch file and save it at storage path
     *
     * @param  string $path
     * @return string
     */
    protected function getFile($path)
    {
        $name = $this->makeName($path);

        # if we fetched it already once
        if ( $this->filesystem->exists($name) ){
            return $name;
        }

        # try to fetch it
        if ($contents = file_get_contents($path)){
            # save
            $this->filesystem->put($name, $contents);
            # return local path to file
            return $name;
        }

        return false;
    }

    /**
     * Make name for the file;
     *
     * @param  string $path
     * @return string
     */
    protected function makeName($path)
    {
        $name = md5($path);

        return "{$this->storage_path}/{$name}.png";
    }
}
