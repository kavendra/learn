<?php

namespace Betta\Models\Observers;

use Betta\Models\CostItem;
use Betta\Models\ProgramInvitation;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramInvitationObserver extends AbstractObserver
{
    /**
     * Cost Item to create and look up
     *
     * @var int
     */
    protected $cost_item_id = CostItem::INVITE;
    protected $brand_id;


    /**
     * Listen to the ProgramInvitation creating event.
     *
     * @param  ProgramInvitation  $model
     * @return void
     */
    public function creating(ProgramInvitation $model)
    {
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }


    /**
     * Listen to the ProgramInvitation saving event.
     *
     * @param  ProgramInvitation  $model
     * @return void
     */
    public function saving(ProgramInvitation $model)
    {
        # Set invitation quatity to nullable field
        $model->setAttribute('invitation_quantity', $model->getAttribute('invitation_quantity') ?: null );

        # Update the invitation cost
        if($model->isDirty('invitation_quantity')){
            $this->brand_id = $model->program->primary_brand_id;
            $this->syncInvitationCost($model);
        }
    }


    /**
     * Sync the Cost
     *
     * @param  ProgramInvitation $model
     * @return Void
     */
    protected function syncInvitationCost(ProgramInvitation $model)
    {
        # Remove the cost if possible
        if ( $model->getAttribute('invitation_quantity') == 0 || in_array($this->brand_id, [1,2])){
            $model->program->costs->whereIn('cost_item_id', $this->syncCostItemIds($model))->each( function($cost){
                # delete the Cost if deletable
                if ($cost->is_deletable){
                    $cost->delete();
                }
            });
        }
        if ( $model->getAttribute('invitation_quantity') == 0){
            return true;
        }
        # insert the updated cost of invitations
        $model->program->costs()->updateOrCreate([
            'cost_item_id' => $this->syncCostItemId($model) ], [
            'estimate' => $this->estimateInvitationCost($model)
        ]);
    }

    /**
     * Cost Item Id
     *
     * @param  ProgramInvitation $model
     * @return Void
     */
    protected function syncCostItemId(ProgramInvitation $model)
    {
        $brand_id = $this->brand_id;


        if($brand_id == 1){
            return $model->KrystexxaInvitationCostId();
        }elseif($brand_id == 2){
            return $model->RayosInvitationCostId();
        }else{
            return $this->cost_item_id;
        }
    }

    /**
     * Cost Item Ids
     *
     * @param  ProgramInvitation $model
     * @return Void
     */
    protected function syncCostItemIds(ProgramInvitation $model)
    {
        $brand_id = $this->brand_id;


        if($brand_id == 1){
            return $model->KrystexxaInvitationCostIds();
        }elseif($brand_id == 2){
            return $model->RayosInvitationCostIds();
        }else{
            return [$this->cost_item_id];
        }
    }

    /**
     * Cost Item cost
     *
     * @param  ProgramInvitation $model
     * @return Void
     */
    protected function estimateInvitationCost(ProgramInvitation $model)
    {
        $brand_id = $this->brand_id;

        if($brand_id == 1){
            $item_id = $model->KrystexxaInvitationCostId();
            return CostItem::find($item_id)->cost;
        }elseif($brand_id == 2){
            $item_id = $model->RayosInvitationCostId();
            return CostItem::find($item_id)->cost;
        }else{
            return $model->estimateInvitationCost();
        }
    }

}
