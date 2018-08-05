<?php

namespace Betta\Services\HcpRoster;

use Betta\Foundation\HasMessageBag;

class HcpRosterChunk
{
    use HasMessageBag;

    /**
     * Handling class
     *
     * @var Model
     */
    protected $handler = HcpRosterModel::class;

    /**
     * Map of the headers
     *
     * @var array
     */
    protected $map = [
        'id',
        'customer_master_id',
        'first_name',
        'middle_name',
        'last_name',
        'relations.address.location_name',
        'relations.address.line_1',
        'relations.address.city',
        'relations.address.state_province',
        'relations.address.postal_code',
        'relations.address.phone',
        'relations.address.email',
        'degree_id',
        'title',
        'specialty_id',
        'relations.hcpProfile.sln_state',
        'relations.hcpProfile.sln',
        'relations.hcpProfile.npi',
    ];

    /**
     * Processed rows
     *
     * @var Array of Profiles
     */
    protected $items = [];

    /**
     * Eager Load these realtions
     *
     * @var Array
     */
    protected $relations = [
        'degree',
        'specialty',
        'profile.addresses',
        'profile.hcpProfile',
        'profile.speakerProfile',
    ];

    /**
     * Create class instance
     * We will load-hydrate the Model using the Customer Master ID
     * The Row will recognize if the record does not exist and will create it
     *
     * @param array   $array
     */
    public function __construct($array)
    {
        $this->items = $this->hydrate($array);
    }

    /**
     * Sync the record
     *
     * @return MessageBag
     */
    public function sync()
    {
        foreach($this->items as $hcpRosterModel){
            # Sync
            $result = with( new HcpRosterRow($hcpRosterModel) )->sync();
            # Merge results
            $this->getMessageBag()->merge( $result );
        }

        # Return resutls
        return $this->getMessageBag();
    }

    /**
     * Load the Profiles
     *
     * @return Collection
     */
    protected function hydrate($array)
    {
        $items = [];

        foreach( $array as $item){
            $items[] = $this->mapAttributes($item);
        }

        return app( $this->handler )->hydrate($items)->load( $this->relations );
    }

    /**
     * Map attributes to an array
     *
     * @param  array $raw
     * @return Void
     */
    protected function mapAttributes($raw)
    {
        $record = [];

        foreach( $this->map as $position => $key){
            array_set($record, $key, array_get($raw, $position) );
        }

        return $record;
    }
}
