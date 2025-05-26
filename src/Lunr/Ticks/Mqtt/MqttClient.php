<?php

/**
 * This file contains the MqttClient class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Mqtt;

use Lunr\Ticks\AnalyticsDetailLevel;
use Lunr\Ticks\EventLogging\EventLoggerInterface;
use Lunr\Ticks\TracingControllerInterface;
use Lunr\Ticks\TracingInfoInterface;
use PhpMqtt\Client\Contracts\MessageProcessor;
use PhpMqtt\Client\Contracts\Repository;
use PhpMqtt\Client\Logger;
use PhpMqtt\Client\MessageProcessors\Mqtt311MessageProcessor;
use PhpMqtt\Client\MessageProcessors\Mqtt31MessageProcessor;
use PhpMqtt\Client\MqttClient as BaseMqttClient;
use Psr\Log\LoggerInterface;

/**
 * Overrides MqttClient methods so we can inject introspection calls.
 *
 * @phpstan-type TracingInterface TracingControllerInterface&TracingInfoInterface
 */
class MqttClient extends BaseMqttClient
{

    /**
     * Instance of the message processor.
     * @var MessageProcessor
     */
    private MessageProcessor $messageProcessor;

    /**
     * Instance of an EventLogger
     * @var EventLoggerInterface
     */
    private readonly EventLoggerInterface $eventLogger;

    /**
     * Shared instance of a tracing controller
     * @var TracingInterface
     */
    private readonly TracingControllerInterface&TracingInfoInterface $tracingController;

    /**
     * Current profiling level
     * @var AnalyticsDetailLevel
     */
    private AnalyticsDetailLevel $level;

    /**
     * Array of request/response data
     * @var array<string, string>
     */
    protected array $data;

    /**
     * Constructor with MQTT Logger
     *
     * @param EventLoggerInterface $eventLogger       Instance of an event logger
     * @param TracingInterface     $tracingController Instance of a tracing controller
     * @param string               $host              Host to connect to
     * @param int                  $port              Port for connection
     * @param string|NULL          $clientId          Client ID
     * @param string               $protocol          Connection protocol
     * @param Repository|NULL      $repository        Repository
     * @param LoggerInterface|NULL $logger            Logger
     */
    public function __construct(
        EventLoggerInterface $eventLogger,
        TracingControllerInterface&TracingInfoInterface $tracingController,
        string $host,
        int $port = 1883,
        ?string $clientId = NULL,
        string $protocol = self::MQTT_3_1,
        ?Repository $repository = NULL,
        ?LoggerInterface $logger = NULL
    )
    {
        $clientId = $clientId ?? $this->generateRandomClientId();

        parent::__construct($host, $port, $clientId, $protocol, $repository, $logger);

        $logger = new Logger($host, $port, $clientId, $logger);

        switch ($protocol)
        {
            case self::MQTT_3_1_1:
                $this->messageProcessor = new Mqtt311MessageProcessor($clientId, $logger);
                break;
            case self::MQTT_3_1:
            default:
                $this->messageProcessor = new Mqtt31MessageProcessor($clientId, $logger);
                break;
        }

        $this->eventLogger       = $eventLogger;
        $this->tracingController = $tracingController;

        $this->data  = [];
        $this->level = AnalyticsDetailLevel::Info;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->data);
        unset($this->level);
        unset($this->messageProcessor);
    }

}

?>
