<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Event;

use Closure;
use InvalidArgumentException;
use SplObserver;
use SplSubject;

/**
 * 观察者角色 observer.
 *
 * @see http://php.net/manual/zh/class.splobserver.php
 */
class Observer implements SplObserver
{
    /**
     * 观察者目标角色 subject.
     *
     * @var \SplSubject
     */
    protected SplSubject $subject;

    /**
     * 观察者实现.
     *
     * @var \Closure
     */
    protected ?Closure $handle = null;

    /**
     * 构造函数.
     */
    public function __construct(?Closure $handle = null)
    {
        $this->handle = $handle;
    }

    /**
     * 观察者实现.
     *
     * @param array ...$args
     */
    public function __invoke(...$args)
    {
        $handle = $this->handle;
        $handle(...$args);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function update(SplSubject $subject): void
    {
        if (method_exists($this, 'handle')) {
            $handle = [$this, 'handle'];
        } elseif ($this->handle) {
            $handle = [$this, '__invoke'];
        } else {
            $handle = null;
        }

        if (!is_callable($handle)) {
            $e = sprintf('Observer %s must has handle method.', get_class($this));

            throw new InvalidArgumentException($e);
        }

        $subject->container->call($handle, $subject->notifyArgs);
    }
}
