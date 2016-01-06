<?php
namespace AlbumRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Album\Model\Album; // <-- Add this import
use Album\Form\AlbumForm; // <-- Add this import
use Zend\View\Model\JsonModel;

class AlbumRestController extends AbstractRestfulController
{

    protected $albumTable;

    protected $em;

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }

    public function getList()
    {
        $results = $this->getEntityManager()->getRepository('Album\Entity\Album')->findAll();
        $data = array();

        
        return new JsonModel(array(
            'data' => $results
        ));
    }

    public function get($id)
    {
        $album = $this->getEntityManager()->find('Album\Entity\Album', $id);
        
        return new JsonModel(array(
            'data' => $album
        ));
    }

    public function create($data)
    {
        /* $form = new AlbumForm();
        $album = new Album();
        $form->setInputFilter($album->getInputFilter());
        $form->setData($data);
        if ($form->isValid()) {
            $album->exchangeArray($form->getData());
            $id = $this->getAlbumTable()->saveAlbum($album);
        }
        
        return $this->get($id); */
        $album = new \Album\Entity\Album();
        $album->setArtist($data[artist]);
        $album->setTitle($data[title]);
        $this->getEntityManager()->persist($album);
        $this->getEntityManager()->flush();
        return new JsonModel(array(
            'data' => $this->get($this->getEntityManager()->getConnection()->lastInsertId()),
        ));
    }

    public function update($id, $data)
    {
        $album = $this->getEntityManager()->find('Album\Entity\Album', $id);
        $album->setArtist($data[artist]);
        $album->setTitle($data[title]);
        $this->getEntityManager()->persist($album);
        $this->getEntityManager()->flush();
        return new JsonModel(array(
            'data' =>$this->get($album->getId())
        ));
    }

    public function delete($id)
    {
        $this->getAlbumTable()->deleteAlbum($id);
        
        return new JsonModel(array(
            'data' => 'deleted'
        ));
    }

    public function getAlbumTable()
    {
        if (! $this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
}
