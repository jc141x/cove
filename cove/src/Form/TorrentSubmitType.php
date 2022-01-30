<?php

namespace App\Form;

use App\Entity\Torrent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TorrentSubmitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('torrent-file', FileType::class, [
                'mapped' => false,
                'label' => 'Torrent file',
                'required' => true,
                'attr' => [
                    'class' => 'custom-file-input',
                    'accept' => 'application/x-bittorrent',
                ],
            ])
            ->add('category', null, [
                'choice_label' => 'name',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Torrent::class,
        ]);
    }
}
