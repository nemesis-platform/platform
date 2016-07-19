<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 10.10.2014
 * Time: 16:26
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Core\CMS\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/ckeditor")
 */
class CKEditorController extends Controller
{

    /**
     * @Route("/upload",name="ckeditor_upload")
     * @Method({"POST"})
     * @Template()
     * @param Request $request
     *
     * @return Response
     */
    public function uploadAction(Request $request)
    {


        /** @var FileBag $files */
        $files = $request->files;


        if ($files->count() === 1) {
            /** @var UploadedFile $file */
            $file = $files->get('upload');

            $hname = sha1(uniqid('uploads_', true)).'.'.$file->getClientOriginalExtension();


            $document = new File();
            $document->setStorageId($hname);
            $document->setMime($file->getMimeType());
            $document->setFilename($file->getClientOriginalName());
            $file->move(
                $this->get('service_container')->getParameter('kernel.root_dir').'/../web/'.$this->get(
                    'service_container'
                )->getParameter('ckeditor_uploads'),
                $hname
            );
            $this->getDoctrine()->getManager()->persist($document);
            $this->getDoctrine()->getManager()->flush();

            return [
                'callback' => $request->get('CKEditorFuncNum'),
                'url'      => $this->get('service_container')->getParameter('ckeditor_uploads').$document->getStorageId(
                    ),
                'message'  => 'Успешная загрузка',
            ];
        }

        return new Response('', 400);
    }

    /**
     * @Route("/browse",name="ckeditor_browse")
     * @Method("GET")
     * @Template()
     */
    public function browseAction()
    {
        $documents = $this->getDoctrine()->getRepository(File::class)->findAll();

        return ['documents' => $documents];
    }
}
