<?php
require 'config/database.php';
require 'config/config.php';
$db =  new Database();
$con = $db->conectar();

$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if($id == '' || $token == ''){
    echo 'Error al procesar la petición';
    exit;
} else{
    $token_tmp =  hash_hmac('sha1',$id, KEY_TOKEN);

    if($token == $token_tmp){
        $sql = $con->prepare("SELECT count(id) FROM libros WHERE id=? AND activo=1");
        $sql->execute([$id]);
        if($sql->fetchColumn() > 0){

            $sql = $con->prepare("SELECT titulo,descripción,precio FROM libros WHERE id=? AND activo=1 LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $titulo = $row['titulo'];
            $descripcion = $row['descripción'];
            $precio = $row['precio'];
            $dir_images = 'images/libros/'.$id.'/';

            $rutaImg = $dir_images . 'principal.jpg';

            if(!file_exists($rutaImg)){
                $rutaImg = 'images/no-photo.jpg';
            }

            $imagenes = array();
            if(file_exists($dir_images)){
            $dir = dir($dir_images);

            while( ($archivo = $dir->read() ) != false){
                if($archivo != 'principal.jpg' && (strpos($archivo,'jpg') || strpos($archivo,'jpeg' ) )){
                    $imagenes[] = $dir_images . $archivo; 
                }
            }
            $dir->close();
        }
        }
    }else{
        echo 'Error al procesar la petición';
        exit;
    }
}



?>

<html lang="es" class="h-100"><head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda en linea de libros</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">

 <!-- Menu de navegación -->
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Tienda online de libros</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBarTop" aria-controls="navBarTop" aria-expanded="false" control-id="ControlID-1">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navBarTop">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Catalogo</a>
                    </li>
                    <li class="nav-item">
                </ul>
                
                <a href="checkout.php" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-shopping-cart"></i> Carrito <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart; ?></span>
                </a>

                <a href="login.php" class="btn btn-success btn-sm"><i class="fas fa-user"></i> Ingresar</a> 
            </div>
        </div>
    </nav>

</header>
    <!-- Contenido -->
    <main class="flex-shrink-0">
        <div class="container">

            <div class="row">
                <div class="col-md-6 order-md-1">
                    <!--Foto-->
                    <div id="image" class="image">
                        <div class="carousel-inner">
                            <!--Imagenes-->
                            <div class="image-item active">
                                <img src="<?php echo $rutaImg; ?>" class="d-block w-100">
                            </div>
                            <!--Imagenes-->
                        </div>

                </div>

                <div class="col-md-7 order-md-2">
                    <h2><?php echo $titulo; ?></h2>
                    <input type="hidden" name="token" value="026a8089ff9ce1c296464138aad4080fe96bd25e">
                        <h2><?php echo MONEDA . number_format($precio,2,'.',','); ?></h2>
                    <p class="lead"> <?php echo $descripcion?></p>
                    <div class="col-3 my-3">
                        <input class="form-control" id="cantidad" name="cantidad" type="number" min="1" max="10" value="1" control-id="ControlID-4">
                    </div>

                    <div class="d-grid gap-3 col-10 mx-auto">
                        <button class="btn btn-primary" type="button" control-id="ControlID-5">Comprar ahora</button>
                        <button class="btn btn-outline-primary" type="button" onclick="addProduct(<?php echo $id; ?>, '<?php echo $token_tmp; ?>')">Agregar al carrito</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<footer class="footer mt-auto py-2 bg-dark">
    <div class="container">
        <p class="text-center">
            <span class="text-white">Tienda de libros 2023</span>
        </p>
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<script>
    function addProduct(id, token) {
        let url = 'clases/carrito.php'
        let formData = new FormData();
        formData.append('id', id);
        formData.append('token', token);

        fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json())
            .then(data => {
                if (data.ok) {
                    let elemento = document.getElementById("num_cart")
                    elemento.innerHTML = data.numero;
                }
            })
    }
</script>

</body>

</html>