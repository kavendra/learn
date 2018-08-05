<?php

namespace Betta\Foundation\Listeners;

use Carbon\Carbon;
use Betta\Models\Contract;
use Betta\Foundation\Events\AbstractContractEvent;

abstract class AbstractContractListener extends AbstractBettaListener
{
    
    /**
     * Bind the implementation
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $contract;

    /**
     * Set the Nomination and
     *
     * @param  AbstractContractEvent  $event
     * @return void
     */
    public function handle(AbstractContractEvent $event)
    {
        # Set the Program for the reuse
        $this->setModel($event->contract);
        # Dimsiss Alerts
        $this->dismiss($event);
        $this->setContract($event->contract)->run();
        
        
    }

    /**
     * Put all the necessary logic into the run section
     *
     * @return Void
     */
    abstract protected function run();

    /**
     * Notify the system the Nomination has been approved
     *
     * @return Void
     */
    protected function notifySystem()
    {
        # We need to build a robust notifier
        $recipient = config('fls.system_email');
    }

    /**
     * Set the Contract
     *
     * @param Contract $contract
     * @return  Instance
     */
    protected function setContract(Contract $contract)
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * Access Contract record
     *
     * @return Contract
     */
    protected function getContract()
    {
        return $this->contract;
    }

    /**
     * Return Now expressed as Carbon
     *
     * @return Carbon\Carbon
     */
    protected function now()
    {
        return Carbon::now();
    }
}
