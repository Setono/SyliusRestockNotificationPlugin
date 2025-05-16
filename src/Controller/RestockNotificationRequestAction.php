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
            /** @var mixed $data */
            $data = $form->getData();
            Assert::isInstanceOf($data, RestockNotificationRequestInterface::class);

            $manager = $this->getManager($data);
            $manager->persist($data);
            $manager->flush();

            // todo add flash
            // todo redirect to referrer or to product
        }

        return new RedirectResponse('/en_US/');
    }
}
