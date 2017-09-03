<?php

namespace App;

trait RecordsActivity {

    // If the Model uses  a trait 
    // protected static function bootTraitName 
    // method will be called as if it's placed within Model's boot method    
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

    // return array of Model events to be recorded as activities
    // can be ovewritten in a model
    protected static function geActivitiesToRecord()
    {
        return ['created'];
    }

    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

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

    protected function getActivityType($event)
    {
        $type = strtolower((new \ReflectionClass($this))->getShortName());
        return "{$event}_{$type}";
    }
}