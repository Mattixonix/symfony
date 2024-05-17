<?php

namespace MateuszJagielskiRekrutacjaSmartiveapp\Controller;

use MateuszJagielskiRekrutacjaSmartiveapp\Form\ThumbnailTypeForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontController extends AbstractController
{
    #[Route('/', name: 'front')]
    public function index(Request $request, ThumbnailTypeForm $thumbnailTypeForm): Response
    {
        $form = $this
            ->createForm(ThumbnailTypeForm::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $uploaded_file = $form->get('image')->getData();
            $thumbnail_entity = $thumbnailTypeForm->submitForm($data->getName(), $data->getDestination(), $uploaded_file, $data->getDropboxToken());

            if ($thumbnail_entity->getDestination() === 0) {
                $file = new File($thumbnail_entity->getImagePath());

                $response = new BinaryFileResponse($file->getRealPath());
                $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

                return $response;
            } else {
                return $this->render('front.html.twig', [
                    'thumbnail_type_form' => $form->createView(),
                    'dropbox_thumb_prepared' => true
                ]);
            }
        }

        return $this->render('front.html.twig', [
            'thumbnail_type_form' => $form->createView(),
            'dropbox_thumb_prepared' => false
        ]);
    }
}
