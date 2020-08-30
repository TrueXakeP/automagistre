<?php

declare(strict_types=1);

namespace App\Manufacturer\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Manufacturer\Entity\Manufacturer;
use App\Manufacturer\Entity\ManufacturerId;
use App\Manufacturer\Form\ManufacturerDto;
use App\Manufacturer\Form\ManufacturerType;
use function array_map;
use function str_replace;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ManufacturerController extends AbstractController
{
    public function widgetAction(): Response
    {
        $request = $this->request;

        /** @var ManufacturerDto $dto */
        $dto = $this->createWithoutConstructor(ManufacturerDto::class);

        $form = $this->createForm(ManufacturerType::class, $dto)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $id = ManufacturerId::generate();

            $em->persist(
                new Manufacturer(
                    $id,
                    $dto->name,
                    $dto->localizedName,
                ),
            );
            $em->flush();

            return new JsonResponse([
                'id' => $id->toString(),
                'text' => $this->display($id),
            ]);
        }

        return $this->render('easy_admin/widget.html.twig', [
            'id' => 'manufacturer',
            'label' => 'Новый производитель',
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $request = $this->request;

        $queryString = str_replace(['.', ',', '-', '_'], '', (string) $request->query->get('query'));
        $qb = $this->createSearchQueryBuilder((string) $request->query->get('entity'), $queryString, []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $request->query->getInt('page', 1));

        return $this->json([
            'results' => array_map(
                fn (Manufacturer $manufacturer) => [
                    'id' => $manufacturer->toId()->toString(),
                    'text' => $this->display($manufacturer->toId()),
                ],
                (array) $paginator->getCurrentPageResults()
            ),
        ]);
    }
}
