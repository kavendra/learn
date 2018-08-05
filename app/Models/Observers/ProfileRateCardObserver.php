<?php

namespace Betta\Models\Observers;

use Betta\Models\ProfileRateCard;
use App\Events\Request\CheckRequest;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProfileRateCardObserver extends AbstractObserver
{
    /**
     * Listen to the ProfileRateCard creating event.
     *
     * @param  ProfileRateCard  $model
     * @return void
     */
    public function creating(ProfileRateCard $model)
    {
        $this->setCreator($model);
        # Set label
        $model->setAttribute('label', $this->label($model));
    }

    /**
     * Listen to the ProfileRateCard updated event.
     *
     * @param  ProfileRateCard  $model
     * @return void
     */
    public function updated(ProfileRateCard $model)
    {
        # load the necessary relations
        $model->load(['rates','request']);
        # if the model is complete
        if($model->is_complete AND $model->request){
            event(new CheckRequest($model->request));
        }
    }

    /**
     * Get the label to store rate card with
     *
     * @param  ProfileRateCard  $model
     * @return string
     */
    protected function label(ProfileRateCard $model)
    {
        return $model->getAttribute('label') ?: $this->createLabel($model);
    }

    /**
     * Make Label from values
     *
     * @param  ProfileRateCard $model
     * @return string
     */
    protected function createLabel(ProfileRateCard $model)
    {
        return data_get($model, 'brand.label', 'Contract') . " ($model->period)";
    }
}
