<?php

namespace Tshafer\Doorkeeper\Contracts;

interface DoorkeeperContract
{
    /**
     * Boot the trait.
     */
    public static function bootDoorkeeper();

    /**
     * {@inheritdoc}
     */
    public function newFromBuilder($attributes = []);

    /**
     * Register a loaded model event with the dispatcher.
     *
     * @param \Closure|string $callback
     * @param int             $priority
     */
    public static function loaded($callback, $priority = 0);

    /**
     * Check if the limit for a relation or overall has been reached.
     *
     * @param string $key
     *
     * @return bool
     */
    public function maxed($key = null);

    /**
     * Get the current count of a relation or the overall count.
     *
     * @param string $key
     *
     * @return int
     */
    public function current($key = null);

    /**
     * Get the limit of a relation or the overall limit.
     *
     * @param string $key
     *
     * @return int
     */
    public function allowed($key = null);

    /**
     * Performs a check on a model with the given rules.
     *
     * @param array $limits
     */
    public function limits($limits);

    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes($key = null);

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     */
    public function fails($key = null);
}
