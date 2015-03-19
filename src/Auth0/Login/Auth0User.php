<?php namespace Auth0\Login;

/**
 * This class represents a generic user initialized with the user information
 * given by Auth0.
 *
 */
class Auth0User implements \Illuminate\Contracts\Auth\Authenticatable {

    private $userInfo;
    function __construct ($userInfo) {
        $this->userInfo = $userInfo;
    }
    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier() {
        return $this->userInfo["user_id"];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword() {
        return $this->userInfo["access_token"];
    }

    public function getRememberToken() {
        return null;
    }

    public function setRememberToken($value) {

    }

    public function getRememberTokenName() {
        return null;
    }

    /**
     * Add a generic getter to get all the properties of the userInfo
     */
    public function __get($name) {
        return $this->userInfo[$name];
    }

}
