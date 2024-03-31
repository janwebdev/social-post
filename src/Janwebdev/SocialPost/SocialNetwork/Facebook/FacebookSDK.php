<?php

declare(strict_types=1);

namespace Janwebdev\SocialPost\SocialNetwork\Facebook;

use JanuSoftware\Facebook\Facebook;
use Janwebdev\SocialPost\Message;
use Janwebdev\SocialPost\Publisher;
use Janwebdev\SocialPost\SocialNetwork\Enum;
use Janwebdev\SocialPost\SocialNetwork\Exception\FailureWhenPublishingMessage;
use Janwebdev\SocialPost\SocialNetwork\Exception\MessageNotIntendedForPublisher;

/**
 * @license https://opensource.org/licenses/MIT
 * @link https://github.com/janwebdev/social-post
 */
class FacebookSDK implements Publisher
{
    /**
     * @var Facebook
     */
    private Facebook $facebook;

    /**
     * @var string
     */
    private string $pageId;

    /**
     * @param Facebook $facebook Ready to use instance of the Facebook PHP SDK
     * @param string $pageId Identifier of the page, on which the status update will be published
     */
    public function __construct(Facebook $facebook, string $pageId)
    {
        $this->facebook = $facebook;
        $this->pageId = $pageId;
    }

    public function canPublish(Message $message): bool
    {
        return !empty(array_intersect($message->getNetworksToPublishOn(), [Enum::ANY, Enum::FACEBOOK]));
    }

    public function publish(Message $message): bool
    {
        if (!$this->canPublish($message)) {
            throw new MessageNotIntendedForPublisher(Enum::FACEBOOK);
        }

        try {
            $publishPostEndpoint = '/'.$this->pageId.'/feed';
            $response = $this->facebook->post(
                $publishPostEndpoint,
                $this->prepareParams($message)
            );
            $post = $response->getGraphNode();

            return !empty($post->getField('id'));

        } catch (\Exception $e) {
            throw new FailureWhenPublishingMessage($e);
        }
    }

    private function prepareParams(Message $message): array
    {
        $params = [];

        $params['message'] = $message->getMessage();

        if (filter_var($message->getLink(), FILTER_VALIDATE_URL) !== false) {
            $params['link'] = $message->getLink();
        }
        if (filter_var($message->getPictureLink(), FILTER_VALIDATE_URL) !== false) {
            $params['picture'] = $message->getPictureLink();
        }
        if (!empty($message->getCaption())) {
            $params['caption'] = $message->getCaption();
        }
        if (!empty($message->getDescription())) {
            $params['description'] = $message->getDescription();
        }

        return $params;
    }
}
