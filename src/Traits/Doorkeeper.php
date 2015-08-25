<?php

    namespace Tshafer\Doorkeeper\Traits;

    use Tshafer\Doorkeeper\Listeners\DoorkeeperListener;

    /**
     * Class Doorkeeper
     *
     * @package Tshafer\Doorkeeper\Traits
     */
    trait Doorkeeper
    {

        /**
         * Boot the trait.
         */
        public static function bootDoorkeeper()
        {
            static::loaded( DoorkeeperListener::class . '@compare' );
        }

        /**
         * {@inheritdoc}
         */
        public function newFromBuilder( $attributes = [ ] )
        {
            $instance = parent::newFromBuilder( $attributes );

            if (empty( $instance->limitations )) {
                return $instance;
            }

            $instance->limitations = collect( $this->limitations );
            $instance->fireModelEvent( 'loaded' );

            return $instance;
        }

        /**
         * Register a loaded model event with the dispatcher.
         *
         * @param string $callback
         * @param int    $priority
         */
        public static function loaded( $callback, $priority = 0 )
        {
            static::registerModelEvent( 'loaded', $callback, $priority );
        }

        /**
         * Check if the limit for a relation or overall has been reached.
         *
         * @param string $key
         *
         * @return bool
         */
        public function maxed( $key = null )
        {
            if ($key !== null) {
                return session()->has( 'doorkeeper_reached_' . $key );
            }

            return session()->has( 'doorkeeper_reached_maximum' );
        }

        /**
         * Get the current count of a relation or the overall count.
         *
         * @param string $key
         *
         * @return int
         */
        public function current( $key = null )
        {
            if ($key !== null) {
                return session()->get( 'doorkeeper_count_' . $key );
            }

            return session()->get( 'doorkeeper_overall_count' );
        }

        /**
         * Get the limit of a relation or the overall limit.
         *
         * @param string $key
         *
         * @return int
         */
        public function allowed( $key = null )
        {
            if ($key !== null) {
                return $this->limitations->get( $key );
            }

            return $this->limitations->sum();
        }

        /**
         * Performs a check on a model with the given rules.
         *
         * @param array $limits
         *
         * @return $this
         */
        public function limits( array $limits )
        {
            $this->limitations = collect( $limits );

            $listener = new DoorkeeperListener();
            $listener->compare( $this );

            return $this;
        }

        /**
         * Determine if the data passes the validation rules.
         *
         * @return bool
         */
        public function passes( $key = null )
        {
            return ! $this->maxed( $key );
        }

        /**
         * Determine if the data fails the validation rules.
         *
         * @return bool
         */
        public function fails( $key = null )
        {
            return $this->maxed( $key );
        }
    }
