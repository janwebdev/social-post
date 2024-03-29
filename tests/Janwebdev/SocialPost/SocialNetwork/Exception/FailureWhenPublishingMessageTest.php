<?php

declare(strict_types=1);

namespace Janwebdev\Tests\SocialPost\SocialNetwork\Exception;

use Janwebdev\SocialPost\SocialNetwork\Exception\FailureWhenPublishingMessage;
use PHPUnit\Framework\TestCase;

/**
 * @license https://opensource.org/licenses/MIT
 * @link https://github.com/janwebdev/social-post
 */
class FailureWhenPublishingMessageTest extends TestCase
{
    /**
     * @test
     */
    public function is_exception(): void
    {
        $exception = new \Exception('test exception');
        $implementation = new FailureWhenPublishingMessage($exception);
        $this->assertInstanceOf(\DomainException::class, $implementation);
    }
}
