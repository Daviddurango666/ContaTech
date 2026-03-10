<?php
session_start();
include("src/config/conexion.php");
// Verifica si el usuario ha iniciado sesión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DashBoard | Conta Tech</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/script.js"></script>
    <link rel="icon" href="assets/img/favicon_io/favicon.ico" type="image/x-icon">
</head>
<body>

  <div class="sidebar">
    <h2 class="app-title">Conta Tech</h2>
    <ul class="menu">
      <li><a href="index.php" class="active">🏠 DashBoard</a></li>
      <li><a href="src/pages/facturacion/crearFacturacion.php">🧾 Facturación</a></li>
      <li><a href="src/pages/compras/ComprasProveedores.php">🛒 Compras y proveedores</a></li>
      <li><a href="src/pages/gastos_ingresos/gasto_ingreso.php">💸 Ingresos y Egresos</a></li>
      <li><a href="src/pages/reportes/ingresos.php">📊 Reportes contables</a></li>
      <li><a href="src/pages/inventario/listaProductos.php">📦 Inventario</a></li>
      <li><a href="src/pages/nomina/nomina.php">📋 Nómina</a></li>
      <li><a href="src/pages/soporte/support.php">🧰 Soporte</a></li>
      <li><button onclick="document.body.classList.toggle('dark')"><img src="assets/img/menu/moon.png" alt=""></button>
  </div>
    <div class="main-content">
      <div class="header">
        <div>
          <h1>DashBoard</h1>
          <p>Resumen</p>
        </div>
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <!-- <p class="user"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p> -->
            <button class="logout"><a href="src/auth/logout.php">Log Out</a></button>
        <?php else: ?>
            <button class="logout"><a href="src/auth/login.php">Log In</a></button>
        <?php endif; ?>
      </div>

    <div class="cards">
      <div class="card">
        <h3>Reporte de Gastos</h3>
        <p class="amount">$
          <?php
            $statement = $conn->prepare("SELECT SUM(monto) AS total_gastos FROM gastos");
            $statement->execute();
            $result = $statement->get_result();
            if($result -> num_rows > 0){
              $gastos = $result->fetch_assoc();
              echo number_format($gastos['total_gastos'], 2);
            } else {
              echo "0.00";
            }
          ?>
        </p>
        <span class="trend positive">+20% month over month</span>
      </div>
      <div class="card">
        <h3>Reporte de Ingresos</h3>
        <p class="amount">$
          <?php
            $statement = $conn->prepare("SELECT SUM(monto) AS total_ingresos FROM ingresos");
            $statement->execute();
            $result = $statement->get_result();
            if($result -> num_rows > 0 ) {
              $gastos = $result->fetch_assoc();
              echo number_format($gastos['total_ingresos'], 2);
            } else {
              echo "0.00";
            }
          ?>
        </p>
        <span class="trend positive">+33% month over month</span>
      </div>
      <div class="card">
        <h3>Alertas</h3>
        <p class="amount">$
            <?php
            // Obtener el total de ingresos
            $ingresos = $conn->prepare("SELECT SUM(monto) AS total_ingresos FROM ingresos");
            $ingresos->execute();
            $resultado_ingresos = $ingresos->get_result();
            $total_ingresos = $resultado_ingresos->fetch_assoc()['total_ingresos'];
            // Obtener el total de gastos
            $gastos = $conn->prepare("SELECT SUM(monto) AS total_gastos FROM gastos");
            $gastos->execute();
            $resultado_gastos = $gastos->get_result();
            $total_gastos = $resultado_gastos->fetch_assoc()['total_gastos'];
            // Comparar gastos e ingresos
            if ($total_gastos > $total_ingresos) {
                $gastos_excedidos = $total_gastos - $total_ingresos;
                echo number_format($gastos_excedidos, 2) . "<br><span class='trend negative'>Alertas: Gastos exceden los ingresos</span>";
            } else {
                echo "0,00<br><span class='trend positive'>No hay alertas de gastos excedidos</span>";
            }
            ?>

        </p>
        <!-- <span class="trend negative">-8% month over month</span> -->
      </div>
    </div>

    <div class="dashboard-grid">
      <div class="chart">
        <h3>Gráfica de Gastos</h3>
        <canvas id="graficaGastos"></canvas>
        
      </div>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            fetch('src/api/api_gastos.php')
              .then(response=>response.json())
              .then(data=> {
                const categorias = data.map(item=>item.categoria);
                const montos = data.map(item=>item.total);
  
                const ctx =
                document.getElementById('graficaGastos').getContext('2d');
                new Chart(ctx, {
                  type: 'bar',
                  data: {
                    labels:categorias,
                    datasets: [{
                      label: 'Total Gastado',
                      data: montos,
                      backgroundColor:['#ff0000'],
                      borderColor:['#ff0000'],  
                    }]
                  }, 
                  options: {
                    responsive: true,
                    scales:{
                      y:{
                        beginAtZero: true
                      }
                    }
                  }
                })
              })
              .catch(error=>console.error('error fetching data', error));
          });
        </script>
      <div class="chart">
        <h3>Grafica de Ingresos</h3>
        <canvas id="graficaIngresos"></canvas>
      </div>
			<script>
			document.addEventListener('DOMContentLoaded', function() {
				fetch('src/api/api_ingresos.php')
				.then(response=>response.json())
				.then(data=> {
					const categorias = data.map(item=>item.categoria);
					const montos = data.map(item=>item.total);
	
					const ctx =
					document.getElementById('graficaIngresos').getContext('2d');
					new Chart(ctx, {
					type: 'bar',
					data: {
						labels:categorias,
						datasets: [{
						label: 'Total Ingresos',
						data: montos,
						backgroundColor:['#1db954'],
						borderColor:['#1db954'],  
						}]
					}, 
					options: {
						responsive: true,
						scales:{
						y:{
							beginAtZero: true
						}
						}
					}
					})
				})
				.catch(error=>console.error('error fetching data', error));
			});
      </script>
    </div>
  </div>

</body>
</html>
