<?php

namespace Swarrot\Tests\Broker\MessageProvider;

use Aws\Sqs\SqsClient;
use Guzzle\Service\Resource\Model;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Swarrot\Broker\Message;
use Swarrot\Broker\MessageProvider\MessageProviderInterface;
use Swarrot\Broker\MessageProvider\SqsMessageProvider;
use Swarrot\Driver\MessageCacheInterface;
use Swarrot\Driver\PrefetchMessageCache;

/**
 * Class SqsMessageProviderTest.
 */
class SqsMessageProviderTest extends TestCase
{
    /**
     * @var SqsMessageProvider
     */
    protected $provider;

    protected $channel;
    protected $cache;

    /**
     * @group legacy
     * @expectedDeprecation "Swarrot\Broker\MessageProvider\SqsMessageProvider" have been deprecated since Swarrot 3.5
     */
    public function setUp(): void
    {
        $this->channel = $this->prophesize(SqsClient::class);
        $this->cache = $this->prophesize(MessageCacheInterface::class);

        $this->provider = new SqsMessageProvider($this->channel->reveal(), 'foo', $this->cache->reveal());
    }

    /**
     * @group legacy
     * @expectedDeprecation "Swarrot\Broker\MessageProvider\SqsMessageProvider" have been deprecated since Swarrot 3.5
     */
    public function testInstance()
    {
        $this->assertInstanceOf(MessageProviderInterface::class, $this->provider);
    }

    /**
     * @group legacy
     * @expectedDeprecation "Swarrot\Broker\MessageProvider\SqsMessageProvider" have been deprecated since Swarrot 3.5
     */
    public function testGetWithNoCache()
    {
        $cache = new PrefetchMessageCache();

        $this->provider = new SqsMessageProvider($this->channel->reveal(), 'foo', $cache);

        $response = $this->prophesize(Model::class);
        $response->get(Argument::any())->willReturn([
            [
                'Body' => 'Body',
                'ReceiptHandle' => 'bar',
            ],
        ]);
        $this->channel->receiveMessage(Argument::any())->willReturn($response);

        $this->assertInstanceOf(Message::class, $this->provider->get());

        $this->channel->receiveMessage([
            'QueueUrl' => 'foo',
            'MaxNumberOfMessages' => 9,
            'WaitTimeSeconds' => 5,
        ])->shouldBeCalled();
    }

    /**
     * @group legacy
     * @expectedDeprecation "Swarrot\Broker\MessageProvider\SqsMessageProvider" have been deprecated since Swarrot 3.5
     */
    public function testGetWithNoResult()
    {
        $this->cache->pop(Argument::any())->willReturn(null);

        $response = $this->prophesize(Model::class);
        $response->get(Argument::any())->willReturn(null);
        $this->channel->receiveMessage(Argument::any())->willReturn($response);

        $this->assertNull($this->provider->get());

        $this->channel->receiveMessage([
            'QueueUrl' => 'foo',
            'MaxNumberOfMessages' => 9,
            'WaitTimeSeconds' => 5,
        ])->shouldBeCalled();
    }

    /**
     * @group legacy
     * @expectedDeprecation "Swarrot\Broker\MessageProvider\SqsMessageProvider" have been deprecated since Swarrot 3.5
     */
    public function testGetWithCache()
    {
        $message = $this->prophesize(Message::class);

        $this->cache->pop(Argument::any())
            ->willReturn($message);

        $this->assertInstanceOf(Message::class, $this->provider->get());

        $this->cache->push(Argument::any())->shouldNotBeCalled();
        $this->channel->receiveMessage(Argument::any())->shouldNotBeCalled();
    }

    /**
     * @group legacy
     * @expectedDeprecation "Swarrot\Broker\MessageProvider\SqsMessageProvider" have been deprecated since Swarrot 3.5
     */
    public function testAck()
    {
        $message = $this->prophesize(Message::class);
        $message->getId()->willReturn('123');

        $this->channel->deleteMessage([
            'QueueUrl' => 'foo',
            'ReceiptHandle' => '123',
        ])->shouldBeCalled();

        $this->provider->ack($message->reveal());
    }

    /**
     * @group legacy
     * @expectedDeprecation "Swarrot\Broker\MessageProvider\SqsMessageProvider" have been deprecated since Swarrot 3.5
     */
    public function testNackWithRequeue()
    {
        $message = $this->prophesize(Message::class);
        $message->getId()->willReturn('123');

        $this->channel->changeMessageVisibility([
            'QueueUrl' => 'foo',
            'ReceiptHandle' => '123',
            'VisibilityTimeout' => 0,
        ])->shouldBeCalled();
        $this->channel->deleteMessage(Argument::any())->shouldNotBeCalled();

        $this->provider->nack($message->reveal(), true);
    }

    /**
     * @group legacy
     * @expectedDeprecation "Swarrot\Broker\MessageProvider\SqsMessageProvider" have been deprecated since Swarrot 3.5
     */
    public function testNackWithoutRequeue()
    {
        $message = $this->prophesize(Message::class);
        $message->getId()->willReturn('123');

        $this->channel->changeMessageVisibility(Argument::any())->shouldNotBeCalled();
        $this->channel->deleteMessage(Argument::any())->shouldNotBeCalled();

        $this->provider->nack($message->reveal());
    }
}
