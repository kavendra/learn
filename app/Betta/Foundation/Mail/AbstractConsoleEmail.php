<?php

namespace Betta\Foundation\Mail;

use App\Mail\CanvassMailable;
use Illuminate\Support\Collection;
use Betta\Models\CommunicationMap;
use Betta\Services\Canvass\Canvass;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractConsoleEmail
{

    /**
     * Bind the implementation
     *
     * @var Illuminate\Support\Collection
     */
    protected $contexts;


    /**
     * Communication Map ID
     *
     * @var integer|Betta\Models\CommunicationMap
     */
    protected $map;


    /**
     * Array used to build the canvass
     *
     * @var array
     */
    private $data;


    /**
     * Construction.
     * Any Collection. Bring out the bug guns
     *
     * @param Illuminate\Support\Collection $contexts
     */
    public function __construct(Collection $contexts)
    {
        $this->contexts = $contexts;

        # Prepare the Communication
        $this->bindCommunication();

        # Build the template
        $this->build();
    }


    /**
     * Prepare the map
     *
     * @return Betta\Model\CommunicationMap
     */
    protected function bindCommunication()
    {
        $this->map = CommunicationMap::findOrFail($this->map)->load('communicationTemplate');
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    protected function build()
    {
        $this->contexts->each(function($context){
            # Make the scaffolding of the email
            # Get the email
            $template = $this->buildCanvass($context);

            # Prepare for Sending
            $email = new CanvassMailable($template);

            # Send email
            Mail::send($email);
        });
    }


    /**
     * Build the email
     * @todo  Refactor so that the Canvass can take genric values
     *
     * @param Illuminate\Database\Eloquent\Model $model$attributes
     * @return Canvass | null
     */
    protected function buildCanvass(Model $model)
    {
        $template = new Canvass($this->map->communicationTemplate, $this->setDataEachCanvass($model));

        # Attach the Attachments
        foreach($this->map->attachments as $key => $attachment){
            # Generated files
            # but Generated can return a collection of files
            $generated = $attachment->handle($this->setDataEachCanvass($model));
            # Attach the generated file
            if( $generated instanceOf Collection ){
                foreach($generated as $file){
                    $template->attach( object_get($file, 'path'), ['as'=> object_get($file, 'file')]);
                }
            } else {
                $template->attach( object_get($generated, 'path'), ['as'=> object_get($generated, 'file')]);
            }
        }

        # Add recipients
        $template->addRecipient($this->getRecipients($model));

        if(count($this->getCcs($model))>0){
            foreach ($this->getCcs($model) as $cc) {
                $template->cc($cc);
            }
        }
        return $template;
    }


    /**
     * Set the attributes that need to be sent to the canvass
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    abstract protected function setDataEachCanvass(Model $model);


    /**
     * Get the recipients of the email from the models context
     * This method can be overridden
     *
     * @param  Model  $model
     * @return mixed
     */
    public function getRecipients(Model $model)
    {
        return $model->email;
    }

    public function getCcs(Model $model)
    {
        return [];
    }
}
