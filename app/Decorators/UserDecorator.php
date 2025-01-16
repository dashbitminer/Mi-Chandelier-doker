<?php

namespace App\Decorators;

class UserDecorator
{
    protected $object;

    private $profile;

    public function __construct($object)
    {
        $this->object = $object;

        $this->profile = $object->profile;
    }

    public function firstName()
    {
        return $this->profile->first_name;
    }

    public function lastName()
    {
        return $this->profile->last_name;
    }

    public function name()
    {
        return implode(' ', [$this->firstName(), $this->lastName()]);
    }

    public function role()
    {
        $roles = $this->object->getRoleNames()->toArray();

        return count($roles) > 0 ? $roles[0] : null;
    }
}
