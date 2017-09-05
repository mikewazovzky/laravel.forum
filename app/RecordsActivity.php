<?php

namespace App;

trait RecordsActivity {

    /**
     * Boot the trait.
     *
     * If the Model uses  a trait 
     * protected static function bootTraitName  
     * method will be called as if it's placed within Model's boot method      
     */
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) return;
        
        foreach (static::geActivitiesToRecord() as $event) {
            static::created(function($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        static::deleting(function($model) {
            $model->activity()->delete();
        });
    }

    /**
     * Fetch all model events that require activity recording.
     *
     * @return array 
     * array of Model events to be recorded as activities, can be ovewritten in a Model
     */
    protected static function geActivitiesToRecord()
    {
        return ['created'];
    }

    /**
     * Record new activity for the model.
     *
     * @param string $event
     */
    protected function recordActivity($event)
    {
        // Activity::create([
        //     'type' => $this->getActivityType($event),
        //     'user_id' => auth()->id(),
        //     'subject_id' => $this->id,
        //     'subject_type' => get_class($this)            
        // ]);

        // use polymorphic relationship
        $this->activity()->create([
            'type' => $this->getActivityType($event),
            'user_id' => auth()->id(),
        ]);       
    }

    /**
     * Fetch the activity relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Determine the activity type.
     *
     * @param  string $event
     * @return string
     */
    protected function getActivityType($event)
    {
        $type = strtolower((new \ReflectionClass($this))->getShortName());
        return "{$event}_{$type}";
    }
}