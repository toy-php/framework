<?php

declare(strict_types=1);

namespace Toy\Base;

use Toy\Interfaces\MiddlewareInterface;

class Middleware implements MiddlewareInterface
{

    /**
     * @var \SplStack
     */
    protected $stack;

    /**
     * @var callable
     */
    protected $behavior;

    /**
     * Middleware constructor.
     * @param callable $callable стартовая функция
     */
    public function __construct(callable $callable)
    {
        $this->stack = new \SplStack();
        $this->stack->setIteratorMode(
            \SplDoublyLinkedList::IT_MODE_LIFO |
            \SplDoublyLinkedList::IT_MODE_KEEP
        );
        $this->stack->push(function (...$arguments) use ($callable) {
            return $callable(...$arguments);
        });
        $this->behavior = function (callable $callable, callable $next) {
            return function (...$arguments) use ($callable, $next) {
                $nextResult = call_user_func_array($next, $arguments);
                return call_user_func_array($callable, [$nextResult]);
            };
        };
    }

    /**
     * Изменение поведения промежуточных функций
     * @param callable $callable
     * @return MiddlewareInterface
     */
    public function withBehavior(callable $callable): MiddlewareInterface
    {
        if ($this->behavior === $callable) {
            return $this;
        }
        $instance = clone $this;
        $instance->behavior = $callable;
        return $instance;
    }

    /**
     * Добавление функции в стек
     * @param callable $callable
     * @return MiddlewareInterface
     */
    public function then(callable $callable): MiddlewareInterface
    {
        $behavior = $this->behavior;
        $next = $this->stack->top();
        $this->stack->push($behavior($callable, $next));
        return $this;
    }

    /**
     * Выполнение стека функций
     * @param array ...$arguments
     * @return mixed
     */
    public function __invoke(...$arguments)
    {
        $callable = $this->stack->top();
        return $callable(...$arguments);
    }
}