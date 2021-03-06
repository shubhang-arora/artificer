<?php

namespace Mascame\Artificer\Fields;

use Illuminate\Database\Query\Builder;
use Mascame\Formality\Field\Field;

trait Filterable
{
    /**
     * @var bool
     */
    public $filterable = true;

    /**
     * @var null
     */
    public $name = null;

    /**
     * @var Field
     */
    public $field;

    /**
     * @param $query Builder
     * @param $value
     * @return mixed
     */
    public function filter($query, $value)
    {
        if (method_exists($this->field, 'filter')) {
            return $this->field->filter($query, $value);
        }

        return $query->where($this->name, $value);
    }

    /**
     * @return mixed
     */
    public function displayFilter()
    {
        if (method_exists($this->field, 'displayFilter')) {
            return $this->field->displayFilter();
        }

        $this->value = \Request::old($this->name);

        return $this->output();
    }

    /**
     * By default it has filter, use hasFilter method if you want to specify something.
     *
     * @return bool
     */
    public function hasFilter()
    {
        if (property_exists($this->field, 'filterable')) {
            return $this->field->filterable;
        }

        return $this->filterable;
    }
}
