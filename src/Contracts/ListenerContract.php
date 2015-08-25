<?php

namespace Tshafer\Doorkeeper\Contracts;

interface ListenerContract
{
    /**
     * Compare the amount of the relations with the limitation.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function compare($model);
}
