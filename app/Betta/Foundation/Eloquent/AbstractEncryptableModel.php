<?php

namespace Betta\Foundation\Eloquent;

use Crypt;
use Betta\Foundation\Eloquent\AbstractModel;

abstract class AbstractEncryptableModel extends AbstractModel
{
    /**
     * Values in this array will stored as encrypted values
     *
     * @var array
     */
    protected $encrypted = [];


    /**
     * Return protected
     *
     * @return array
     */
    public function getEncrypted()
    {
        return $this->encrypted;
    }

    /**
     * Overload method to get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return  $this->isEncrypted($key)
                ? $this->decryptAttribute($key)
                : parent::getAttribute($key);
    }


    /**
     * Overload method to set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return Void
     */
    public function setAttribute($key, $value)
    {
        $this->isEncrypted($key) ? $this->encryptAttribute($key, $value) : parent::setAttribute($key, $value);
    }


    /**
     * True is the element is encrypted
     *
     * @param  string $key
     * @return boolean
     */
    public function isEncrypted($key)
    {
        return array_key_exists($key, array_flip($this->encrypted));
    }


    /**
     * Apply the mutations and decrypt
     *
     * @param  string $key
     * @return string
     */
    protected function decryptAttribute($key)
    {
        return Crypt::decrypt( parent::getAttribute($key) );
    }


    /**
     * Encrypt the value
     *
     * @param  string $key
     * @return Void
     */
    protected function encryptAttribute($key, $value)
    {
        parent::setAttribute($key, Crypt::encrypt($value));
    }
}
