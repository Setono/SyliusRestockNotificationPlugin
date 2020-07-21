<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Controller\Action;

use function Safe\sprintf;
use Setono\SyliusRestockNotificationPlugin\Form\Type\NotificationShopType;
use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;
use Setono\SyliusRestockNotificationPlugin\Repository\NotificationRepositoryInterface;
use Setono\SyliusRestockNotificationPlugin\Resolver\OutOfStockResolverInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class AvailableNotificationsForProduct
{
    /** @var Environment */
    private $twig;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var OutOfStockResolverInterface */
    private $outOfStockResolver;

    /** @var FactoryInterface */
    private $notificationFactory;

    /** @var NotificationRepositoryInterface */
    private $notificationRepository;

    /** @var FlashBagInterface */
    private $flashBag;

    public function __construct(
        Environment $twig,
        ProductRepositoryInterface $productRepository,
        FormFactoryInterface $formFactory,
        OutOfStockResolverInterface $outOfStockResolver,
        FactoryInterface $notificationFactory,
        NotificationRepositoryInterface $notificationRepository,
        FlashBagInterface $flashBag
    ) {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
        $this->formFactory = $formFactory;
        $this->outOfStockResolver = $outOfStockResolver;
        $this->notificationFactory = $notificationFactory;
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

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var NotificationInterface $notification */
                $notification = $form->getData();

                if (!$this->notificationRepository->hasNotification($notification)) {
                    $this->notificationRepository->add($notification);
                }

                $this->flashBag->add('success', 'setono_sylius_restock_notification.notification.subscribed');

                return new RedirectResponse($request->headers->get('referer'));
            }
            if (Request::METHOD_POST === $request->getMethod()) {
                // This is to allow to render form errors since this is loaded by subrender method
                // In case of post request, we redirect user back to where he is coming from, and pass POST params as GET
                return new RedirectResponse(sprintf('%s?%s', $request->headers->get('referer'), \http_build_query($request->request->all())));
            }
        }

        return new Response($this->twig->render(
            '@SetonoSyliusRestockNotificationPlugin/shop/notification/available/content.html.twig',
            [
                'form' => $form->createView(),
                'product' => $product,
            ]
        ));
    }
}
