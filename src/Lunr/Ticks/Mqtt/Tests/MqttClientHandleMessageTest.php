<?php

/**
 * This file contains the MqttClientHandleMessageTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Mqtt\Tests;

use Lunr\Ticks\AnalyticsDetailLevel;
use PhpMqtt\Client\MessageType;
use RuntimeException;

/**
 * This class contains the read tests for the MqttClient.
 *
 * @covers Lunr\Ticks\Mqtt\MqttClient
 */
class MqttClientHandleMessageTest extends MqttClientTestCase
{

    /**
     * Test handleMessage() when the trace ID is unavailable.
     *
     * @covers Lunr\Ticks\Mqtt\MqttClient::handleMessage
     */
    public function testHandleMessageWithTraceIDUnavailable(): void
    {
        $this->setReflectionPropertyValue('socket', fopen('/dev/null', 'r+'));
        $this->setReflectionPropertyValue('data', [ 'topic' => 'topic' ]);

        $property = $this->reflection->getParentClass()->getProperty('settings');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->settings);

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturn(NULL);

        $this->controller->shouldNotReceive('getSpanId');

        $this->controller->shouldNotReceive('getParentSpanId');

        $this->controller->shouldNotReceive('getSpanSpecifictags');

        $this->controller->shouldNotReceive('stopChildSpan');

        $this->event->expects($this->never())
                    ->method('addTags');

