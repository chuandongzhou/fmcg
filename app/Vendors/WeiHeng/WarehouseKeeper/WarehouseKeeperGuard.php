<?php
namespace WeiHeng\WarehouseKeeper;

use Illuminate\Auth\Guard as BaseGuard;

class WarehouseKeeperGuard extends BaseGuard
{
    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|\App\Models\WarehouseKeeper|null
     */
    public function user()
    {
        return parent::user();
    }

    /**
     *Fire the attempt event with the arguments.
     *
     * @param array $credentials
     * @param bool $remember
     * @param bool $login
     */
    protected function fireAttemptEvent(array $credentials, $remember, $login)
    {
        if ($this->events) {
            $payload = [$credentials, $remember, $login];

            $this->events->fire('warehouse_keeper.auth.attempt', $payload);
        }
    }

    /**
     * Register an authentication attempt event listener.
     *
     * @param  mixed $callback
     * @return void
     */
    public function attempting($callback)
    {
        if ($this->events) {
            $this->events->listen('warehouse_keeper.auth.attempt', $callback);
        }
    }

    /**
     *
     * Fire the login event if the dispatcher is set.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param bool $remember
     */
    protected function fireLoginEvent($user, $remember = false)
    {
        if (isset($this->events)) {
            $this->events->fire('warehouse_keeper.auth.login', [$user, $remember]);
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout()
    {
        $user = $this->user();

        // If we have an event dispatcher instance, we can fire off the logout event
        // so any further processing can be done. This allows the developer to be
        // listening for anytime a user signs out of this application manually.
        $this->clearUserDataFromStorage();

        if (!is_null($this->user)) {
            $this->refreshRememberToken($user);
        }

        if (isset($this->events)) {
            $this->events->fire('warehouse_keeper.auth.logout', [$user]);
        }

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

        $this->loggedOut = true;
    }
}