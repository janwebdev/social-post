<?php

declare(strict_types=1);

namespace Janwebdev\SocialPost\SocialNetwork;

use Janwebdev\SocialPost\Message;
use Janwebdev\SocialPost\Publisher;
use Janwebdev\SocialPost\SocialNetwork\Exception\FailureWhenPublishingMessage;

/**
 * Main contract for publishing a new public message at a social network account
 *
 * @license https://opensource.org/licenses/MIT
 * @link https://github.com/janwebdev/social-post
 */
class AllInOne implements Publisher
{
    /**
     * @var Publisher[]
     */
    private array $publishers = [];

    public function __construct(Publisher ...$publishers)
    {
        foreach ($publishers as $publisher) {
            $this->publishers[] = $publisher;
        }
    }

    public function canPublish(Message $message): bool
    {
        return true;
    }

    public function publish(Message $message): bool
    {
        try {
            $allPublished = true;
            foreach ($this->publishers as $publisher) {
                if ($publisher->canPublish($message)) {
                    $allPublished &= $publisher->publish($message);
                }
            }

            return (bool) $allPublished;
        } catch (\Exception $e) {
            throw new FailureWhenPublishingMessage($e);
        }
    }
}
