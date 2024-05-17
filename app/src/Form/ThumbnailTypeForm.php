<?php

namespace MateuszJagielskiRekrutacjaSmartiveapp\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use MateuszJagielskiRekrutacjaSmartiveapp\Entity\Thumbnail;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use MateuszJagielskiRekrutacjaSmartiveapp\Service\ThumbnailGeneratorService;
use MateuszJagielskiRekrutacjaSmartiveapp\Validator\DropboxToken;

class ThumbnailTypeForm extends AbstractType
{
    private const THUMBNAILS_DIRECTORY = '/private/thumbnails/';

    private KernelInterface $kernel;
    private ThumbnailGeneratorService $thumbnailGeneratorSerivce;
    private EntityManagerInterface $entityManager;

    public function __construct(
        KernelInterface $kernel,
        ThumbnailGeneratorService $thumbnail_generator_service,
        EntityManagerInterface $entity_manager
    ) {
        $this->kernel = $kernel;
        $this->thumbnailGeneratorSerivce = $thumbnail_generator_service;
        $this->entityManager = $entity_manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(null, null, 255)
                ]
            ])
            ->add('destination', ChoiceType::class, [
                'choices' => [
                    'Save locally' => 0,
                    'Upload to Dropbox' => 1
                ],
            ])
            ->add('dropboxToken', TextType::class, [
                'label' => 'Dropbox token',
                'required' => false,
                'constraints' => [
                    new DropboxToken(),
                ]
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'label' => 'Upload Image',
                'constraints' => [
                    new NotBlank(),
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG or PNG image',
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'The file is too large. Maximum size allowed is 2MB.',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class);
    }

    public function submitForm(string $filename, int $destination, UploadedFile $image, string $dropbox_token): Thumbnail
    {
        $upload_directory = $this->kernel->getProjectDir() . self::THUMBNAILS_DIRECTORY;
        $new_filename =  $filename . '_' . uniqid() . '.' . $image->guessExtension();

        try {
            $saved_file = $image->move($upload_directory, $new_filename);
        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        }


        $thumbnail_name = $this->thumbnailGeneratorSerivce->generate($saved_file->getPath(), $saved_file->getFilename());
        $thumbnail = new Thumbnail;
        $thumbnail
            ->setName($filename)
            ->setDestination($destination)
            ->setImagePath($thumbnail_name)
            ->setSendStatus(1)
            ->setDropboxToken($dropbox_token);

        if ($destination === 1) {
            $thumbnail->setSendStatus(0);
        }

        $this->entityManager->persist($thumbnail);
        $this->entityManager->flush();

        return $thumbnail;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Thumbnail::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'thumbnail_item'
        ]);
    }
}
