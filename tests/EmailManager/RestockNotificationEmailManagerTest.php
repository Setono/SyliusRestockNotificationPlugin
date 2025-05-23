<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Tests\EmailManager;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusRestockNotificationPlugin\EmailManager\RestockNotificationEmailManager;
use Setono\SyliusRestockNotificationPlugin\Mailer\Emails;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Setono\SyliusRestockNotificationPlugin\Resolver\ImageUrlResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class RestockNotificationEmailManagerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<SenderInterface> */
    private ObjectProphecy $sender;

    /** @var ObjectProphecy<ImageUrlResolverInterface> */
    private ObjectProphecy $imageUrlResolver;

    protected function setUp(): void
    {
        $this->sender = $this->prophesize(SenderInterface::class);
        $this->imageUrlResolver = $this->prophesize(ImageUrlResolverInterface::class);
    }

    /**
     * @test
     */
    public function it_sends_email_with_correct_parameters(): void
    {
        $emailManager = new RestockNotificationEmailManager(
            $this->sender->reveal(),
            $this->imageUrlResolver->reveal(),
        );

        $email = 'customer@example.com';
        $localeCode = 'en_US';
        $imageUrl = 'https://example.com/image.jpg';

        $channel = $this->prophesize(ChannelInterface::class);
        $request = $this->prophesize(RestockNotificationRequestInterface::class);

        $request->getChannel()->willReturn($channel->reveal());
        $request->getLocaleCode()->willReturn($localeCode);
        $request->getEmail()->willReturn($email);

        $this->imageUrlResolver->resolve($request->reveal())->willReturn($imageUrl);

        $this->sender->send(
            Emails::RESTOCK_NOTIFICATION_REQUEST,
            [$email],
            [
                'restockNotificationRequest' => $request->reveal(),
                'channel' => $channel->reveal(),
                'localeCode' => $localeCode,
                'imageUrl' => $imageUrl,
            ],
        )->shouldBeCalled();

        $emailManager->sendRestockNotificationEmail($request->reveal());
    }

    /**
     * @test
     */
    public function it_throws_exception_when_channel_is_null(): void
    {
        $emailManager = new RestockNotificationEmailManager(
            $this->sender->reveal(),
            $this->imageUrlResolver->reveal(),
        );

        $request = $this->prophesize(RestockNotificationRequestInterface::class);
        $request->getChannel()->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $emailManager->sendRestockNotificationEmail($request->reveal());
    }

    /**
     * @test
     */
    public function it_throws_exception_when_locale_code_is_null(): void
    {
        $emailManager = new RestockNotificationEmailManager(
            $this->sender->reveal(),
            $this->imageUrlResolver->reveal(),
        );

        $channel = $this->prophesize(ChannelInterface::class);
        $request = $this->prophesize(RestockNotificationRequestInterface::class);

        $request->getChannel()->willReturn($channel->reveal());
        $request->getLocaleCode()->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $emailManager->sendRestockNotificationEmail($request->reveal());
    }

    /**
     * @test
     */
    public function it_throws_exception_when_email_is_null(): void
    {
        $emailManager = new RestockNotificationEmailManager(
            $this->sender->reveal(),
            $this->imageUrlResolver->reveal(),
        );

        $channel = $this->prophesize(ChannelInterface::class);
        $request = $this->prophesize(RestockNotificationRequestInterface::class);

        $request->getChannel()->willReturn($channel->reveal());
        $request->getLocaleCode()->willReturn('en_US');
        $request->getEmail()->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $emailManager->sendRestockNotificationEmail($request->reveal());
    }
}
