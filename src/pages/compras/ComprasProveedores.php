<?php
session_start();
// Verifica si el usuario ha iniciado sesión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compras y proveedores | Conta Tech</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <script src="../../assets/js/script.js"></script>
    <link rel="icon" href="../../../assets/img/favicon_io/favicon.ico" type="image/x-icon">
</head>
<body>

  <div class="sidebar">
    <h2 class="app-title">Conta Tech</h2>
    <ul class="menu">
      <li><a href="../../../index.php">🏠 DashBoard</a></li>
      <li><a href="../facturacion/crearFacturacion.php">🧾 Facturación</a></li>
      <li><a href="../compras/ComprasProveedores.php" class="active">🛒 Compras y proveedores</a></li>
      <li><a href="../gastos_ingresos/gasto_ingreso.php">💸 Ingresos y Egresos</a></li>
      <li><a href="../reportes/ingresos.php">📊 Reportes contables</a></li>
      <li><a href="../inventario/listaProductos.php">📦 Inventario</a></li>
      <li><a href="../nomina/nomina.php">📋 Nómina</a></li>
      <li><a href="support.php">🧰 Soporte</a></li>
    </ul>
  </div>
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Compras</h1>
            </div>
            <button class="logout">Log Out</button>
        </div>
        <div class="top-bar">
            <div class="tabs">
                <button class="tab active"><a href="ComprasProveedores.php">Factura de Compra</a></button>
                <button class="tab"> <a href="gestionProveedores.php">Gestion de Proveedores</a></button>
                <button class="tab"><a href="ordenesCompra.php">Ordenes de Compra</a></button>
            </div>
            <div class="search-box">
                <span class="icon">🔍</span>
                <input type="text" placeholder="Buscar.....">
            </div>
        </div>
                <form method="post" action="procesar_factura.php">
            <table>
                <thead>
                    <tr>
                        <th>Cod</th>
                        <th>Nombre del producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Operaciones</th>
                    </tr>
                </thead>
                <tbody id="tablaProductos">
                    <?php
                    include("../../config/conexion.php");
                    $query = "SELECT * FROM compra_proveedores";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $precio = $row['precio'];
                            $cantidad = $row['cantidad'];
                            $total = $precio * $cantidad;
                            echo "<tr>";
                            echo "<td>{$row['cod_producto']}</td>";
                            echo "<td>{$row['nombre_producto']}</td>";
                            echo "<td><input type='number' class='precio' value='$precio' readonly></td>";
                            echo "<td><input type='number' class='cantidad' value='$cantidad' min='1'></td>";
                            echo "<td><input type='text' class='total' value='$total' readonly></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay datos disponibles</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <!-- Botones -->
            <div class="botones">
            <button type="submit">Guardar Factura</button>
            <button type="button" id="abrirDialogo">Agregar Producto</button>
            <button type="button" onclick="window.print()">Imprimir Factura</button>
            <button type="reset">Nueva Factura</button>
            </div>
        </form>

        <!-- Modal con <dialog> -->
        <dialog id="dialogoProducto">
            <form method="dialog" id="formProducto">
                <h2>Agregar Producto</h2>

                <label for="buscador">Buscar producto:</label>
                <input type="text" id="buscador" placeholder="Escribe el nombre del producto" autocomplete="off">

                <ul id="resultados" class="resultado-lista"></ul>

                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" min="1" value="1">

                <menu>
                    <button type="submit">Agregar</button>
                    <button type="button" id="cerrarDialogo">Cancelar</button>
                </menu>
            </form>
        </dialog>
        <script>
            const dialogo = document.getElementById('dialogoProducto');
            const abrirBtn = document.getElementById('abrirDialogo');
            const cerrarBtn = document.getElementById('cerrarDialogo');
            const buscador = document.getElementById('buscador');
            const resultados = document.getElementById('resultados');

            abrirBtn.addEventListener('click', () => {
                dialogo.showModal();
            });

            cerrarBtn.addEventListener('click', () => {
                dialogo.close();
            });

            buscador.addEventListener('input', () => {
                const query = buscador.value.trim();
                resultados.innerHTML = '';
                if (query.length === 0) return;

                fetch('../../api/buscar_productos.php?query=${encodeURIComponent(query)}')
                .then(response => response.json())
                .then(data => {
                    resultados.innerHTML = '';
                    data.forEach(producto => {
                    const li = document.createElement(' ');
                    li.textContent = '${producto.nombre_producto} (Código: ${producto.cod_producto})';
                    li.addEventListener('click', () => {
                        buscador.value = producto.nombre_producto;
                        resultados.innerHTML = '';
                    });
                    resultados.appendChild(li);
                    });
                });
            });
        </script>
    </div>
</body>
</html>
