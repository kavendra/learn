<?php
namespace Betta\Foundation\Mail;

interface CommunicationMapMailableInterface
{

    /**
     * Set the Program Type brand in the array map
     *
     * @return void
     */
    public function setProgramTypeBrandForMap();


    /**
     * Set the Context Model in the array map
     *
     * @return void
     */
    public function setContextModelForMap();


    /**
     * Set the Context status in the array map
     *
     * @return void
     */
    public function setContextStatusForMap();


    /**
     * Prepare the map
     *
     * @return Betta\Model\CommunicationMap
     */
    public function bindCommunication();
}
