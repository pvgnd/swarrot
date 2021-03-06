<?php

namespace Swarrot\Driver;

use Swarrot\Broker\Message;

/**
 * @deprecated
 */
interface MessageCacheInterface
{
    /**
     * Pushes a message to the end of the cache.
     *
     * @param string $queueName
     */
    public function push($queueName, Message $message);

    /**
     * Get the next message in line. Or nothing if there is no more
     * in the cache.
     *
     * @param string $queueName
     *
     * @return Message|null
     */
    public function pop($queueName);
}
