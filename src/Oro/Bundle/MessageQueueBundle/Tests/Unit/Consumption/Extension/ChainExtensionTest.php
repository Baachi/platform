<?php

namespace Oro\Bundle\MessageQueueBundle\Tests\Unit\Consumption\Extension;

use Oro\Bundle\MessageQueueBundle\Consumption\Extension\ChainExtension;
use Oro\Bundle\MessageQueueBundle\Consumption\Extension\ChainExtensionAwareInterface;
use Oro\Bundle\MessageQueueBundle\Consumption\Extension\ResettableExtensionInterface;
use Oro\Component\MessageQueue\Consumption\Context;
use Oro\Component\MessageQueue\Consumption\ExtensionInterface;
use Oro\Component\Testing\ClassExtensionTrait;

class ChainExtensionTest extends \PHPUnit_Framework_TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementExtensionInterface()
    {
        $this->assertClassImplements(ExtensionInterface::class, ChainExtension::class);
    }

    public function testShouldImplementResettableExtensionInterface()
    {
        $this->assertClassImplements(ResettableExtensionInterface::class, ChainExtension::class);
    }

    public function testShouldImplementChainExtensionAwareInterface()
    {
        $this->assertClassImplements(ChainExtensionAwareInterface::class, ChainExtension::class);
    }

    public function testCouldBeConstructedWithExtensionsArray()
    {
        new ChainExtension(
            [$this->createExtension(), $this->createExtension()]
        );
    }

    public function testWhenConstructedShouldPassItselfToAllToAllChainExtensionAwareExtensions()
    {
        $extension1 = $this->createExtension();
        $extension2 = $this->createMock(ChainExtension::class);

        $passedChainExtension = null;
        $extension2->expects(self::once())
            ->method('setChainExtension')
            ->willReturnCallback(function ($extension) use (&$passedChainExtension) {
                $passedChainExtension = $extension;
            });

        $chainExtension = new ChainExtension([$extension1, $extension2]);
        self::assertSame($chainExtension, $passedChainExtension);
    }

    public function testShouldProxyOnStartToAllInternalExtensions()
    {
        $context = $this->createContext();

        $fooExtension = $this->createExtension();
        $fooExtension->expects($this->once())
            ->method('onStart')
            ->with($this->identicalTo($context));
        $barExtension = $this->createExtension();
        $barExtension->expects($this->once())
            ->method('onStart')
            ->with($this->identicalTo($context));

        $chainExtension = new ChainExtension([$fooExtension, $barExtension]);
        $chainExtension->onStart($context);
    }

    public function testShouldProxyOnBeforeReceiveToAllInternalExtensions()
    {
        $context = $this->createContext();

        $fooExtension = $this->createExtension();
        $fooExtension->expects($this->once())
            ->method('onBeforeReceive')
            ->with($this->identicalTo($context));
        $barExtension = $this->createExtension();
        $barExtension->expects($this->once())
            ->method('onBeforeReceive')
            ->with($this->identicalTo($context));

        $chainExtension = new ChainExtension([$fooExtension, $barExtension]);
        $chainExtension->onBeforeReceive($context);
    }

    public function testShouldProxyOnPreReceiveToAllInternalExtensions()
    {
        $context = $this->createContext();

        $fooExtension = $this->createExtension();
        $fooExtension->expects($this->once())
            ->method('onPreReceived')
            ->with($this->identicalTo($context));
        $barExtension = $this->createExtension();
        $barExtension->expects($this->once())
            ->method('onPreReceived')
            ->with($this->identicalTo($context));

        $chainExtension = new ChainExtension([$fooExtension, $barExtension]);
        $chainExtension->onPreReceived($context);
    }

    public function testShouldProxyOnPostReceiveToAllInternalExtensions()
    {
        $context = $this->createContext();

        $fooExtension = $this->createExtension();
        $fooExtension->expects($this->once())
            ->method('onPostReceived')
            ->with($this->identicalTo($context));
        $barExtension = $this->createExtension();
        $barExtension->expects($this->once())
            ->method('onPostReceived')
            ->with($this->identicalTo($context));

        $chainExtension = new ChainExtension([$fooExtension, $barExtension]);
        $chainExtension->onPostReceived($context);
    }

    public function testShouldProxyOnIdleToAllInternalExtensions()
    {
        $context = $this->createContext();

        $fooExtension = $this->createExtension();
        $fooExtension->expects($this->once())
            ->method('onIdle')
            ->with($this->identicalTo($context));
        $barExtension = $this->createExtension();
        $barExtension->expects($this->once())
            ->method('onIdle')
            ->with($this->identicalTo($context));

        $chainExtension = new ChainExtension([$fooExtension, $barExtension]);
        $chainExtension->onIdle($context);
    }

    public function testShouldProxyOnInterruptedToAllInternalExtensions()
    {
        $context = $this->createContext();

        $fooExtension = $this->createExtension();
        $fooExtension->expects($this->once())
            ->method('onInterrupted')
            ->with($this->identicalTo($context));
        $barExtension = $this->createExtension();
        $barExtension->expects($this->once())
            ->method('onInterrupted')
            ->with($this->identicalTo($context));

        $chainExtension = new ChainExtension([$fooExtension, $barExtension]);
        $chainExtension->onInterrupted($context);
    }

    public function testShouldResetAllResettableExtensions()
    {
        $extension1 = $this->createExtension();
        $extension2 = $this->createMock(ChainExtension::class);

        $chainExtension = new ChainExtension([$extension1, $extension2]);

        $extension2->expects(self::once())
            ->method('reset');

        $chainExtension->reset();
    }

    public function testShouldSetChainExtensionToAllToAllChainExtensionAwareExtensions()
    {
        $extension1 = $this->createExtension();
        $extension2 = $this->createMock(ChainExtension::class);

        $chainExtension = new ChainExtension([$extension1, $extension2]);

        $anotherChainExtension = $this->createMock(ChainExtension::class);
        $extension2->expects(self::once())
            ->method('setChainExtension')
            ->with(self::identicalTo($anotherChainExtension));

        $chainExtension->setChainExtension($anotherChainExtension);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Context
     */
    protected function createContext()
    {
        return $this->createMock(Context::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ExtensionInterface
     */
    protected function createExtension()
    {
        return $this->createMock(ExtensionInterface::class);
    }
}
