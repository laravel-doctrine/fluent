<?php
namespace Tests\Builders;

use InvalidArgumentException;
use LaravelDoctrine\Fluent\Builders\Traits\Macroable;

/**
 * @mixin TestCase
 */
trait IsMacroable
{
    /**
     * Get the builder under test. 
     * @return Macroable
     */
    abstract protected function getMacroableBuilder();
    
    public function test_can_add_macros()
    {
        $builder = $this->getMacroableBuilder();
        $this->addMacroCallExpectation($builder);

        $builder->callTheMock();
    }

    public function test_can_add_macros_with_params()
    {
        $builder = $this->getMacroableBuilder();
        $this->addMacroCallExpectation($builder, ['param', 'param2', 3]);

        $builder->callTheMock('param', 'param2', 3);
    }

    public function test_can_only_be_extended_with_closures()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Macros should be used with a closure argument, none given');

        call_user_func(
            [get_class($this->getMacroableBuilder()), 'macro'], 
            'fail'
        );
        
    }

    public function test_two_different_instances_contain_all_macros()
    {
        $builder = $this->getMacroableBuilder();
        $other = clone $builder;
        
        $this->addMacroCallExpectation($builder, [], 'addedOnBuilder');
        $this->addMacroCallExpectation($other, [], 'addedOnOther');
        
        $builder->addedOnOther();
        $other->addedOnBuilder();
    }

    /**
     * @param Macroable $builder
     * @param array     $params
     * @param string    $method
     *
     * @return \Mockery\Mock
     */
    private function addMacroCallExpectation($builder, array $params = [], $method = 'callTheMock')
    {
        array_unshift($params, \Mockery::type(get_class($builder)));
        
        /** @var \Mockery\Mock $mock */
        $mock = \Mockery::mock(['callMe' => true]);
        $mock->shouldReceive('callMe')->once()->withArgs($params);
        
        call_user_func(
            [get_class($builder), 'macro'],
            $method,
            function () use ($mock) {
                call_user_func_array([$mock, 'callMe'], func_get_args());
            }
        );
        
        return $mock;
    }
}
