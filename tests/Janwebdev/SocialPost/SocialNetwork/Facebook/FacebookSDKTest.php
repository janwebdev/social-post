<?php

declare(strict_types=1);

namespace Janwebdev\Tests\SocialPost\SocialNetwork\Facebook;

use JanuSoftware\Facebook\Facebook;
use JanuSoftware\Facebook\Response as FacebookResponse;
use Janwebdev\SocialPost\Message;
use Janwebdev\SocialPost\SocialNetwork\Enum;
use Janwebdev\SocialPost\SocialNetwork\Exception\FailureWhenPublishingMessage;
use Janwebdev\SocialPost\SocialNetwork\Exception\MessageNotIntendedForPublisher;
use Janwebdev\SocialPost\SocialNetwork\Facebook\FacebookSDK;
use PHPUnit\Framework\TestCase;

/**
 * @license https://opensource.org/licenses/MIT
 * @link https://github.com/janwebdev/social-post
 */
class FacebookSDKTest extends TestCase
{
    /**
     * @test
     */
    public function can_publish_only_facebook_intended_messages(): void
    {
        $pageId = '2009';
        $facebook = $this
            ->getMockBuilder(Facebook::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        $statusUpdate = 'test message';
        $message = new Message($statusUpdate);
        $message->setNetworksToPublishOn([Enum::FACEBOOK]);

        $facebookProvider = new FacebookSDK($facebook, $pageId);
        $this->assertTrue($facebookProvider->canPublish($message));
    }

    /**
     * @test
     */
    public function cannot_publish_when_message_not_intended_for_facebook(): void
    {
        $pageId = '2009';
        $facebook = $this
            ->getMockBuilder(Facebook::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        $statusUpdate = 'test message';
        $message = new Message($statusUpdate);
        $message->setNetworksToPublishOn([Enum::TWITTER]);

        $facebookProvider = new FacebookSDK($facebook, $pageId);
        $this->assertFalse($facebookProvider->canPublish($message));
    }

    /**
     * @test
     */
    public function will_throw_an_exception_when_publishing_if_message_is_not_intended_for_facebook(): void
    {
        $pageId = '2009';
        $facebook = $this
            ->getMockBuilder(Facebook::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        $tweet = 'test message';
        $message = new Message($tweet);
        $message->setNetworksToPublishOn([Enum::TWITTER]);

        $facebookProvider = new FacebookSDK($facebook, $pageId);

        $this->expectException(MessageNotIntendedForPublisher::class);
        $facebookProvider->publish($message);
    }

    /**
     * @test
     */
    public function can_successfully_publish_as_a_page(): void
    {
        $pageId = '2009';
        $endpoint = sprintf('/%s/feed', $pageId);

        $facebookResponse = $this
            ->getMockBuilder(FacebookResponse::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGraphNode'])
            ->getMock();

        $post = ['id' => '2013'];
        $facebookResponse
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($post);

        $facebook = $this
            ->getMockBuilder(Facebook::class)
            ->disableOriginalConstructor()
            ->setMethods(['post'])
            ->getMock();

        $statusUpdate = 'test status update';
        $link = 'https://www.example.com';
        $pictureLink = 'https://www.example.com/logo.svg';
        $caption = 'some caption';
        $description = 'some description';
        $message = new Message($statusUpdate, $link, $pictureLink, $caption, $description);
        $data = ['message' => $statusUpdate, 'link' => $link, 'picture' => $pictureLink, 'caption' => $caption, 'description' => $description];
        $facebook
            ->expects($this->once())
            ->method('post')
            ->with($endpoint, $data)
            ->willReturn($facebookResponse);

        $facebookProvider = new FacebookSDK($facebook, $pageId);
        $this->assertTrue($facebookProvider->publish($message));
    }

    /**
     * @test
     */
    public function will_fail_if_cannot_find_the_id_of_the_new_post(): void
    {
        $pageId = '2009';
        $endpoint = sprintf('/%s/feed', $pageId);

        $facebookResponse = $this
            ->getMockBuilder(FacebookResponse::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGraphNode'])
            ->getMock();

        $post = ['id' => ''];
        $facebookResponse
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($post);

        $facebook = $this
            ->getMockBuilder(Facebook::class)
            ->disableOriginalConstructor()
            ->setMethods(['post'])
            ->getMock();

        $statusUpdate = 'test status update';
        $message = new Message($statusUpdate);
        $data = ['message' => $statusUpdate];
        $facebook
            ->expects($this->once())
            ->method('post')
            ->with($endpoint, $data)
            ->willReturn($facebookResponse);

        $facebookProvider = new FacebookSDK($facebook, $pageId);
        $this->assertFalse($facebookProvider->publish($message));
    }

    /**
     * @test
     */
    public function will_throw_an_exception_if_completely_fails_to_publish(): void
    {
        $pageId = '2009';
        $facebook = $this
            ->getMockBuilder(Facebook::class)
            ->disableOriginalConstructor()
            ->setMethods(['post'])
            ->getMock();

        $statusUpdate = 'test status update';
        $message = new Message($statusUpdate);
        $facebook
            ->expects($this->once())
            ->method('post')
            ->willThrowException(new \Exception('something went wrong'));

        $facebookProvider = new FacebookSDK($facebook, $pageId);

        $this->expectException(FailureWhenPublishingMessage::class);
        $facebookProvider->publish($message);
    }
}
