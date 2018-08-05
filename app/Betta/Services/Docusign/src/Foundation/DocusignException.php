<?php

namespace Betta\Docusign\Foundation;

use Exception;

abstract class DocusignException extends Exception
{
    /**
     * @var string
     */
    protected $id;


    /**
     * @var string
     */
    protected $status;


    /**
     * @var string
     */
    protected $title;


    /**
     * @var string
     */
    protected $detail;


    /**
     * @param @string $message
     * @return void
     */
    public function __construct( $message )
    {
        parent::__construct( $message );
    }


    /**
     * Get the status
     *
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->status;
    }

    /**
     * Return the Exception as an array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id'     => $this->id,
            'status' => $this->status,
            'title'  => $this->title,
            'detail' => $this->detail
        ];
    }

    /**
     * Build the Exception
     *
     * @param array $args
     * @return string
     */
    protected function build(array $args)
    {
        # Make shte first argument the ID
        $this->id     = array_get($args, 'id',     $this->id);
        $this->title  = array_get($args, 'title',  $this->title);
        $this->detail = array_get($args, 'detail', $this->detail);
        $this->status = array_get($args, 'status', $this->status);

        $this->detail = $this->detail ?: head($args);
        $this->status = $this->status ?: last($args);

        return $this->detail;
    }
}
