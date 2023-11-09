<?php

include 'assets/componentes/conexion.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['cancel'])){

   $booking_id = $_POST['booking_id'];
   $booking_id = filter_var($booking_id, 513);

   $verify_booking = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
   $verify_booking->execute([$booking_id]);

   if($verify_booking->rowCount() > 0){
      $delete_booking = $conn->prepare("DELETE FROM `bookings` WHERE booking_id = ?");
      $delete_booking->execute([$booking_id]);
      $success_msg[] = '!Reserva cancelada con éxito!';
   }else{
      $warning_msg[] = 'La reserva ya fue cancelada';
   }
   
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Reservaciones</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

   <?php include 'assets/componentes/header.php'; ?>

   <!--=============== RESERVAR ===============--> 
   <section class="bookings">      
      <h1 class="heading">Mis Reservaciones</h1>

      <div class="box-container">
         <?php
            $select_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ?");
            $select_bookings->execute([$user_id]);
            if($select_bookings->rowCount() > 0){
               while($fetch_booking = $select_bookings->fetch(PDO::FETCH_ASSOC)){
         ?>         
         <div class="box">
            <p>Nombre : <span><?= $fetch_booking['name']; ?></span></p>
            <p>Apellido : <span><?= $fetch_booking['lastname']; ?></span></p>
            <p>Correo : <span><?= $fetch_booking['email']; ?></span></p>
            <p>Telefono : <span><?= $fetch_booking['number']; ?></span></p>
            <p>Check in : <span><?= $fetch_booking['check_in']; ?></span></p>
            <p>Check out : <span><?= $fetch_booking['check_out']; ?></span></p>
            <p>Tipo de Habitaciones : <span><?= $fetch_booking['type_rooms']; ?></span></p>
            <p>Habitaciones : <span><?= $fetch_booking['rooms']; ?></span></p>
            <p>Adultos : <span><?= $fetch_booking['adults']; ?></span></p>
            <p>Niños : <span><?= $fetch_booking['childs']; ?></span></p>
            <p>ID reservacion : <span><?= $fetch_booking['booking_id']; ?></span></p>
            <form action="" method="POST">
               <input type="hidden" name="booking_id" value="<?= $fetch_booking['booking_id']; ?>">
               <input type="submit" value="Cancelar reservacion" name="cancel" class="btn" onclick="return confirm('Cancelar esta reserva?');">
            </form>
         </div>

         <?php
         }
         }else{
         ?>   
         <div class="box" style="text-align: center;">
            <p style="padding-bottom: .5rem; text-transform:capitalize;">No hay reservaciones</p>
            <a href="index.php#reservar" class="btn">Reservar Ahora</a>
         </div>
         <?php
         }
         ?>
      </div>
   </section>
   <!--=============== RESERVAR ===============-->

   <?php include 'assets/componentes/footer.php'; ?>

   <!--=============== SCRIPT ===============-->
   <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>


   <script src="assets/js/script.js"></script>
   
   <?php include 'assets/componentes/mensaje.php'; ?>
</body>
</html>