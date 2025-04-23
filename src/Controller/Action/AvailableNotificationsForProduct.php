<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Controller\Action;

use Setono\SyliusRestockNotificationPlugin\Form\Type\RestockNotificationShopRequestType;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Setono\SyliusRestockNotificationPlugin\Repository\RestockNotificationRequestRepositoryInterface;
use Setono\SyliusRestockNotificationPlugin\Resolver\OutOfStockResolverInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class AvailableNotificationsForProduct
{
    public function __construct(
        private readonly Environment $twig,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly OutOfStockResolverInterface $outOfStockResolver,
        private readonly FactoryInterface $notificationFactory,
        private readonly RestockNotificationRequestRepositoryInterface $notificationRepository,
    ) {
    }

    public function __invoke(Request $request, string $code): Response
    {
        $product = $this->productRepository->findOneByCode($code);
        if (null === $product) {
            throw new NotFoundHttpException(sprintf('The product with code "%s" does not exist', $code));
        }

        // if none of the product variants is out of stock, do not show anything
        if (!$this->outOfStockResolver->hasVariantsOutOfStock($product)) {
            return new Response('');
        }

        $form = $this->formFactory->create(RestockNotificationShopRequestType::class, $this->notificationFactory->createNew(), [
            'product' => $product,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var RestockNotificationRequestInterface $notification */
                $notification = $form->getData();

                if (!$this->notificationRepository->hasRestockNotificationRequest($notification)) {
                    $this->notificationRepository->add($notification);
                }

                //$this->flashBag->add('success', 'setono_sylius_restock_notification.notification.subscribed');

                return new RedirectResponse((string) $request->headers->get('referer'));
            }

            $formErrors = $form->getErrors(true, true);
            $errorMessages = [];
            /** @var FormError $formError */
            foreach ($formErrors as $formError) {
                $errorMessages[] = $formError->getMessage();
            }

            //$this->flashBag->add('error', ['message' => 'setono_sylius_restock_notification.notification.error', 'parameters' => ['%errors%' => \implode(', ', $errorMessages)]]);

            // Redirect the user back to where he is coming from since this request can be a sub one
            return new RedirectResponse((string) $request->headers->get('referer'));
        }

        return new Response($this->twig->render(
            '@SetonoSyliusRestockNotificationPlugin/shop/notification/available/content.html.twig',
            [
                'form' => $form->createView(),
                'product' => $product,
            ],
        ));
    }
}
