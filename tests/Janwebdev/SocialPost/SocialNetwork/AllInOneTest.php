<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\SocialPost\SocialNetwork;

use MartinGeorgiev\SocialPost\Message;
use MartinGeorgiev\SocialPost\SocialNetwork\AllInOne;
use MartinGeorgiev\SocialPost\SocialNetwork\Exception\FailureWhenPublishingMessage;
use MartinGeorgiev\SocialPost\SocialNetwork\Facebook\FacebookSDK;
use MartinGeorgiev\SocialPost\SocialNetwork\Twitter\TwitterSDK;
use PHPUnit\Framework\TestCase;

/**
 * @license https://opensource.org/licenses/MIT
 * @link https://github.com/janwebdev/social-post
 */
class AllInOneTest extends TestCase
{
    /**
     * @test
     */
    public function can_publish_any_message(): void
    {
        $socialPost = 'test message';
        $message = new Message($socialPost);

        $facebook = $this
            ->getMockBuilder(FacebookSDK::class)
            ->disableOriginalConstructor()
            ->getMock();

        $allInOne = new AllInOne($facebook);
        $this->assertTrue($allInOne->canPublish($message));
    }

    /**
     * @test
     */
    public function can_successfully_publish_to_all_providers(): void
    {
        $socialPost = 'test message';
        $message = new Message($socialPost);

        $facebook = $this
            ->getMockBuilder(FacebookSDK::class)
            ->disableOriginalConstructor()
            ->setMethods(['publish'])
            ->getMock();
        $facebook
            ->expects($this->once())
            ->method('publish')
            ->with($message)
            ->willReturn(true);

        $twitter = $this
            ->getMockBuilder(TwitterSDK::class)
            ->disableOriginalConstructor()
            ->setMethods(['publish'])
            ->getMock();
        $twitter
            ->expects($this->once())
            ->method('publish')
            ->with($message)
            ->willReturn(true);

        $allInOne = new AllInOne($facebook, $twitter);
        $this->assertTrue($allInOne->publish($message));
    }

    /**
     * @test
     */
    public function will_fail_if_cannot_successfully_publish_to_all_providers(): void
    {
        $socialPost = 'test message';
        $message = new Message($socialPost);

        $facebook = $this
            ->getMockBuilder(FacebookSDK::class)
            ->disableOriginalConstructor()
            ->setMethods(['publish'])
            ->getMock();
        $facebook
            ->expects($this->once())
            ->method('publish')
            ->with($message)
            ->willReturn(true);

        $twitter = $this
            ->getMockBuilder(TwitterSDK::class)
            ->disableOriginalConstructor()
            ->setMethods(['publish'])
            ->getMock();
        $twitter
            ->expects($this->once())
            ->method('publish')
            ->with($message)
            ->willReturn(false);

        $allInOne = new AllInOne($facebook, $twitter);
        $this->assertFalse($allInOne->publish($message));
    }

    /**
     * @test
     */
    public function will_throw_an_exception_if_completly_fails_to_publish(): void
    {
        $socialPost = 'test message';
        $message = new Message($socialPost);

        $facebook = $this
            ->getMockBuilder(FacebookSDK::class)
            ->disableOriginalConstructor()
            ->setMethods(['publish'])
            ->getMock();
        $facebook
            ->expects($this->once())
            ->method('publish')
            ->with($message)
            ->willReturn(true);

        $exception = new FailureWhenPublishingMessage(new \Exception('test exception'));
        $twitter = $this
            ->getMockBuilder(TwitterSDK::class)
            ->disableOriginalConstructor()
            ->setMethods(['publish'])
            ->getMock();
        $twitter
            ->expects($this->once())
            ->method('publish')
            ->with($message)
            ->willThrowException($exception);

        $allInOne = new AllInOne($facebook, $twitter);

        $this->expectException(FailureWhenPublishingMessage::class);
        $allInOne->publish($message);
    }
}
