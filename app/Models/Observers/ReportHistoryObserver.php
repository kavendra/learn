<?php

namespace App\Models\Observers;

use App\Models\ReportHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class ReportHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the ReportHistory creating event.
     *
     * @param  ReportHistory  $model
     * @return void
     */
    public function creating(ReportHistory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ReportHistory created event.
     *
     * @param  ReportHistory  $model
     * @return void
     */
    public function created(ReportHistory $model)
    {
        if ($model->report_uri){
            //$this->attachDocument($model);
        }
    }


    /**
     * Add the Document to the history
     *
     * @param  ReportHistory $model
     * @return Void
     */
    protected function attachDocument(ReportHistory $model)
    {
        $model->documents()->create([
            'label'         => $model->report_name,
            'original_name' => $model->report_name,
            'file_name'     => $model->report_name,
            'uri'           => $model->report_uri,
        ]);
    }
}
