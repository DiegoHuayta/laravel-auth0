<?php namespace Auth0\Login;

use Config;
use Auth0SDK\Auth0;
/**
 * Service that provides access to the Auth0 SDK.
 */
class Auth0Service {
    private $auth0;

     /**
     * The Laravel Application
     *
     * @var Application
     */
    protected $app;
    /**
     * Create a new middleware instance.
     *
     * @param  Application  $app
     * @return void
     */
    public function __construct(\Illuminate\Foundation\Application $app)
    {
        $this->app = $app;
    }
   

    /**
     * Creates an instance of the Auth0 SDK using
     * the config set in the laravel way and using a LaravelSession
     * as a store mechanism
     */
    private function getSDK() {
        if (is_null($this->auth0)) {
            // $auth0Config = Config::get('auth0::config');
            $auth0Config = $this->app['config']->get('auth0', []);
            $auth0Config['store'] = new LaravelSessionStore();
            $this->auth0 = new Auth0($auth0Config);
        }
        return $this->auth0;

    }
    /**
     * Logouts the user from the SDK
     */
    public function logout() {
        $this->getSDK()->logout();
    }

    /**
     * If the user is logged in, returns the user information
     * @return \Auth0\LaravelAuth0\Auth0User User info as described in https://docs.auth0.com/user-profile
     */
    public function getUserInfo() {
        // Get the user info from auth0
        $userInfo = $this->getSDK()->getUserInfo();

        $auth0User = new Auth0User($userInfo);
        return $auth0User;
    }

    private $_onLoginCb = null;
    /**
     * Sets a callback to be called when the user is logged in
     * @param  callback $cb A function that receives an auth0User and receives a Laravel user
     */
    public function onLogin($cb) {
        $this->_onLoginCb = $cb;
    }

    public function hasOnLogin () {
        return $this->_onLoginCb !== null;
    }

    public function callOnLogin($auth0User) {
        return call_user_func($this->_onLoginCb, $auth0User);
    }

    private $apiuser;
    public function decodeJWT($encUser) {

        // $secret = Config::get('auth0::api.secret');
        $secret = $this->app['config']->get('auth0_api.secret', []);
        $canDecode = false;

        try {
            // Decode the user
            $this->apiuser = \JWT::decode($encUser, base64_decode(strtr($secret, '-_', '+/')) );
            // validate that this JWT was made for us
            // if ($this->apiuser->aud == Config::get('auth0::api.audience')) {
            if ($this->apiuser->aud == $this->app['config']->get('auth0_api.audience', []) ) {
                $canDecode = true;
            }
        } catch(\UnexpectedValueException $e) {
        }

        return $canDecode;
    }

    public function jwtuser() {
        return $this->apiuser;
    }
}
