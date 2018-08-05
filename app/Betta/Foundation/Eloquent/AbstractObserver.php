<?php

namespace Betta\Foundation\Eloquent;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Betta\Foundation\Interfaces\SimulatingUserInterface;

abstract class AbstractObserver implements SimulatingUserInterface
{
    /**
     * List caches that may be flushed away upon saved()
     *
     * @var array
     */
    protected $flushable = [];

    /**
     * Return Default Profile Id
     *
     * @return int
     */
    protected function getDefaultProfileId()
    {
        return config('betta.default_profile_id');
    }

    /**
     * Resolve Current User
     *
     * @return User|null
     */
    protected function getUser()
    {
        return auth()->user();
    }

    /**
     * Resolve Simulating User's Profile
     *
     * @return Profile|null
     */
    protected function getSimulatingUser()
    {
        // Ideally, return Profile
    }

    /**
     * Get the User Id
     *
     * @return int
     */
    protected function getUserId()
    {
        return object_get($this->getUser(), 'profile_id', $this->getDefaultProfileId() );
    }

    /**
     * Get the Simulating User Id
     *
     * @return int
     */
    protected function getSimulatingUserId()
    {
        return session(static::SIMULATING_USER_PROFILE_ID);
    }

    /**
     * Return Now for the models that need it
     *
     * @return Carbon\Carbon
     */
    protected function now()
    {
        return Carbon::now();
    }

    /**
     * Clean up and return numbers only
     *
     * @param  mixed $value
     * @return int
     */
    protected function numbersOnly( $value )
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Ensure the field is unique by runnin a query
     *
     * @todo  Validate uniqueness of the field against the model
     *
     * @param  string $field
     * @param  string $value
     * @return string
     */
    protected function uniqueField($field, $value = '')
    {
        return $value;
    }

    /**
     * Set nullable value into the field
     *
     * @param Model $model
     * @param string $field
     */
    protected function setNullableField($model, $field)
    {
        $value = $model->getAttribute($field);

        # Compare the Value to empty string
        if( $value === ''){
            $value = null;
        }

        $model->setAttribute($field, $value );
    }

    /**
     * Set Current User as Creator
     *
     * @param Model $model
     * @return $this
     */
    protected function setCreator(Model $model)
    {
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId());

        return $this;
    }
}
