<?php

declare(strict_types=1);

namespace Janwebdev\SocialPost\SocialNetwork\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use Janwebdev\SocialPost\Message;
use Janwebdev\SocialPost\Publisher;
use Janwebdev\SocialPost\SocialNetwork\Enum;
use Janwebdev\SocialPost\SocialNetwork\Exception\FailureWhenPublishingMessage;
use Janwebdev\SocialPost\SocialNetwork\Exception\MessageNotIntendedForPublisher;

/**
 * @license https://opensource.org/licenses/MIT
 * @link https://github.com/janwebdev/social-post
 */
class TwitterSDK implements Publisher
{
    public const TEXT_MAX_LENGTH = 280;

    /**
     * @var TwitterOAuth
     */
    private TwitterOAuth $twitter;

    /**
     * @param TwitterOAuth $twitter Ready to use instance of TwitterOAuth
     */
    public function __construct(TwitterOAuth $twitter)
    {
        $this->twitter = $twitter;
    }

    public function canPublish(Message $message): bool
    {
        return !empty(array_intersect($message->getNetworksToPublishOn(), [Enum::ANY, Enum::TWITTER]));
    }

    public function publish(Message $message): bool
    {
        if (!$this->canPublish($message)) {
            throw new MessageNotIntendedForPublisher(Enum::TWITTER);
        }

        try {

            $mediaId = null;
            if ($message->getPictureLink()) {
                $this->twitter->setApiVersion('1.1');
                $mediaResponse = $this->twitter->upload('media/upload', ['media' => $message->getPictureLink()]);
                $mediaId = $mediaResponse->media_id;
            }


            $status = $this->prepareStatus($message);

            $parameters = [
                'text' => $status
            ];

            if ($mediaId) {
                $parameters = array_merge($parameters, [
                    'media' => [
                        'media_ids' => [(string)$mediaId]
                    ]
                ]);
            }

            $this->twitter->setApiVersion('2');
            $post = $this->twitter->post('tweets', $parameters);

            return !empty($post->id);

        } catch (\Exception $e) {
            throw new FailureWhenPublishingMessage($e);
        }
    }

    private function prepareStatus(Message $message): string
    {
        $status = $message->getMessage();

        if (filter_var($message->getLink(), FILTER_VALIDATE_URL) !== false) {
            $linkIsNotIncludedInTheStatus = mb_strpos($status, $message->getLink()) === false;
            if ($linkIsNotIncludedInTheStatus) {
                $status .= ' '.$message->getLink();
            }
        }

        if (mb_strlen($status) > self::TEXT_MAX_LENGTH) {
            throw new \Exception('Message it too long for publishing');
        }

        return $status;
    }
}
