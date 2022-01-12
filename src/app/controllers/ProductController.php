<?php
namespace ProyectoWeb\app\controllers;

use Psr\Container\ContainerInterface;
use ProyectoWeb\entity\Product;
use ProyectoWeb\exceptions\QueryException;
use ProyectoWeb\exceptions\NotFoundException;
use ProyectoWeb\database\Connection;
use ProyectoWeb\repository\CategoryRepository;
use ProyectoWeb\repository\ProductRepository;

class ProductController
{
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function ficha($request, $response, $args) {
        extract($args);
        $repositoryCateg = new CategoryRepository();
        $categorias = $repositoryCateg->findAll();
        $repositorio = new ProductRepository();
        try{
            $producto =$repositorio->findById($id);
        }catch(NotFoundException $nfe){
            $response = new \Slim\Http\Response(404);
            return $response->write("Producto no encontrado");
        }
        $title = $producto->getNombre();
        $relacionados = $repositorio->getRelacionados($producto->getID(), $producto->getIDCategoria());
        return $this->container->renderer->render($response, "product.view.php", compact('title', 'categorias', 'producto', 'relacionados'));
    }
}