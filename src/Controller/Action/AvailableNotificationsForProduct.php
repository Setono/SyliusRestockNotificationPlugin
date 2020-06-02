<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Controller\Action;

use InvalidArgumentException;
use function Safe\sprintf;
use Setono\SyliusRestockNotificationPlugin\Form\Type\NotificationShopType;
use Setono\SyliusRestockNotificationPlugin\Repository\NotificationRepositoryInterface;
use Setono\SyliusRestockNotificationPlugin\Resolver\OutOfStockResolverInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class AvailableNotificationsForProduct
{
    /** @var Environment */
    private $twig;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var OutOfStockResolverInterface */
    private $outOfStockResolver;

    /** @var FactoryInterface */
    private $notificationFactory;

    /** @var RepositoryInterface */
    private $localeRepository;

    /** @var NotificationRepositoryInterface */
    private $notificationRepository;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        Environment $twig,
        ProductRepositoryInterface $productRepository,
        FormFactoryInterface $formFactory,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        OutOfStockResolverInterface $outOfStockResolver,
        FactoryInterface $notificationFactory,
        RepositoryInterface $localeRepository,
        NotificationRepositoryInterface $notificationRepository,
        FlashBagInterface $flashBag
    ) {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
        $this->formFactory = $formFactory;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->outOfStockResolver = $outOfStockResolver;
        $this->notificationFactory = $notificationFactory;
        $this->localeRepository = $localeRepository;
        $this->notificationRepository = $notificationRepository;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request, string $code): Response
    {
        $product = $this->productRepository->findOneByCode($code);
        if (null === $product) {
            throw new NotFoundHttpException(sprintf('The product with code "%s" does not exist', $code));
        }

        // if none of the products variants is out of stock do not show anything
        if (!$this->outOfStockResolver->hasVariantsOutOfStock($product)) {
            return new Response('');
        }

        $form = $this->formFactory->create(NotificationShopType::class, $this->notificationFactory->createNew(), [
            'product' => $product,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notification = $form->getData();

            $this->notificationRepository->add($notification);

            $this->flashBag->add('success', 'setono_sylius_restock_notification.notification.subscribed');

            return new RedirectResponse($request->headers->get('referer'));
        }

        return new Response($this->twig->render(
            '@SetonoSyliusRestockNotificationPlugin/shop/notification/available/content.html.twig',
            [
                'form' => $form->createView(),
                'product' => $product,
            ]
        ));
    }

    private function getLocaleFromCode(string $code): LocaleInterface
    {
        /** @var LocaleInterface|null $locale */
        $locale = $this->localeRepository->findOneBy([
            'code' => $code,
        ]);

        if (null === $locale) {
            throw new InvalidArgumentException(sprintf('The locale with code "%s" does not exist', $code));
        }

        return $locale;
    }
}
