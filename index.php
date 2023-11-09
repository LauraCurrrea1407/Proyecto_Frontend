<?php

include 'assets/componentes/conexion.php';

if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];
}else{
    setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
    header('location:index.php');
}

if(isset($_POST['check'])){
    $check_in = $_POST['check_in'];
    $check_in = filter_var($check_in, 513);

    $total_rooms = 0;

    $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
    $check_bookings->execute([$check_in]);

    while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
        $total_rooms += $fetch_bookings['rooms'];
    }
    // if the hotel has total 30 rooms 
    if($total_rooms >= 30){
        $warning_msg[] = 'Las habitaciones no están disponibles';
    }else{
        $success_msg[] = 'Las habitaciones están disponibles';
    }
}

if(isset($_POST['book'])){    

    $booking_id = create_unique_id();
    $name = $_POST['name'];
    $name = filter_var($name, 513);
    $lastname = $_POST['lastname'];
    $lastname = filter_var($lastname, 513);
    $email = $_POST['email'];
    $email = filter_var($email, 513);
    $number = $_POST['number'];
    $number = filter_var($number, 513);
    $type_rooms = $_POST['type_rooms'];
    $type_rooms = filter_var($type_rooms, 513);
    $rooms = $_POST['rooms'];
    $rooms = filter_var($rooms, 513);
    $check_in = $_POST['check_in'];
    $check_in = filter_var($check_in, 513);
    $check_out = $_POST['check_out'];
    $check_out = filter_var($check_out, 513);
    $adults = $_POST['adults'];
    $adults = filter_var($adults, 513);
    $childs = $_POST['childs'];
    $childs = filter_var($childs, 513);

    $total_rooms = 0;

    $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
    $check_bookings->execute([$check_in]);

    while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
        $total_rooms += $fetch_bookings['rooms'];
    }

    if($total_rooms >= 30){
        $warning_msg[] = 'Las habitaciones no están disponibles';
    }else{

        $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND lastname = ? AND email = ? AND number = ? AND type_rooms = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
        $verify_bookings->execute([$user_id, $name, $lastname, $email, $number, $type_rooms, $rooms, $check_in, $check_out, $adults, $childs]);

        if($verify_bookings->rowCount() > 0){
            $warning_msg[] = 'La habitación ya fue reservada';
        }else{
            $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, lastname, email, number, type_rooms, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
            $book_room->execute([$booking_id, $user_id, $name, $lastname, $email, $number, $type_rooms, $rooms, $check_in, $check_out, $adults, $childs]);
            $success_msg[] = '¡Habitación reservada con éxito!';
        }
    }
}

