<?php

namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters 
{
    /**
     * @var Illuminate\Http\Request
     */
    protected $request;
    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;
    /**
     * Registered filters to be applied.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Create a new Filters subclass instance.
     * 
     * @param Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Applies filters.
     *
     * @param  \Illuminate\Database\Eloquent\Builder Builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        foreach($this->getFilters() as $filter => $value) {
            if(method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }        

        return $this->builder; 
    }

    /**
     * Fetch all relevant filters from the request.
     *
     * @return Array
     */
    public function getFilters()
    {
        return $this->request->intersect($this->filters);
    }
}