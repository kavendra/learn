<?php

namespace Betta\Foundation\Mail;

use App\Mail\CanvassMailable;
use Illuminate\Support\Collection;
use Betta\Models\CommunicationMap;
use Betta\Services\Canvass\Canvass;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractCanvassEmail implements CommunicationMapMailableInterface
{

    /**
     * Bind the implementation
     *
     * @var string
     */
    protected $fromContext;


    /**
     * Bind the implementation
     *
     * @var Illuminate\Support\Collection
     */
    protected $toContext;


    /**
     * Communication Map ID
     *
     * @var Betta\Models\CommunicationMap
     */
    protected $map;


    /**
     * Conditions to get map.
     * The conditions should give the column_names as key and value as value for the column
     *
     * @var array
     */
    protected $array_map = [];


    /**
     * Array used to build the canvass
     *
     * @var array
     */
    private $data;


    /**
     * Construction.
     * Any Collection. Bring any collection
     *
     * @param Illuminate\Support\Collection $toContext
     */
    public function __construct(Collection $toContext, string $fromContext)
    {
        $this->fromContext  = $fromContext;
        $this->toContext    = $toContext;

        # Prepare the Map
        $this->setProgramTypeBrandForMap();
        $this->setContextModelForMap();
        $this->setContextStatusForMap();

        $this->handle();
    }

    /**
     * Handle the build
     *
     * @return Void
     */
    protected function handle()
    {
        # If we do not have any array map elements return
        if(empty($this->array_map))
            return;

        # Prepare the Communication
        $this->bindCommunication();

        # Build the template if map is available
        if(!$this->map)
            return;
        $this->build();
    }


    /**
     * Prepare the map
     *
     * @return Betta\Model\CommunicationMap
     */
    public function bindCommunication()
    {
        # Iterate through each key value pair and apply filters
        $this->map = CommunicationMap::where(function($query){
                foreach ($this->array_map as $key => $value) {
                    $query->where($key,'=',$value);
                }
            })
            ->with('communicationTemplate')
            ->first();
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    protected function build()
    {
        $this->toContext->each(function($context){
            # Make the scaffolding of the email
            # Get the email
            $template = $this->buildCanvass($context);

            # Prepare for Sending
            $email = new CanvassMailable($template);


            # Build the email whose (sender) intiating from
            if($this->buildFrom())
            $email->from($this->buildFrom());

            # Send email
            Mail::send($email);
        });
    }


    /**
     * Build the email
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
            } else if($generated){
                $template->attach( object_get($generated, 'path'), ['as'=> object_get($generated, 'file')]);
            }
        }

        # Add recipients
        $template->addRecipient($this->getRecipients($model));

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


    /**
     * Build the from whom the email should initaite
     *
     * @return Void
     */
    public function buildFrom()
    {
        return;
    }
}
