<?php

namespace Swarrot\Driver;

use Swarrot\Broker\Message;

/**
 * Class PrefetchMessageCache.
 */
class PrefetchMessageCache implements MessageCacheInterface
{
    protected $caches = [];

    public function __construct()
    {
        @trigger_error(sprintf('"%s" have been deprecated since Swarrot 3.7', __CLASS__), E_USER_DEPRECATED);
    }

    /**
     * Pushes a message to the end of the cache.
     *
     * @param string $queueName
     */
    public function push($queueName, Message $message)
    {
        $cache = $this->get($queueName);
        $cache->enqueue($message);
    }

    /**
     * Get the next message in line. Or nothing if there is no more
     * in the cache.
     *
     * @param string $queueName
     *
     * @return Message|null
     */
    public function pop($queueName)
    {
        $cache = $this->get($queueName);

        if (!$cache->isEmpty()) {
            return $cache->dequeue();
        }
    }

    /**
     * Create the queue cache internally if it doesn't yet exists.
     *
     * @param string $queueName
     *
     * @return \SplQueue
     */
    protected function get($queueName)
    {
        if (isset($this->caches[$queueName])) {
            return $this->caches[$queueName];
        }

        return $this->caches[$queueName] = new \SplQueue();
    }
}