        $this->event->expects($this->never())
                    ->method('addFields');

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->never())
                    ->method('setTraceId');

        $this->event->expects($this->never())
                    ->method('setSpanId');

        $this->event->expects($this->never())
                    ->method('setParentSpanId');

        $this->event->expects($this->never())
                    ->method('record');

        $this->message->expects($this->exactly(3))
                      ->method('getType')
                      ->willReturn(MessageType::PUBLISH_ACKNOWLEDGEMENT());

        $this->message->expects($this->once())
                      ->method('getQualityOfService')
                      ->willReturn(1);

        $this->message->expects($this->once())
                      ->method('getTopic')
                      ->willReturn('testTopic');

        $this->message->expects($this->exactly(3))
                      ->method('getMessageId')
                      ->willReturn(10);

        $floatval  = 1734352683.3516;
        $stringval = '0.35160200 1734352683';

        $this->mockFunction('microtime', fn(bool $float) => $float ? $floatval : $stringval);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Trace ID not available!');

        $method = $this->getReflectionMethod('handleMessage');
        $method->invokeArgs($this->class, [ $this->message ]);

        $this->unmockFunction('microtime');
    }

    /**
     * Test handleMessage() when the span ID is unavailable.
     *
     * @covers Lunr\Ticks\Mqtt\MqttClient::handleMessage
     */
    public function testHandleMessageWithSpanIDUnavailable(): void
    {
        $this->setReflectionPropertyValue('socket', fopen('/dev/null', 'r+'));
        $this->setReflectionPropertyValue('data', [ 'topic' => 'topic' ]);

        $traceID = '7b333e15-aa78-4957-a402-731aecbb358e';

        $property = $this->reflection->getParentClass()->getProperty('settings');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->settings);

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturn($traceID);

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturn(NULL);

        $this->controller->shouldNotReceive('getParentSpanId');

        $this->controller->shouldNotReceive('getSpanSpecifictags');

        $this->controller->shouldNotReceive('stopChildSpan');

        $this->event->expects($this->never())
                    ->method('addTags');

        $this->event->expects($this->never())
                    ->method('addFields');

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('setTraceId')
                    ->with($traceID);

        $this->event->expects($this->never())
                    ->method('setSpanId');

        $this->event->expects($this->never())
                    ->method('setParentSpanId');

        $this->event->expects($this->never())
                    ->method('record');

        $this->message->expects($this->exactly(3))
                      ->method('getType')
                      ->willReturn(MessageType::PUBLISH_ACKNOWLEDGEMENT());

        $this->message->expects($this->once())
                      ->method('getQualityOfService')
                      ->willReturn(1);

        $this->message->expects($this->once())
                      ->method('getTopic')
                      ->willReturn('testTopic');

        $this->message->expects($this->exactly(3))
                      ->method('getMessageId')
                      ->willReturn(10);

        $floatval  = 1734352683.3516;
        $stringval = '0.35160200 1734352683';

        $this->mockFunction('microtime', fn(bool $float) => $float ? $floatval : $stringval);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Span ID not available!');

        $method = $this->getReflectionMethod('handleMessage');
        $method->invokeArgs($this->class, [ $this->message ]);

        $this->unmockFunction('microtime');
    }

    /**
     * Test handleMessage() when the parent span ID is unavailable.
     *
     * @covers Lunr\Ticks\Mqtt\MqttClient::handleMessage
     */
    public function testHandleMessageWithParentSpanIDUnavailable(): void
    {
        $this->setReflectionPropertyValue('socket', fopen('/dev/null', 'r+'));
        $this->setReflectionPropertyValue('data', [ 'topic' => 'topic' ]);

        $traceID = '7b333e15-aa78-4957-a402-731aecbb358e';
        $spanID  = '24ec5f90-7458-4dd5-bb51-7a1e8f4baafe';

        $property = $this->reflection->getParentClass()->getProperty('settings');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->settings);

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturn($traceID);

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturn($spanID);

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturn(NULL);

        $this->controller->shouldNotReceive('getSpanSpecifictags');

        $this->controller->shouldNotReceive('stopChildSpan');

        $this->event->expects($this->never())
                    ->method('addTags');

        $this->event->expects($this->never())
                    ->method('addFields');

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('setTraceId')
                    ->with($traceID);

        $this->event->expects($this->once())
                    ->method('setSpanId')
                    ->with($spanID);

        $this->event->expects($this->never())
                    ->method('setParentSpanId');

        $this->event->expects($this->never())
                    ->method('record');

        $this->message->expects($this->exactly(3))
                      ->method('getType')
                      ->willReturn(MessageType::PUBLISH_ACKNOWLEDGEMENT());

        $this->message->expects($this->once())
                      ->method('getQualityOfService')
                      ->willReturn(1);

        $this->message->expects($this->once())
                      ->method('getTopic')
                      ->willReturn('testTopic');

        $this->message->expects($this->exactly(3))
                      ->method('getMessageId')
                      ->willReturn(10);

        $floatval  = 1734352683.3516;
        $stringval = '0.35160200 1734352683';

        $this->mockFunction('microtime', fn(bool $float) => $float ? $floatval : $stringval);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Parent Span ID not available!');

        $method = $this->getReflectionMethod('handleMessage');
        $method->invokeArgs($this->class, [ $this->message ]);

        $this->unmockFunction('microtime');
    }

    /**
     * Test handleMessage() at analytics detail level Info.
     *
     * @covers Lunr\Ticks\Mqtt\MqttClient::handleMessage
     */
    public function testHandleMessageAtLevelInfo(): void
    {
        $this->setReflectionPropertyValue('socket', fopen('/dev/null', 'r+'));
        $this->setReflectionPropertyValue('data', [ 'topic' => 'topic' ]);

        $traceID      = '7b333e15-aa78-4957-a402-731aecbb358e';
        $spanID       = '24ec5f90-7458-4dd5-bb51-7a1e8f4baafe';
        $parentSpanID = '8b1f87b5-8383-4413-a341-7619cd4b9948';

        $property = $this->reflection->getParentClass()->getProperty('settings');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->settings);

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturn($traceID);

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturn($spanID);

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturn($parentSpanID);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'type'   => 'MQTT-response',
            'domain' => 'host',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'url'            => 'testTopic',
            'startTimestamp' => 1734352683.3516,
            'endTimestamp'   => 1734352683.3516,
            'executionTime'  => 0.0,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('setTraceId')
                    ->with($traceID);

        $this->event->expects($this->once())
                    ->method('setSpanId')
                    ->with($spanID);

        $this->event->expects($this->once())
                    ->method('setParentSpanId')
                    ->with($parentSpanID);

        $this->event->expects($this->once())
                    ->method('record');

        $this->message->expects($this->exactly(3))
                      ->method('getType')
                      ->willReturn(MessageType::PUBLISH_ACKNOWLEDGEMENT());

        $this->message->expects($this->once())
                      ->method('getQualityOfService')
                      ->willReturn(1);

        $this->message->expects($this->once())
                      ->method('getTopic')
                      ->willReturn('testTopic');

        $this->message->expects($this->exactly(3))
                      ->method('getMessageId')
                      ->willReturn(10);

        $floatval  = 1734352683.3516;
        $stringval = '0.35160200 1734352683';

        $this->mockFunction('microtime', fn(bool $float) => $float ? $floatval : $stringval);

        $method = $this->getReflectionMethod('handleMessage');
        $method->invokeArgs($this->class, [ $this->message ]);

        $this->unmockFunction('microtime');
    }

    /**
     * Test handleMessage() at analytics detail level Detailed.
     *
     * @covers Lunr\Ticks\Mqtt\MqttClient::handleMessage
     */
    public function testHandleMessageAtLevelDetailed(): void
    {
        $this->setReflectionPropertyValue('socket', fopen('/dev/null', 'r+'));
        $this->setReflectionPropertyValue('data', [ 'topic' => 'topic' ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $traceID      = '7b333e15-aa78-4957-a402-731aecbb358e';
        $spanID       = '24ec5f90-7458-4dd5-bb51-7a1e8f4baafe';
        $parentSpanID = '8b1f87b5-8383-4413-a341-7619cd4b9948';

        $property = $this->reflection->getParentClass()->getProperty('settings');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->settings);

        $string  = 'b7rrrEKWPBBniam2zDQjn2QaYE5dAPLgfyTy2RbTPVykQDrYeq3HKjTKPLeSgaf8dTJNiatfrbGKMUBU4VYY8PphqxBZSe6mKuz2R7FVdcc9VZmAEkNDg7mfT7EPcvg';
        $string .= 'LgTKUihAfxc76CihMFqVpnU7e3iqWJdBPLnP34JQ2zQVBmSv8kvHjAGrv5fCVnPCEvbQx5PUNBukQVNFZukLtEtb2ZYy54JqjbHi4CF9kWV9MHq2Ah5A9vjYLxTBziT';
        $string .= 'MYcTCtXxcFCVYQ6awvkN9TdupdD7ihecSHB79JbqPSAVbRbz4ZFtnbe2aPzVRmVvkLDuFefmutDfGgKCizYMGJnExv6ViCryU4JZAufWxeag22BrDJ34aBRwbnCqwEa';
        $string .= 't2K6p45zvvCVpen5Z6VkQCiLGV5kGzfhb6cgUvnvyKK5tzjE7xx95PLupW8uPaCYyrpgT9RS8GQNf72qwnA5bebjRe3hi66KXLaJU2d5Tkpe4eRutgucvKFFBk8MxkY';

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturn($traceID);

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturn($spanID);

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturn($parentSpanID);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'type'   => 'MQTT-response',
            'domain' => 'host',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $headers = '{"type":"PUBLISH_ACKNOWLEDGEMENT","qualityOfService":1,"messageId":10,"topic":"testTopic","acknowledgedQualityOfServices":[]}';

        $fields = [
            'url'             => 'testTopic',
            'startTimestamp'  => 1734352683.3516,
            'endTimestamp'    => 1734352683.3516,
            'executionTime'   => 0.0,
            'responseBody'    => $string . 'E984...',
            'responseHeaders' => $headers,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('setTraceId')
                    ->with($traceID);

        $this->event->expects($this->once())
                    ->method('setSpanId')
                    ->with($spanID);

        $this->event->expects($this->once())
                    ->method('setParentSpanId')
                    ->with($parentSpanID);

        $this->event->expects($this->once())
                    ->method('record');

        $this->message->expects($this->exactly(3))
                      ->method('getType')
                      ->willReturn(MessageType::PUBLISH_ACKNOWLEDGEMENT());

        $this->message->expects($this->once())
                      ->method('getContent')
                      ->willReturn($string . 'E984TBDFDAKJF');

        $this->message->expects($this->once())
                      ->method('getQualityOfService')
                      ->willReturn(1);

        $this->message->expects($this->once())
                      ->method('getTopic')
                      ->willReturn('testTopic');

        $this->message->expects($this->exactly(3))
                      ->method('getMessageId')
                      ->willReturn(10);

        $floatval  = 1734352683.3516;
        $stringval = '0.35160200 1734352683';

        $this->mockFunction('microtime', fn(bool $float) => $float ? $floatval : $stringval);

        $method = $this->getReflectionMethod('handleMessage');
        $method->invokeArgs($this->class, [ $this->message ]);

        $this->unmockFunction('microtime');
    }

    /**
     * Test handleMessage() at analytics detail level Full.
     *
     * @covers Lunr\Ticks\Mqtt\MqttClient::handleMessage
     */
    public function testHandleMessageAtLevelFull(): void
    {
        $this->setReflectionPropertyValue('socket', fopen('/dev/null', 'r+'));
        $this->setReflectionPropertyValue('data', [ 'topic' => 'topic' ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Full);

        $traceID      = '7b333e15-aa78-4957-a402-731aecbb358e';
        $spanID       = '24ec5f90-7458-4dd5-bb51-7a1e8f4baafe';
        $parentSpanID = '8b1f87b5-8383-4413-a341-7619cd4b9948';

        $property = $this->reflection->getParentClass()->getProperty('settings');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, $this->settings);

        $string  = 'b7rrrEKWPBBniam2zDQjn2QaYE5dAPLgfyTy2RbTPVykQDrYeq3HKjTKPLeSgaf8dTJNiatfrbGKMUBU4VYY8PphqxBZSe6mKuz2R7FVdcc9VZmAEkNDg7mfT7EPcvg';
        $string .= 'LgTKUihAfxc76CihMFqVpnU7e3iqWJdBPLnP34JQ2zQVBmSv8kvHjAGrv5fCVnPCEvbQx5PUNBukQVNFZukLtEtb2ZYy54JqjbHi4CF9kWV9MHq2Ah5A9vjYLxTBziT';
        $string .= 'MYcTCtXxcFCVYQ6awvkN9TdupdD7ihecSHB79JbqPSAVbRbz4ZFtnbe2aPzVRmVvkLDuFefmutDfGgKCizYMGJnExv6ViCryU4JZAufWxeag22BrDJ34aBRwbnCqwEa';
        $string .= 't2K6p45zvvCVpen5Z6VkQCiLGV5kGzfhb6cgUvnvyKK5tzjE7xx95PLupW8uPaCYyrpgT9RS8GQNf72qwnA5bebjRe3hi66KXLaJU2d5Tkpe4eRutgucvKFFBk8MxkY';

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturn($traceID);

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturn($spanID);

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturn($parentSpanID);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'type'   => 'MQTT-response',
            'domain' => 'host',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $headers = '{"type":"PUBLISH_ACKNOWLEDGEMENT","qualityOfService":1,"messageId":10,"topic":"testTopic","acknowledgedQualityOfServices":[]}';

        $fields = [
            'url'             => 'testTopic',
            'startTimestamp'  => 1734352683.3516,
            'endTimestamp'    => 1734352683.3516,
            'executionTime'   => 0.0,
            'responseBody'    => $string . 'E984TBDFDAKJF',
            'responseHeaders' => $headers,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('setTraceId')
                    ->with($traceID);

        $this->event->expects($this->once())
                    ->method('setSpanId')
                    ->with($spanID);

        $this->event->expects($this->once())
                    ->method('setParentSpanId')
                    ->with($parentSpanID);

        $this->event->expects($this->once())
                    ->method('record');

        $this->message->expects($this->exactly(3))
                      ->method('getType')
                      ->willReturn(MessageType::PUBLISH_ACKNOWLEDGEMENT());

        $this->message->expects($this->once())
                      ->method('getContent')
                      ->willReturn($string . 'E984TBDFDAKJF');

        $this->message->expects($this->once())
                      ->method('getQualityOfService')
                      ->willReturn(1);

        $this->message->expects($this->once())
                      ->method('getTopic')
                      ->willReturn('testTopic');

        $this->message->expects($this->exactly(3))
                      ->method('getMessageId')
                      ->willReturn(10);

        $floatval  = 1734352683.3516;
        $stringval = '0.35160200 1734352683';

        $this->mockFunction('microtime', fn(bool $float) => $float ? $floatval : $stringval);

        $method = $this->getReflectionMethod('handleMessage');
        $method->invokeArgs($this->class, [ $this->message ]);

        $this->unmockFunction('microtime');
    }

}

?>
