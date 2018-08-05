<?php

namespace Betta\Services\HcpRoster;

use Betta\Models\Degree;
use Betta\Models\Profile;
use Betta\Models\Specialty;
use Betta\Foundation\HasMessageBag;

class HcpRosterRow
{
    use HasMessageBag;

    /**
     * Handling class
     *
     * @var Model
     */
    protected $class = Profile::class;

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
        'relations.hcpProfile.degree',
        'title',
        'relations.hcpProfile.specialty',
        'relations.hcpProfile.sln_state',
        'relations.hcpProfile.sln',
        'relations.hcpProfile.npi',
    ];

    /**
     * Raw Attributes
     *
     * @var Array
     */
    protected $raw = [];

    /**
     * Mapped Attributes
     *
     * @var Array
     */
    protected $attributes = [];

    /**
     * List existing Degrees
     *
     * @var Collection
     */
    protected $degrees;

    /**
     * List Existing Specialties
     *
     * @var Collection
     */
    protected $specialties;

    /**
     * Create new Instance of the HcpRosterRow
     *
     * @param array   $array
     */
    public function __construct(HcpRosterModel $source)
    {
        $this->source = $source;
    }

    /**
     * Sync the record
     *
     * @return MessageBag
     */
    public function sync()
    {
        # Locate the Profile
        if($profile = $this->syncProfile()){
            # Define Message bag to notify
            $bag = $profile->wasRecentlyCreated ? 'created' : 'updated';

            # First or create the Address
            $this->syncAddress($profile);

            # Sync the HCP Profile
            $this->syncHcpProfile($profile);

            # Add message
            $this->addMessage($bag, $profile->id);
        }

        # return the messages
        return $this->getMessageBag();
    }

    /**
     * Locate the Profile by CMID, FLS ID or create new, but ONLY if the CMID is present
     *
     * @return Betta\Models\Profile | null
     */
    protected function syncProfile()
    {
        # CMID Profile exists;
        # Let's update the values and save if dirty
        if ($profile = $this->source->profile){
            return $this->syncExistingProfile($profile);
        }
        # Profile BY FLS ID if exists;
        if ($id = $this->source->id){
            if($profile = app( $this->class )->find($id)){
                # try to re-assign the CMID, if provided
                $profile->customer_master_id = $this->source->customer_master_id ?: $profile->customer_master_id;
                # Let's update the values and save if dirty
                return $this->syncExistingProfile($profile);
            }
        }

        # verify the value:
        if(empty($this->source->customer_master_id)){
            # Add error
            $this->addMessage('skipped empty', "Empty customer_master_id [{$this->source}]");
            # return
            return null;
        }

        # create new record
        return app( $this->class )->create([
            'customer_master_id' => $this->source->customer_master_id,
            'first_name' => $this->source->first_name,
            'middle_name' => $this->source->middle_name,
            'last_name' => $this->source->last_name,
        ]);
    }

    /**
     * Update existing Profile
     *
     * @param  Profile $profile
     * @return Profile
     */
    protected function syncExistingProfile(Profile $profile)
    {
        # Profile has speakerProfile: skip update
        if(!empty($profile->speakerProfile)){
            return $profile;
        }

        $profile->fill([
            'first_name' => $this->source->first_name,
            'middle_name' => $this->source->middle_name,
            'last_name' => $this->source->last_name,
        ]);

        # save if dirty
        if ($profile->isDirty()){
            $profile->save();
        }

        return $profile;
    }

    /**
     * Sync the Address Record
     *
     * @param  Profile $profile
     * @return Instance
     */
    protected function syncAddress(Profile $profile)
    {
        $data = $this->get('relations.address');

        # These things change rarely
        $baseFields = ['line_1','city','state_province','postal_code'];

        # These fields may fluctuate
        $mutatableFields = ['location_name', 'email', 'phone'];

        # Get the Address by first searching for it, creating is missing and updating it with new values
        if($address = $profile->getLastAddressWhere(array_only($data, $baseFields))){
            # address exists
            # now, lets see if the data needs to be updated
            $address->fill( array_only($data, $mutatableFields) );

            if( $address->isDirty() ){
                $address->save();
            }

        } else {
            # create new address
            $address = $profile->addresses()->create($data);
        }

        # if the address does not match, update it
        if ( $profile->primary_address_id != $address->id){

            $profile->update([
                'primary_address_id' => $address->id,
            ]);
        }

        return $this;
    }

    /**
     * Sync the HCP Profile
     *
     * @param  Profile $profile
     * @return Instance
     */
    protected function syncHcpProfile(Profile $profile)
    {
        # Skip Profile with existing speakerProfile
        if(!empty($profile->speakerProfile)){
            return $profile;
        }

        $data = $this->get('relations.hcpProfile');

        if($hcp = $profile->hcpProfile){
            # HCP Profile exists
            $hcp->fill($data);
        } else {
            $hcp = $profile->hcpProfile()->create( $data );
        }

        # Compare the values for Specialty: Specialty is not empty AND does not match what's in Profile
        if ( $specialty = $this->get('specialty_id') AND !str_is($hcp->specialty, $specialty) ){
            $specialty = app(Specialty::class)->firstOrCreate( ['label'=>$specialty] );

            # associate
            $hcp->primarySpecialty()->associate($specialty);
        }

        # Compare the values for Degree: Degree is not empty AND does not match what's in Profile
        if ( $professional_degree = $this->get('degree_id', null) AND !str_is($hcp->degree, $professional_degree) ){

            $degree = app(Degree::class)->firstOrCreate( compact('professional_degree') );

            # associate
            $hcp->primaryDegree()->associate($degree);
        }

        # HCP is Dirty - needs saving;
        if($hcp->isDirty()){
            $hcp->save();
        }

        return $this;
    }

    /**
     * Map attributes to an array
     *
     * @return Void
     */
    protected function mapAttributes()
    {
        foreach( $this->map as $position => $key){
            array_set($this->attributes, $key, array_get($this->raw, $position) );
        }
    }

    /**
     * Resolve Item from Attributes
     *
     * @param  string $key
     * @param  array|mixed  $default
     * @return mixed
     */
    protected function get($key, $default = [])
    {
        return $this->source->getOriginal($key) ?: $default;
    }
}
