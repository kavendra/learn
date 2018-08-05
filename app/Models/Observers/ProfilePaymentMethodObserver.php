<?php

namespace Betta\Models\Observers;

use Betta\Models\ProfilePaymentMethod;
use App\Events\Profile\PaymentMethodUpdated;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProfilePaymentMethodObserver extends AbstractObserver
{
    /**
     * Keep track on these
     *
     * @var Array
     */
    protected $observable = [
        'payment_method',
        'account_label',
        'account_routing',
        'account_number',
        'account_type'
    ];

    /**
     * Listen to the ProfilePaymentMethod creating event.
     *
     * @param  ProfilePaymentMethod  $model
     * @return void
     */
    public function creating(ProfilePaymentMethod $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProfilePaymentMethod saving event.
     *
     * @param  ProfilePaymentMethod  $model
     * @return void
     */
    public function saving(ProfilePaymentMethod $model)
    {
        # Clean up the Direct Deposit in case of a check
        $this->cleanUpDirectDepositInfo($model);
    }

    /**
     * Listen to the ProfilePaymentMethod saved event.
     *
     * @param  ProfilePaymentMethod  $model
     * @return void
     */
    public function saved(ProfilePaymentMethod $model)
    {
        # Fire the event to notify the change of payment method
        if($model->isDirty($this->observable)){
            event(new PaymentMethodUpdated($model));
        }
    }

    /**
     * In case our PaymentMethod is Checking, remove the DirectDeposit Information
     *
     * @param  ProfilePaymentMethod $model
     * @return Void
     */
    protected function cleanUpDirectDepositInfo(ProfilePaymentMethod $model)
    {
        $nullable = [
            'account_label',
            'account_routing',
            'account_number',
            'account_type',
        ];

        if($model->is_check_method){
            foreach($nullable as $field){
                $model->setAttribute($field, null);
            }
        }
    }
}
