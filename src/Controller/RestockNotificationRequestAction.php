<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusRestockNotificationPlugin\Factory\RestockNotificationRequestFactoryInterface;
use Setono\SyliusRestockNotificationPlugin\Form\Type\RestockNotificationShopRequestType;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class RestockNotificationRequestAction
{
    use ORMTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly FormFactoryInterface $formFactory,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RestockNotificationRequestFactoryInterface $restockNotificationRequestFactory,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $form = $this->formFactory->create(RestockNotificationShopRequestType::class, $this->restockNotificationRequestFactory->createWithChannelAndLocaleContext());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var mixed|RestockNotificationRequestInterface $data */
            $data = $form->getData();
            Assert::isInstanceOf($data, RestockNotificationRequestInterface::class);

            $manager = $this->getManager($data);
            $manager->persist($data);
            $manager->flush();

            self::addFlash($request, 'success', 'setono_sylius_restock_notification.restock_notification_request_created');

            return self::createRedirect($request, $this->urlGenerator->generate('sylius_shop_product_show', [
                'slug' => $data->getProductVariant()?->getProduct()?->getSlug(),
            ]));
        }

        self::addFlash($request, 'error', 'setono_sylius_restock_notification.restock_notification_request_failed');

        return self::createRedirect($request, $this->urlGenerator->generate('sylius_shop_homepage'));
    }

    /**
     * @param 'success'|'error' $type
     */
    private static function addFlash(Request $request, string $type, string $message): void
    {
        $session = $request->getSession();
        if ($session instanceof Session) {
            $session->getFlashBag()->add($type, $message);
        }
    }

    private static function createRedirect(Request $request, string $default): RedirectResponse
    {
        $referrer = $request->headers->get('referer');
        if (null !== $referrer && '' !== $referrer) {
            return new RedirectResponse($referrer);
        }

        return new RedirectResponse($default);
    }
}
