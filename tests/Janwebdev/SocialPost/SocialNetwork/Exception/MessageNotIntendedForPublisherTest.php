<?php

declare(strict_types=1);

namespace Janwebdev\Tests\SocialPost\SocialNetwork\Exception;

use Janwebdev\SocialPost\SocialNetwork\Exception\MessageNotIntendedForPublisher;
use PHPUnit\Framework\TestCase;

/**
 * @license https://opensource.org/licenses/MIT
 * @link https://github.com/janwebdev/social-post
 */
class MessageNotIntendedForPublisherTest extends TestCase
{
    /**
     * @test
     */
    public function is_exception(): void
    {
        $implementation = new MessageNotIntendedForPublisher('facebook');
        $this->assertInstanceOf(\DomainException::class, $implementation);
    }
}
