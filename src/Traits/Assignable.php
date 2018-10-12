<?php

namespace JoshThackeray\Roles\Traits;

use JoshThackeray\Roles\Exceptions\RoleNotFoundException;
use JoshThackeray\Roles\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait Assignable
{
    /**
     * Returns a collection of Role models related to the parent object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class,  'role_relations', 'relation_id', 'role_id','')
            ->withTimestamps()
            ->where('status', Role::STATUS_ACTIVE)
            ->wherePivot('relation_type', self::class);
    }

    /**
     * Searches by name and label to return a Role model.
     *
     * @param $value
     * @return mixed
     * @throws RoleNotFoundException
     */
    private function roleSearch($value)
    {
        try {
            return Role::where('status', Role::STATUS_ACTIVE)->where(function ($query) use ($value) {
                $query->where('name', $value)
                    ->orWhere('id', $value);
            })->firstOrFail();
        } catch (ModelNotFoundException $e) {
            //The role doesn't exist, return an \JoshThackeray\Roles\Exceptions\RoleNotFoundException
            throw new RoleNotFoundException("Role could not be find.");
        }
    }


    /**
     * Returns whether or not the current object has the given role.
     *
     * @param string|integer|Role $search
     * @return bool
     * @throws RoleNotFoundException
     */
    public function hasRole($search)
    {
        //Searching for the Role by name or id.
        if(!$search instanceof Role)
            $search = $this->roleSearch($search);

        //Loops through each of the roles for the current object
        foreach($this->roles as $role) {
            //This checks whether the object itself has the role
            //Or the roles themselves have any children which contain the $search
            if($this->roles->contains($search) || $role->hasRole($search))
                return true;
        }

        //The object doesn't have the role.
        return false;
    }

    /**
     * Attaches the given role to the parent object.
     *
     * @param $role
     * @throws RoleNotFoundException
     */
    public function assign($role)
    {
        //Searching for the Role by name or id.
        if(!$role instanceof Role)
            $role = $this->roleSearch($role);

        //Check whether the object already has this role before applying
        if(!$this->hasRole($role))
            $this->roles()->attach($role, ['relation_type' => self::class]);
    }

    /**
     * Revokes the given role from the parent object.
     *
     * @param $role
     * @throws RoleNotFoundException
     */
    public function revoke($role)
    {
        //Searching for the Role by name or id.
        if(!$role instanceof Role)
            $role = $this->roleSearch($role);

        //Check whether the object already has this role before applying
        if($this->hasRole($role))
            $this->roles()->detach($role);
    }
}