<?php
require 'config/database.php';
require 'config/config.php';
$db =  new Database();
$con = $db->conectar();


$sql = $con->prepare("SELECT id,titulo,precio FROM libros WHERE activo=1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

//session_destroy();

?>

<html lang="es" class="h-100"><head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta property="og:type" content="article">
	<meta property="og:title" content="Tienda Online de Libros">

	<!-- Google tag (gtag.js) -->
    <script async="" src="https://www.googletagmanager.com/gtag/js?id=G-4G5KB2ZVC2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-4G5KB2ZVC2');
    </script>

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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBarTop" aria-controls="navBarTop" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navBarTop">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Catalogo</a>
                    </li>
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
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        
            <?php foreach($resultado as $row) {?>
            <!-- Carta del producto -->    
            <div class="col mb-2">
                <!-- Imagen -->
                <div class="card shadow-sm h-100">
                        <?php
                        $id = $row['id'];
                        $image = "images/libros/".$id."/principal.jpg";
                        if(!file_exists($image)){
                            $image = "images/no-photo.jpg";
                        }
                        ?>

                    <a a href="details.php?id=<?php echo $row['id']; ?>&token=<?php echo hash_hmac('sha1',$row['id'], KEY_TOKEN); ?>">
                        <img src="<?php echo $image?>" class="card-img-top" class="d-block w-100">
                    </a>
                    <!-- Titulo y precio -->
                    <div class="card-body">
                        <p class="card-title"><?php echo $row['titulo'];?></p>
                        <p class="card-text"><strong><?php echo number_format($row['precio'],2,'.',',');?></strong></p>
                    </div>
                    <!-- Detalles y botones -->
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="details.php?id=<?php echo $row['id']; ?>&token=<?php echo 
                                hash_hmac('sha1',$row['id'], KEY_TOKEN); ?>" class="btn btn-primary">Detalles</a>
                            </div>
                            <button class="btn btn-success" type="button" onclick="addProduct(
                                <?php echo $row['id']; ?>, '<?php echo hash_hmac('sha1',$row['id'], KEY_TOKEN); ?>')">Agregar al carrito</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php  } ?>

        </div>
    </div>
</main>

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