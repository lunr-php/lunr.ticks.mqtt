<?php

/**
 * This file contains the MqttClientBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Mqtt\Tests;

use Lunr\Ticks\AnalyticsDetailLevel;
use PhpMqtt\Client\Contracts\MessageProcessor;

/**
 * This class contains the base tests for the MqttClient.
 *
 * @covers Lunr\Ticks\Mqtt\MqttClient
 */
class MqttClientBaseTest extends MqttClientTestCase
{

    /**
     * Test that the eventlogger class was passed correctly.
     */
    public function testEventLoggerPassed(): void
    {
        $this->assertPropertySame('eventLogger', $this->eventLogger);
    }

    /**
     * Test that the tracing controller class was passed correctly.
     */
    public function testTracingControllerPassed(): void
    {
        $this->assertPropertySame('tracingController', $this->controller);
    }

    /**
     * Test that the default analytics detail level is set correctly.
     */
    public function testAnalyticsDetailLevel(): void
    {
        $this->assertPropertySame('level', AnalyticsDetailLevel::Info);
    }

    /**
     * Test that data is set to empty.
     */
    public function testDataSetCorrectly(): void
    {
        $this->assertPropertySame('data', []);
    }

    /**
     * Test that the MessageProcessor class is set correctly.
     */
    public function testMessageProcessorIsSetCorrectly(): void
    {
        $messageProcessor = $this->getReflectionPropertyValue('messageProcessor');

        $this->assertInstanceOf(MessageProcessor::class, $messageProcessor);
    }

    /**
     * Test setAnalyticsDetailLevel().
     *
     * @covers Lunr\Ticks\Mqtt\MqttClient::setAnalyticsDetailLevel
     */
    public function testSetAnalyticsDetailLevel(): void
    {
        $this->class->setAnalyticsDetailLevel(AnalyticsDetailLevel::Detailed);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Detailed);
    }

}

?>
