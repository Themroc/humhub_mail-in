<?php

namespace themroc\humhub\modules\mail_in;

class FakeSession
{
    private $user;

    public function fakeSetUser($user) {
        $this->user= $user;
    }

    public function remove() {
    }

    public function getIsActive() {
        return @$this->user->isActive();
    }

    public function getId() {
        return @$this->user->getId();
    }

}
