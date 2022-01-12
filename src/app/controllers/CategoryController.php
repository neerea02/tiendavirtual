<?php
namespace ProyectoWeb\app\controllers;

use JasonGrimes\Paginator;
use Psr\Container\ContainerInterface;
use ProyectoWeb\entity\Product;
use ProyectoWeb\exceptions\QueryException;
use ProyectoWeb\exceptions\NotFoundException;
use ProyectoWeb\database\Connection;
use ProyectoWeb\repository\CategoryRepository;
use ProyectoWeb\repository\ProductRepository;

class CategoryController
{
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    public function listado($request, $response, $args){
        extract($args);

        $repositorio = new CategoryRepository();
        try {
            $categoriaActual = $repositorio->findById($id);
        }catch(NotFoundException $nfe){
            return $response->write("Categoria no encontrada");
        }
        $title = $categoriaActual->getNombre();

        $repositorioProductos = new ProductRepository();

        $currentPage = ($currentPage ?? 1);
        $totalItems = $repositorioProductos->getCountByCategory($categoriaActual->getId());
        $itemsPerPage = App::get('config')['itemsPerPage'];
        $urlPattern = $this->container->router->pathFor('categoria',
                    ['nombre' => \ProyectoWeb\app\utils\Utils::encodeURI($categoriaActual->getNombre()),
                    'id' => $categoriaActual->getId()]) . '/page/(:num)';
        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
        $productos = $repositorioProductos->getByCategory($categoriaActual->getId(), $itemsPerPage, $currentPage);

        $categorias = $repositorio->findAll();

        return $this->container->renderer->render($response, "categoria.view.php", compact('title', 'categorias', 'categoriaActual', 'productos', 'paginator'));

    }
}