if(isset($_POST['send'])){

    $id = create_unique_id();
    $name = $_POST['name'];
    $name = filter_var($name, 513);
    $email = $_POST['email'];
    $email = filter_var($email, 513);
    $number = $_POST['number'];
    $number = filter_var($number, 513);
    $message = $_POST['message'];
    $message = filter_var($message, 513);

    $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
    $verify_message->execute([$name, $email, $number, $message]);

    if($verify_message->rowCount() > 0){
        $warning_msg[] = 'El mensaje ya fue enviado';
    }else{
        $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
        $insert_message->execute([$id, $name, $email, $number, $message]);
        $success_msg[] = '¡Mensaje enviado exitosamente!';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Luna Hotel - Reservación</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet"/>    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css"/>
</head>
<body>  

    <?php include 'assets/componentes/header.php'; ?>

    <!--=============== INICIO ===============-->      
    <section class="inicio">
        <div class="diapositivas">
            <img src="assets/img/inicio/habitacion.png" alt="imagen-1">
        </div>
        <div class="diapositivas">
            <img src="assets/img/inicio/restaurante.png" alt="">
        </div>
        <div class="diapositivas">
            <img src="assets/img/inicio/piscina.png" alt="">
        </div>                    
    </section>
    <!--=============== INICIO ===============-->            
    

    <!--=============== DISPONIBILIDAD ===============-->   
    <section class="disponibilidad" id="disponibilidad">                
        <form action="" method="post">
            <div class="flex">
                <div class="box">
                    <label class="form-label" style="font-weight: 500;">Check in</label>
                    <input type="date" name="check_in" class="form-control">
                </div>
                <div class="box">
                    <label class="form-label" style="font-weight: 500;">Check out</label>
                    <input type="date" name="check_out" class="form-control">
                </div>
                <div class="box">
                    <label class="form-label" style="font-weight: 500;">Adultos</label>
                    <select name="adults" class="form-control">
                        <option value="1">1 adulto</option>
                        <option value="2">2 adultos</option>
                        <option value="3">3 adultos</option>
                        <option value="4">4 adultos</option>
                        <option value="5">5 adultos</option>
                    </select>
                </div>
                <div class="box">
                    <label class="form-label" style="font-weight: 500;">Niños</label>
                    <select name="childs" class="form-control">
                        <option value="-">0 niños</option>
                        <option value="1">1 niño</option>
                        <option value="2">2 niños</option>
                        <option value="3">3 niños</option>
                        <option value="4">4 niños</option>
                        <option value="5">5 niños</option>
                    </select>
                </div>
                <div class="box">
                    <label class="form-label" style="font-weight: 500;">Habitaciones</label>
                    <select name="rooms" class="form-control">
                        <option value="1">1 habitación</option>
                        <option value="2">2 habitaciones</option>
                        <option value="3">3 habitaciones</option>
                        <option value="4">4 habitaciones</option>
                        <option value="5">5 habitaciones</option>
                    </select>
                </div>
            </div>
            <input type="submit" value="Consultar Disponibilidad" name="check" class="btn">
        </form>            
    </section>
    <!--=============== DISPONIBILIDAD ===============--> 
    

    <!--=============== NOSOTROS ===============-->  
    <section class="nosotros" id="nosotros">
        <h2 class="titulo">
            <span>N</span>
            <span>O</span>
            <span>S</span>
            <span>O</span>
            <span>T</span>
            <span>R</span>
            <span>O</span>
            <span>S</span>
        </h2>         
        <div class="content">        
            <p style="text-align: center">
                Adéntrate en un mundo donde la hospitalidad se entrelaza con el 
                compromiso inquebrantable de superar cada expectativa.
            </p>
        </div>
        <div class="row">     
            <div class="image">
                <img src="assets/img/nosotros/nosotros.png" alt="about"/>
            </div>
            <div class="content">
                <p>
                    En el corazón de nuestra hospitalidad late un compromiso inquebrantable de 
                    superar expectativas. Cada integrante de nuestro hotel se compromete a 
                    ofrecer un servicio de calidad, destacándose por su profesionalismo y 
                    dedicación. Nos enorgullece sumergirnos en la esencia de la excelencia, 
                    asegurando que cada huésped viva una experiencia única e inolvidable.
                </p>
            </div>
        </div>
        <div class="box-container">
            <div class="box">
                <img src="assets/img/nosotros/alimentos.png" alt="">
                <h3>Alimentos</h3>
            </div>
            <div class="box">
                <img src="assets/img/nosotros/piscina.png" alt="">
                <h3>Piscina</h3>
            </div>
            <div class="box">
                <img src="assets/img/nosotros/personal_medico.png" alt="">
                <h3>Personal Medico</h3>
            </div>
            <div class="box">
                <img src="assets/img/nosotros/limpieza.png" alt="">
                <h3>Limpieza</h3>
            </div>
            <div class="box">
                <img src="assets/img/nosotros/spa.png" alt="">
                <h3>Spa</h3>
            </div>
        </div>
    </section>
    <!--=============== NOSOTROS ===============--> 


    <!--=============== HABITACIONES ===============-->  
    <section class="habitaciones" id="habitaciones">
        <h2 class="titulo">
            <span>H</span>
            <span>A</span>
            <span>B</span>
            <span>I</span>
            <span>T</span>
            <span>A</span>
            <span>C</span>
            <span>I</span>
            <span>O</span>
            <span>N</span>
            <span>E</span>
            <span>S</span>
        </h2>
        <div class="habitaciones__grid">
            <div class="habitaciones__card">
                <img src="assets/img/habitaciones/suite_principal.png"/>
                <h3>Suit Principal</h3>
            </div>
            <div class="habitaciones__card">
                <img src="assets/img/habitaciones/habitacion_familiar.png"/>
                <h3>Habitacion Familiar</h3>
            </div>
            <div class="habitaciones__card">
                <img src="assets/img/habitaciones/habitacion_matrimonial.png"/>
                <h3>Habitacion Matrimonial</h3>
            </div>
        </div>
    </section>
    <!--=============== HABITACIONES ===============--> 


    <!--=============== RESERVAR ===============--> 
    <section class="reservacion" id="reservar">
        <h2 class="titulo">
            <span>R</span>
            <span>E</span>
            <span>S</span>
            <span>E</span>
            <span>R</span>
            <span>V</span>
            <span>A</span>
            <span>R</span>
        </h2>
        <div class="contenedor">
            <div class="formulario">
                <form action="" method="post">                    
                    <h3>Haz una reserva</h3>
                    <div class="flex">
                        <div class="box">
                            <label class="form-label" style="font-weight: 500;">Nombre</label>
                            <input type="text" name="name" maxlength="50" required placeholder="Ingrese su nombre" class="form-control">
                        </div>
                        <div class="box">
                            <label class="form-label" style="font-weight: 500;">Apellido</label>
                            <input type="text" name="lastname" maxlength="50" required placeholder="Ingrese su apellido" class="form-control">
                        </div>
                    </div>   
                    <div class="flex">
                        <div class="box">
                            <label class="form-label" style="font-weight: 500;">Correo Electrónico </label>
                            <input type="email" name="email" maxlength="50" required placeholder="Ingrese su correo electrónico" class="form-control">
                        </div>
                        <div class="box">
                            <label class="form-label" style="font-weight: 500;">Número telefónico</label>
                            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="Ingrese su número telefónico" class="form-control">
                        </div>
                    </div> 
                    <div class="flex">
                        <div class="box">
                            <label class="form-label" style="font-weight: 500;">Tipo de habitacion </label>
                            <select name="type_rooms" class="form-control" required>                        
                                <option value="0">Seleccione el tipo de habitacion</option>
                                <option value="Suit Principal">Suit Principal</option>
                                <option value="Habitacion Familiar">Habitacion Familiar</option>
                                <option value="Habitacion Matrimonial">Habitacion Matrimonial</option>
                            </select>
                        </div>
                        <div class="box">
                            <label class="form-label" style="font-weight: 500;">Habitaciones</label>
                            <select name="rooms" class="form-control" required>
                                <option value="1">1 habitacion</option>
                                <option value="2">2 habitaciones</option>
                                <option value="3">3 habitaciones</option>
                                <option value="4">4 habitaciones</option>
                                <option value="5">5 habitaciones</option>
                                <option value="6">6 habitaciones</option>
                            </select>
                        </div>
                    </div>  
                    <div class="flex">
                        <div class="box">
                            <label class="form-label" style="font-weight: 500;">Check in</label>
                            <input type="date" name="check_in" class="form-control" required>
                        </div>
                        <div class="box">
                            <label class="form-label" style="font-weight: 500;">Check out</label>
                            <input type="date" name="check_out" class="form-control" required>
                        </div>
                    </div>   
                    <div class="flex">
                        <div class="box">
                            <label class="form-label" style="font-weight: 500;">Adultos </label>
                            <select name="adults" class="form-control" required>
                                <option value="1">1 adulto</option>
                                <option value="2">2 adultos</option>
                                <option value="3">3 adultos</option>
                                <option value="4">4 adultos</option>
                                <option value="5">5 adultos</option>
                                <option value="6">6 adultos</option>
                            </select>
                        </div>
                        <div class="box">
                            <label class="form-label" style="font-weight: 500;">Niños</label>
                            <select name="childs" class="form-control" required>
                                <option value="0">0 Niño</option>
                                <option value="1">1 Niños</option>
                                <option value="2">2 Niños</option>
                                <option value="3">3 Niños</option>
                                <option value="4">4 Niños</option>
                                <option value="5">5 Niños</option>
                                <option value="6">6 Niños</option>
                            </select>
                        </div>
                    </div> 
                    <input type="submit" value="Hacer reservación " name="book" class="btn">
                </form>
            </div>            
            <div class="video">
                <video controls>
                    <source src="assets/video/1.mp4" type="video/mp4">
                </video>
            </div>
        </div>        
    </section>       
    <!--=============== RESERVAR ===============--> 


    <!--=============== GALERIA ===============--> 
    <section class="galeria" id="galeria">
        <h2 class="titulo">
            <span>G</span>
            <span>A</span>
            <span>L</span>
            <span>E</span>
            <span>R</span>
            <span>I</span>
            <span>A</span>
        </h2>                   
        <div>
            <img src="assets/img/galeria/galeria-1.png" alt="">
            <img src="assets/img/galeria/galeria-2.png" alt="">
            <img src="assets/img/galeria/galeria-3.png" alt="">
            <img src="assets/img/galeria/galeria-4.png" alt="">
            <img src="assets/img/galeria/galeria-5.png" alt="">
            <img src="assets/img/galeria/galeria-6.png" alt="">
        </div>                
    </section>
    <!--=============== GALERIA ===============-->
 

    <!--=============== CONTACTO ===============-->
    <section class="contacto" id="contacto">
        <h2 class="titulo">
            <span>C</span>
            <span>O</span>
            <span>N</span>
            <span>T</span>
            <span>A</span>
            <span>C</span>
            <span>T</span>
            <span>O</span>
        </h2>        
        <div class="contenedor">   
            <div class="formulario">                   
                <form action="" method="post">
                    <h3>Envianos un mensaje</h3>
                    <input type="text" name="name" required maxlength="50" placeholder="Ingresa tu nombre" class="form-control">
                    <input type="email" name="email" required maxlength="50" placeholder="Ingresa tu correo electronico" class="form-control">
                    <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="Ingresa tu numero" class="form-control">
                    <textarea name="message" class="form-control" required maxlength="1000" placeholder="Escribe tu mensaje" cols="30" rows="10"></textarea>
                    <input type="submit" value="Enviar mensaje" name="send" class="btn">
                </form>
            </div>   
            <div class="video">
                <video controls>
                    <source src="assets/video/2.mp4" type="video/mp4">
                </video>
            </div>         
        </div>    
    </section>
    <!--=============== CONTACTO ===============-->


    <!--=============== RESEÑA ===============-->
    <section class="reseña__contenedor" id="reseñas">            
        <h2 class="titulo">
            <span>R</span>
            <span>E</span>
            <span>S</span>
            <span>E</span>
            <span>Ñ</span>
            <span>A</span>
            <span>S</span>
        </h2>            
        <div class="cliente__grid">
            <div class="cliente__tarjeta">
                <div class="cliente__tarjeta__encabezado">
                    <span><i class="ri-double-quotes-r"></i></span>
                    <div class="ratings">
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-half-fill"></i></span>
                    </div>
                </div>
                <p>
                    Excelente servicio al cliente, personal amable y atento. Las 
                    instalaciones estaban impecables, me encanto.
                </p>
                <div class="cliente__tarjeta__footer">
                    <img src="assets/img/clientes/cliente-1.png" alt="client" />
                    <div class="cliente__detalles">
                        <h4>Alejandro Morales</h4>
                    </div>
                </div>
            </div>
            <div class="cliente__tarjeta">
                <div class="cliente__tarjeta__encabezado">
                    <span><i class="ri-double-quotes-r"></i></span>
                    <div class="ratings">
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                    </div>
                </div>
                <p>
                    Ambiente acogedor, perfecto para familias. El personal fue muy 
                    atento con los niños. Definitivamente volveríamos.
                </p>
                <div class="cliente__tarjeta__footer">
                    <img src="assets/img/clientes/cliente-2.png" alt="client"/>
                    <div class="cliente__detalles">
                        <h4>Valentina Herrera</h4>
                    </div>
                </div>
            </div>
            <div class="cliente__tarjeta">
                <div class="cliente__tarjeta__encabezado">
                    <span><i class="ri-double-quotes-r"></i></span>
                    <div class="ratings">
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-line"></i></span>
                    </div>
                </div>
                <p>
                    Ubicación estratégica, cerca de atracciones turísticas y 
                    restaurantes. El personal fue excepcional.
                </p>
                <div class="cliente__tarjeta__footer">
                    <img src="assets/img/clientes/cliente-3.png" alt="client" />
                    <div class="cliente__detalles">
                        <h4>Mateo Ramirez</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--=============== RESEÑA ===============-->

    <?php include 'assets/componentes/footer.php'; ?>       

    <!--=============== SCRIPT ===============-->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="assets/js/script.js"></script>
    <?php include 'assets/componentes/mensaje.php'; ?>

</body>
</html>