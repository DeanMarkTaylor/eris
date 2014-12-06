<?php

/**
 * Some of these would make good unit tests, but importing them
 * doesn't solve the problem as the more important ones are the failures
 * We need to look into end-to-end testing
 */
class WhenTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testWhenWithAnAnonymousFunctionWithGherkinSyntax()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->when(function($n) {
                return $n > 42;
            })        
            ->then(function($number) {
                $this->assertTrue(
                    $number > 42,
                    "\$number was filtered to be more than 42, but it's $number"
                );
            });
    }

    public function testWhenWithAnAnonymousFunctionWithLogicSyntax()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->theCondition(function($n) {
                return $n > 42;
            })        
            ->implies(function($number) {
                $this->assertTrue(
                    $number > 42,
                    "\$number was filtered to be more than 42, but it's $number"
                );
            });
    }

    public function testWhenWithAnAnonymousFunctionForMultipleArguments()
    {
        $this->forAll([
            $this->genNat(),
            $this->genNat(),
        ])
            ->when(function($first, $second) {
                return $first > 42 && $second > 23;
            })        
            ->then(function($first, $second) {
                $this->assertTrue(
                    $first + $second > 42 + 23,
                    "\$first and \$second were filtered to be more than 42 and 23, but they are $first and $second"
                );
            });
    }

    public function testWhenWithOnePHPUnitConstraint()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->when($this->greaterThan(42))
            ->then(function($number) {
                $this->assertTrue(
                    $number > 42,
                    "\$number was filtered to be more than 42, but it's $number"
                );
            });
    }

    public function testWhenWithMultiplePHPUnitConstraints()
    {
        $this->forAll([
            $this->genNat(),
            $this->genNat(),
        ])
            ->when($this->greaterThan(42), $this->greaterThan(23))
            ->then(function($first, $second) {
                $this->assertTrue(
                    $first + $second > 42 + 23,
                    "\$first and \$second were filtered to be more than 42 and 23, but they are $first and $second"
                );
            });
    }

    public function testMultipleWhenClausesWithGherkinSyntax()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->when($this->greaterThan(42))
            ->andAlso($this->lessThan(900))
            ->then(function($number) {
                $this->assertTrue(
                    $number > 42 && $number < 900,
                    "\$number was filtered to be between 42 and 900, but it is $number"
                );
            });
    }

    public function testMultipleWhenClausesWithLogicSyntax()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->theCondition($this->greaterThan(42))
            ->andTheCondition($this->lessThan(900))
            ->imply(function($number) {
                $this->assertTrue(
                    $number > 42 && $number < 900,
                    "\$number was filtered to be between 42 and 900, but it is $number"
                );
            });
    }

    public function testWhenWhichSkipsTooManyValues()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->when($this->greaterThan(800))
            ->then(function($number) {
                $this->assertTrue(
                    $number > 800
                );
            });
    }

    /**
     * The current implementation shows no problem as PHPUnit prefers to show 
     * the exception from the test method than the one from teardown
     * when both fail.
     */
    public function testWhenFailingWillNaturallyHaveALowEvaluationRatioSoWeDontWantThatErrorToObscureTheTrueOne()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->when($this->greaterThan(100))
            ->then(function($number) {
                $this->assertTrue(
                    $number <= 100,
                    "\$number should be less or equal to 100, but it is $number"
                );
            });
    }
}