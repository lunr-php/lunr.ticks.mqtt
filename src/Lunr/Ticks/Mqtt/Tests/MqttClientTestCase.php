<?php

/**
 * This file contains the MqttClientTestCase class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Mqtt\Tests;

use Lunr\Halo\LunrBaseTestCase;
use Lunr\Ticks\EventLogging\EventInterface;
use Lunr\Ticks\EventLogging\EventLoggerInterface;
use Lunr\Ticks\Mqtt\MqttClient;
use Lunr\Ticks\TracingControllerInterface;
use Lunr\Ticks\TracingInfoInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Contracts\MessageProcessor;
use PhpMqtt\Client\Message;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the MqttClient class.
 *
 * @covers Lunr\Ticks\Mqtt\MqttClient
 */
abstract class MqttClientTestCase extends LunrBaseTestCase
{

    use MockeryPHPUnitIntegration;

    /**
     * Mock instance of the Logger interface.
     * @var LoggerInterface&MockObject
     */
    protected LoggerInterface&MockObject $logger;

    /**
     * Mock instance of a MessageProcessor.
     * @var MessageProcessor&MockObject
     */
    protected MessageProcessor&MockObject $messageProcessor;

    /**
     * Mock instance of a Message.
     * @var Message&MockObject
     */
    protected Message&MockObject $message;

    /**
     * Mock instance of ConnectionSettings.
     * @var ConnectionSettings&MockObject
     */
    protected ConnectionSettings&MockObject $settings;

    /**
     * Mock Instance of an event logger.
     * @var EventLoggerInterface&MockObject
     */
    protected EventLoggerInterface&MockObject $eventLogger;

    /**
     * Mock instance of a Controller
     * @var TracingControllerInterface&TracingInfoInterface&MockInterface
     */
    protected TracingControllerInterface&TracingInfoInterface&MockInterface $controller;

    /**
     * Mock Instance of an analytics event.
     * @var EventInterface&MockObject
     */
    protected EventInterface&MockObject $event;

    /**
     * Instance of the tested class.
     * @var MqttClient
     */
    protected MqttClient $class;

    /**
     * Mock request id.
     * @var string
     */
    protected string $id = 'some_id';

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->eventLogger = $this->getMockBuilder(EventLoggerInterface::class)
                                  ->getMock();

        $this->event = $this->getMockBuilder(EventInterface::class)
                            ->getMock();

        $this->controller = Mockery::mock(
                                TracingControllerInterface::class,
                                TracingInfoInterface::class,
                            );

        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $this->settings = $this->getMockBuilder(ConnectionSettings::class)
                               ->disableOriginalConstructor()
                               ->getMock();

        $this->messageProcessor = $this->getMockBuilder(MessageProcessor::class)->getMock();

        $this->message = $this->getMockBuilder(Message::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->class = new MqttClient(
            $this->eventLogger,
            $this->controller,
            'host',
            1883,
            $this->id,
            MqttClient::MQTT_3_1,
            NULL,
            $this->logger
        );

        parent::baseSetUp($this->class);

        $this->setReflectionPropertyValue('messageProcessor', $this->messageProcessor);
    }

    /**
     * TestCase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->class);
        unset($this->id);
        unset($this->settings);
        unset($this->logger);
        unset($this->message);
        unset($this->eventLogger);
        unset($this->event);
        unset($this->controller);

        parent::tearDown();
    }

}

?>
