<?php

namespace Betta\Services\FieldRoster;

use Carbon\Carbon;
use Betta\Models\Profile;
use Betta\Models\Territory;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Collections\CellCollection;
use Betta\Foundation\Rosterize\AbstractRosterFeed;

class FieldRosterFile extends AbstractRosterFeed
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Profile
     */
    protected $profile;

    /**
     * Bind implementation
     *
     * @var Maatwebsite\Excel\Excel
     */
    protected $excel;


    /**
     * Number of rows to skip
     * @var integer
     */
    protected $skipRows = 10;


    /**
     * Tab name to have the
     *
     * @var string
     */
    protected $tabName = 'Roster';


    /**
     * Today, Start of Day
     *
     * @var string
     */
    protected $today = 'Roster';


    /**
     * Create new instance of the
     *
     * @param Profile $profile
     */
    public function __construct(Excel $excel, Profile $profile, Territory $territory)
    {
        $this->excel  = $excel;
        $this->profile  = $profile;
        $this->territory  = $territory;
        $this->today = Carbon::now()->startOfDay();
    }


    /**
     * Run the Roster File
     *
     * @return Collection
     */
    public function run()
    {
        if(!$roster = $this->getRosterTab() ){
            $this->addError('Roster File does not contain the `Roster` tab');
        } else {
            $this->processRoster($roster);
        }
        return $this->getResults();
    }


    /**
     * Return the Roster Tab
     *
     * @return ExcelSheet
     */
    protected function getRosterTab()
    {
        config()->set('excel.import.startRow', $this->skipRows);

        return $this->excel->selectSheets( $this->tabName )->load( $this->getFile() )->get()->first();
    }


    /**
     * Process the Roster
     *
     * @param  Sheet $roster
     * @return Void
     */
    protected function processRoster($roster)
    {
        $roster->reject(function($row){
            # these are possibly vacat, so we can work with them too;
            return empty($row->id);
        })->each(function($row){
            # sync Profile
            $profile = $this->syncProfile($row);

            # sync Address
            $this->syncAddress($profile, $row);

            # sync extended profile
            $this->syncRepProfile($profile, $row);

            # sync user record
            $this->syncUser($profile, $row);

            # sync territory
            # Possibly if the territory will hold the permission groups in array or relation, we can auto-populate them
            $this->syncTerritory($profile, $row);

            # sync brands
            $this->syncBrands($profile, $row);

            # Sync Groups
            $this->syncGroups($profile, $row);
        });
    }


    /**
     * Sync Profile Data
     *
     * @param  Maatwebsite\Excel\Collections\CellCollection $row
     * @return Betta\Models\Profile
     */
    protected function syncProfile(CellCollection $row)
    {
        return $this->profile->firstOrCreate(['customer_master_id' => $row->id], $row->only( $this->profile->getFillable() )->all() );
    }


    /**
     * Review the Address and if necessary, update it
     *
     * @param  Profile        $profile
     * @param  CellCollection $row
     * @return instance
     */
    protected function syncAddress(Profile $profile, CellCollection $row)
    {
        # These things change rarely
        $baseAddressFields = [
            'line_1'         => $row->address ?: '',
            'city'           => $row->city ?: '',
            'state_province' => $row->state ?: '',
            'postal_code'    => $row->zip ?: '',
        ];

        # These fields may fluctuate
        $mutatableAddressFields = [
            'email'      => $row->horizon_email,
            'cell_phone' => $row->cell_phone ?: '',
            'phone'      => $row->office_phone ?: '',
            'fax'        => $row->fax_number ?: '',
        ];

        # Get the Address by first searching for it, creating is missing and updating it with new values
        $address = $profile->addresses()->updateOrCreate($baseAddressFields, $mutatableAddressFields);

        # Reset the Primary Address to the created one
        $profile->update(['primary_address_id' =>$address->id]);

        return $this;
    }


    /**
     * Sync the Rep Profile Data and if necessary, update
     *
     * @param  Profile        $profile
     * @param  CellCollection $row
     * @return instance
     */
    protected function syncRepProfile(Profile $profile, CellCollection $row)
    {
        # These things change rarely
        $baseFields = ['profile_id' => $profile->profile_id ];

        # These fields may fluctuate
        $mutatableAddressFields = [
            # 'primary_email' => app()->environment('production') ? $row->horizon_email : "{$row->horizon_email}.dev",
            'primary_email' => "{$row->horizon_email}.dev",
            'primary_phone' => $row->cell_phone ?: $row->office_phone,
            'title' => $row->job_title,
        ];

        # Mutate Rep Profile
        if ($repProfile = $profile->repProfile()->first()){
            $repProfile->update($mutatableAddressFields);
        } else {
            $profile->repProfile()->create($baseFields + $mutatableAddressFields);
        }

        return $this;
    }


    /**
     * Sync Territory
     *
     * @param  Profile        $profile
     * @param  CellCollection $row
     * @return instance
     */
    protected function syncTerritory(Profile $profile, CellCollection $row)
    {
        # Is someone else has territory now, we need to stop the relation
        # 1. We need the territory
        $territory = $this->territory->active()->whereAccountTerritoryId( $row->number )->first();

        if (!$territory) {
            # Push error
            $this->addError("{$row->number} does not exist, {$row->preferred_name} not updated");

            return $this;
        }

        # Check if the profile is in the valid primaryProfile list for the territory
        # Re-get the territories
        $profile->territories()->get();

        # Territory is present but in not aligned to profile
        if (object_get($profile->territory, 'account_territory_id') != $row->number){
            # attach the territory
            $profile->territories()->attach($territory, ['valid_from' => $this->today]);
            # add Info
            $this->addinfo("{$row->number} does not exist, {$row->preferred_name} not updated");
        }

        # Detach all other territories

        return $this;
    }


    /**
     * Sync Brands
     *
     * @param  Profile        $profile
     * @param  CellCollection $row
     * @return instance
     */
    protected function syncBrands(Profile $profile, CellCollection $row)
    {
        if ( !$profile->territory ){
            # Push error
            $this->addError("Did not sync brands for {$row->preferred_name} (Reason: Missing Territory)");

            return $this;
        }

        $brands = $profile->territory->brands->keyBy('id')->map(function($brand){
            return array('is_primary'=> $brand->pivot->is_primary);
        });

        # Sync the brands with detaching
        $sync = $profile->brands()->sync( $brands->all() );

        return $this;
    }


    /**
     * Sync Groups
     *
     * @param  Profile        $profile
     * @param  CellCollection $row
     * @return instance
     */
    protected function syncGroups(Profile $profile, CellCollection $row)
    {
        if ( !$profile->territory ){
            # Push error
            $this->addError("Did not sync brands for {$row->preferred_name} (Reason: Missing Territory)");

            return $this;
        }

        $profileGroups = object_get($profile->territory, 'profileGroups', collect([]));

        # Sync the brands with detaching
        $sync = $profile->groups()->sync( $profileGroups );

        return $this;
    }


    /**
     * Sync the User
     *
     * @param  Profile        $profile
     * @param  CellCollection $row
     * @return Instnce
     */
    protected function syncUser(Profile $profile, CellCollection $row)
    {
        # We need to verify the user doesn't have the account
        # Also we possibly need to verify someone else doesn't have it
        # $username = app()->environment('production') ? $row->horizon_email : "{$row->horizon_email}.dev";
        $username = "{$row->horizon_email}.dev";

        $exists = $profile->user()->getModel()->whereUsername($username)
                          ->where('profile_id', '!=', $profile->id)->count();

        # No user?
        if ( empty($profile->user) ){
            # Create New, add standard password
            $profile->user()->create([
                'username' => $username,
                'password' => bcrypt(config('betta.default_password')),
            ]);
        } else {
            # Update username
            $profile->user->update(compact('username'));
        }
    }
}
