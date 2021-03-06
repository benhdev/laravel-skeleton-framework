<?php

namespace Skeleton\Guards;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Illuminate\Auth\AuthenticationException;

class SkeletonGuard implements Guard
{
    use GuardHelpers;

    /**
     * The key used to identify a user when logging in
     *
     * @var string $identifier
     */
    protected $identifier = 'email';

    /**
     * The name of the credentials item containing the API token
     *
     * @var string $inputKey
     */
    protected $inputKey = 'api_token';

    /**
     * The name of the token "column" within the database
     *
     * @var string
     */
    protected $storageKey = 'api_token';

    /**
     * The plain text API Token for the authenticated user
     *
     * @var string
     */
    protected $plainTextToken;

    /**
     * @method __construct()
     * Create a new authentication guard
     *
     * @param Illuminate\Contracts\Auth\UserProvider $provider
     * @param Illuminate\Http\Request $request
     *
     * @return void
     */
    public function __construct(UserProvider $provider, protected Request $request)
    {
        $this->provider = $provider;
    }

    /**
     * @method attempt()
     * Attempt to authenticate a user using the given credentials
     *
     * @param array $credentials
     *
     * @return string
     */
    public function attempt(array $credentials = [])
    {
        // ensure we have credentials to match with a user
        if (empty($credentials[$this->identifier]) || empty($credentials['password'])) {
            throw new AuthenticationException;
        }

        // retrieve the user by the credentials
        if ($user = $this->provider->retrieveByCredentials($credentials)) {
            // validate the password with the provider
            $validated = $this->provider->validateCredentials($user, $credentials);
        }

        if (empty($validated)) {
            // The user credentials could not be validated
            throw new AuthenticationException;
        }

        // regenerate the users API token
        $token = Str::of(Str::random(60))->start($user->getAuthIdentifier().'|');
        $user->{$this->storageKey} = Hash::make(
            $this->plainTextToken = $token
        );

        $user->save();

        // set the user instance on the guard
        $this->user = $user;
        return $this->plainTextToken;
    }

    /**
     * @method validate()
     * Validate a user's credentials
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $credentials = array_filter(
            $credentials,
            fn ($key) => ! str_contains($key, $this->inputKey),
            ARRAY_FILTER_USE_KEY
        );

        // get the token from the request
        $token = $this->request->bearerToken();

        $identifier = Str::of($token)->explode('|')->first();
        if ($user = $this->provider->retrieveById($identifier)) {
            return Hash::check($token, $user->{$this->storageKey})
                ? true
                : false;
        }

        return false;
    }

    /**
     * @method user()
     * Get the currently authenticated user
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // if we already have the user then return it
        if ($user = $this->user) {
            return $user;
        }

        // get the token from the request
        $token = $this->request->bearerToken();

        $identifier = Str::of($token)->explode('|')->first();
        if ($user = $this->provider->retrieveById($identifier)) {
            return Hash::check($token, $user->{$this->storageKey})
                ? $this->user = $user
                : null;
        }
    }

    /**
     * @method token()
     * Retrieve the plain text token for the user
     *
     * @return string
     */
    public function token()
    {
        return $this->plainTextToken;
    }

    /**
     * @method login()
     * Authenticate as the $user provided
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    public function login(Authenticatable $user)
    {
        $this->setUser($user);
    }

    /**
     * @method logout()
     * Logout the currently authenticated user
     *
     * @return void
     */
    public function logout()
    {
        $this->setUser(null);
    }

    /**
     * @method logoutOtherDevices()
     * Logout the currently authenticated user on other devices
     *
     * Make sure to display the generated token
     * to the user once this has been called
     * @example Auth::token()
     *
     * @return void
     */
    public function logoutOtherDevices()
    {
        if (!$user = $this->user) {
            return;
        }

        // regenerate the users API token
        $token = Str::of(Str::random(60))->start($user->getAuthIdentifier().'|');
        $user->{$this->storageKey} = Hash::make(
            $this->plainTextToken = $token
        );

        $user->save();
    }
}
