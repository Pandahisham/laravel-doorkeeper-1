<?php

    namespace Tshafer\Doorkeeper\Listeners;

    use Tshafer\Doorkeeper\Contracts\ListenerContract;

    class DoorkeeperListener implements ListenerContract
    {

        /**
         * Compare the amount of the relations with the limitation.
         *
         * @param \Illuminate\Database\Eloquent\Model $model
         *
         * @return mixed
         */
        public function compare( $model )
        {
            // Flush session before each check
            $session = session()->get( null );
            foreach ($session as $key => $value) {
                if (starts_with( $key, 'doorkeeper_' )) {
                    session()->forget( $key );
                }
            }

            // Limits
            $limits = $model->limitations;

            // No limits
            if ($limits->isEmpty()) {
                return $model;
            }

            // Check each relation and limitation
            $overallCount = 0;
            foreach ($limits as $relation => $limit) {
                if ( ! $model->$relation) {
                    continue;
                }

                $relationCount = $model->$relation->count();
                $overallCount += $relationCount;

                if ($relationCount >= $limit) {
                    session()->put( 'doorkeeper_reached_' . $relation, true );
                }

                session()->put( 'doorkeeper_count_' . $relation, $relationCount );
            }
            // Check if the overall limit has been reached
            if ($overallCount >= $limits->sum()) {
                session()->put( 'doorkeeper_reached_maximum', true );
            }

            session()->put( 'doorkeeper_overall_count', $overallCount );
        }
    }
