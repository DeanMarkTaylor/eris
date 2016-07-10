<?php
namespace Eris\Generator;

use IteratorAggregate;
use ArrayIterator;
use Countable;

/**
 * Parametric with respect to the type <T> of its value, 
 * which should be the type parameter <T> of all the contained GeneratedValue
 * instances.
 */
class GeneratedValueOptions
    extends GeneratedValue 
    implements IteratorAggregate, Countable
{
    private $generatedValues;
    
    public function __construct(array $generatedValues)
    {
        $this->generatedValues = $generatedValues;
    }

    public function last()
    {
        return $this->generatedValues[count($this->generatedValues) - 1];
    }

    public function map(callable $callable, $passthru)
    {
        return new self(array_map(
            function ($value) use ($callable, $passthru) {
                return $value->map($callable, $passthru);
            },
            $this->generatedValues
        ));
    }

    /**
     * @return self
     */
    public function add(GeneratedValue $value)
    {
        return new self(array_merge(
            $this->generatedValues,
            [$value]
        ));
    }

    public function remove(GeneratedValue $value)
    {
        $generatedValues = $this->generatedValues;
        $index = array_search($value, $generatedValues);
        if ($index !== false) {
            unset($generatedValues[$index]);
        }
        return new self(array_values($generatedValues));

    }

    public function unbox()
    {
        return $this->last()->unbox();
    }

    public function input()
    {
        return $this->last()->input();
    }

    public function getIterator()
    {
        return new ArrayIterator($this->generatedValues);
    }

    public function count()
    {
        return count($this->generatedValues);
    }

    public function cartesianProduct($generatedValueOptions, callable $merge) 
    {
        $options = [];
        foreach ($this as $firstPart) {
            foreach ($generatedValueOptions as $secondPart) {
                $options[] = $firstPart->merge($secondPart, $merge);
            }
        }
        return new self($options);
    }
}