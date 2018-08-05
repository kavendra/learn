<?php

namespace App\Models\Eloquent;

use Crypt;
use Illuminate\Database\Eloquent\Model;

class EncryptableModel extends Model
{

    /**
     * Values in this array will stored as encrypted values
     *
     * @var array
     */
    protected $encrypted;


    /**
     * Overload method to get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (array_key_exists($key, array_flip($this->encrypted))){
            return Crypt::decrypt( parent::getAttribute($key) );
            # obtain the value
            $value = parent::getAttribute($key);
            # if the value is too short, just return it without trying to decrypt it
            return  strlen( $value ) > 1 ? Crypt::decrypt( $value ) : $value;
        }
        return parent::getAttribute($key);
    }


    /**
     * Overload method to set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function setAttribute($key, $value)
    {
        if (array_key_exists($key, array_flip($this->encrypted))){
            parent::setAttribute($key, Crypt::encrypt($value));
            return;
        }

        parent::setAttribute($key, $value);
    }


    /**
     * Return protected
     *
     * @return Array
     */
    public function getEncrypted()
    {
        return $this->encrypted;
    }
}
