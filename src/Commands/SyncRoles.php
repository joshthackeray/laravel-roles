<?php

namespace JoshThackeray\Roles\Commands;

use JoshThackeray\Roles\Models\Role;
use Illuminate\Console\Command;

class SyncRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'joshthackeray:sync-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs the database with roles defined in the config';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $definitionsConfig = config('joshthackeray.roles.definitions');
        $childrenConfig = config('joshthackeray.roles.children');

        //Creating/updating any roles defined in config
        foreach($definitionsConfig as $name => $definition)
            Role::updateOrCreate(
                ['name' => $name],
                [
                    'label' => $definition['label'],
                    'description' => $definition['description'],
                    'status' => Role::STATUS_ACTIVE
                ]);

        $roles = Role::all();
        foreach($roles as $role) {

            //If this role doesn't exist in the config, set this to inactive
            if(!isset($definitionsConfig[$role->name]))
                $role->update(['status' => Role::STATUS_INACTIVE]);

            //If there are no children defined, remove any existing
            if(!isset($childrenConfig[$role->name])) {
                $role->roles()->detach();
            } else {
                //Else, add for each child
                $children = Role::whereIn('name', $childrenConfig[$role->name])->get();
                $toSync = [];
                foreach($children as $child)
                    $toSync[$child->id] = ['relation_type' => Role::class];
                $role->roles()->sync($toSync);
            }
        }
    }
}
