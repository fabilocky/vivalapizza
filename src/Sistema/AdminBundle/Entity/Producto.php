<?php

namespace Sistema\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sistema\AdminBundle\Entity\Producto
 *
 * @ORM\Table(name="producto")
 * @ORM\Entity(repositoryClass="Sistema\AdminBundle\Repository\ProductoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Producto
{
    /**
     * @Assert\Image(maxSize = "500k")
     */
    public $file;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=200, nullable=false)
     */
    private $nombre;
    
    /**
     * @var string $imagen
     *
     * @ORM\Column(name="imagen", type="string", length=255, nullable=true)
     */
    protected $imagen;
    
    /**
     * @var string $cantidad_vendido
     *
     * @ORM\Column(name="cantidad_vendido", type="string", length=200, nullable=true)
     */
    private $cantidad_vendido;
    
    /**
     * @var datetime $created
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;
    
    /**
     * @ORM\OneToMany(targetEntity="TipoProducto", mappedBy="idProducto", cascade={"persist", "remove"})
     */
    private $idTipoProducto;
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idTipoProducto = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString() {
        return $this->nombre;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Producto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }
    
    /**
     * Set imagen
     *
     * @param string $imagen
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }

    /**
     * Get imagen
     *
     * @return string 
     */
    public function getImagen()
    {
        return $this->imagen;
    }
    
    /**
     * Set cantidad_vendido
     *
     * @param string $cantidad_vendido
     * @return Producto
     */
    public function setCantidadVendido($cantidad_vendido)
    {
        $this->cantidad_vendido = $cantidad_vendido;
    
        return $this;
    }

    /**
     * Get cantidad_vendido
     *
     * @return string 
     */
    public function getCantidadVendido()
    {
        return $this->cantidad_vendido;
    }
    
    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime 
     */
    public function getCreated()
    {
        return $this->created;
    }
    
    public function getAbsolutePath()
    {
        return null === $this->imagen ? null : $this->getUploadRootDir().'/'.$this->imagen;
    }

    public function getWebPath()
    {
        return null === $this->imagen ? null : $this->getUploadDir().'/'.$this->imagen;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory imagen where uploaded documents should be saved
        return __DIR__.'/../../../../'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'web/uploads/producto/';
    }
    
    /**
     * @ORM\PostLoad
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpload()
    {
        $this->created =  new \DateTime();
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // set the imagen property to the filename where you'ved saved the file
        if (null === $this->imagen) {
            $this->setImagen(uniqid().'.'.$this->file->guessExtension());
        }else{
            $this->removeUpload();
//            $extension = pathinfo($this->imagen, PATHINFO_EXTENSION);
//            $nombre_base = basename($this->imagen, '.'.$extension);
            $this->setImagen(uniqid().'.'.$this->file->guessExtension());
        }
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */    
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        // move takes the target directory and then the target filename to move to
        $this->file->move($this->getUploadRootDir(), $this->imagen);

        unset($this->file);
    }
    
    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }
    
    /**
     * Add idTipoProducto
     *
     * @param Sistema\AdminBundle\Entity\TipoProducto $idTipoProducto
     * @return Producto
     */
    public function addIdTipoProducto(\Sistema\AdminBundle\Entity\TipoProducto $idTipoProducto)
    {
        $this->idTipoProducto[] = $idTipoProducto;
    
        return $this;
    }

    /**
     * Remove idTipoProducto
     *
     * @param Sistema\AdminBundle\Entity\TipoProducto $idTipoProducto
     */
    public function removeIdTipoProducto(\Sistema\AdminBundle\Entity\TipoProducto $idTipoProducto)
    {
        $this->idTipoProducto->removeElement($idTipoProducto);
    }

    /**
     * Get idTipoProducto
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getIdTipoProducto()
    {
        return $this->idTipoProducto;
    }
}