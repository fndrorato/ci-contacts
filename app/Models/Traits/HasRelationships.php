<?php

namespace App\Models\Traits;

trait HasRelationships
{
    public function with($relationships)
    {
        $this->loadRelationships($relationships);
        return $this;
    }

    protected function loadRelationships($relationships)
    {
        foreach ($relationships as $relationship) {
            if (method_exists($this, $relationship)) {
                $this->{$relationship}; // Trigger the relationship loading
            }
        }
    }
}