<?php namespace Auth0\Login\Middleware;

use \Illuminate\Foundation\Application;
use \Illuminate\Contracts\Routing\Middleware;
use \Closure;
/**
 * Service that provides access to the Auth0 SDK.
 */
class AuthJwt implements Middleware{
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
	public function __construct(Application $app)
	{
		$this->app = $app;
	}
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		// Get the encrypted user
		$authorizationHeader = $request->header("Authorization");
		$encUser = str_replace('Bearer ', '', $authorizationHeader);

		if (trim($encUser) != '') {
			$canDecode = \App::make('auth0')->decodeJWT($encUser);
		} else {
			$canDecode = false;
		}
		

		if (!$canDecode) {
			return \Response::make("Unauthorized user", 401);
		}

		return $next($request);
		
	}
}