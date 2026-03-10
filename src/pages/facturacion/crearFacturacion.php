<?php
session_start();
// Verifica si el usuario ha iniciado sesión
include("../../config/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturacion | Conta Tech</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <script src="../../../assets/js/script.js"></script>
    <link rel="icon" href="../../../assets/img/favicon_io/favicon.ico" type="image/x-icon">
</head>
<body>

  <div class="sidebar">
    <h2 class="app-title">Conta Tech</h2>
    <ul class="menu">
      <li><a href="../../../index.php">🏠 DashBoard</a></li>
      <li><a href="../facturacion/crearFacturacion.php"  class="active">🧾 Facturación</a></li>
      <li><a href="../compras/ComprasProveedores.php">🛒 Compras y proveedores</a></li>
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
                <h1>Facturacion</h1>
            </div>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <!-- <p class="user"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p> -->
                <button class="logout"><a href="../../auth/logout.php">Log Out</a></button>
            <?php else: ?>
                <button class="logout"><a href="../../auth/login.php">Log In</a></button>
            <?php endif; ?>
        </div>
        <div class="top-bar">
            <div class="tabs">
                <button class="tab active""><a href="crearFacturacion.php">Crear Facturas</a></button>
                <button class="tab"><a href="verFacturas.php">Factura</a></button>
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
                    <!-- <?php
                    include("../../config/conexion.php");
                    $query = "SELECT * FROM productos WHERE cantidad > 0";
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
                        echo "<tr><td colspan='5'>No hay datos disponibles</td></tr>";
                    }
                    ?> -->
                </tbody>
            </table>
            <!-- Botones -->
            <div class="botones">
                <button type="submit">Guardar Factura</button>
                <button type="button" id="abrirDialogo">Agregar Producto</button>
                <button type="button" onclick="window.print()">Imprimir Factura</button>
                <button type="button">Enviar Factura</button>
                <button type="reset">Nueva Factura</button>
            </div>
        </form>

        <!-- Modal con <dialog> -->
        <dialog id="dialogoProducto">
            <form id="formProducto">
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
        <!-- script para ventana modal y lista de productos -->
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

                fetch(`../../api/buscar_productos.php?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultados.innerHTML = '';
                    data.forEach(producto => {
                    const li = document.createElement('li');
                    li.textContent = `${producto.nombre_producto} (Código: ${producto.cod_producto})`;
                    li.addEventListener('click', () => {
                        buscador.value = producto.nombre_producto;
                        resultados.innerHTML = '';
                    });
                    resultados.appendChild(li);
                    });
                });
            });
        </script>
        <!-- script para cargar el producto en la tabla -->
        <script>
            let productoSeleccionado = null;

            // Actualizar productoSeleccionado al hacer clic en un producto
            resultados.addEventListener('click', function (e) {
                if (e.target.tagName === 'LI') {
                    const texto = e.target.textContent;
                    const cod = texto.match(/\(Código: (.+?)\)/)[1];
                    const nombre = texto.split(' (')[0];

                    productoSeleccionado = { cod_producto: cod, nombre_producto: nombre };
                    buscador.value = nombre;
                    resultados.innerHTML = '';
                }
            });

            // Agregar producto a la tabla cuando se envía el formulario
            document.getElementById('formProducto').addEventListener('submit', function (e) {
                e.preventDefault();

                const cantidad = parseInt(document.getElementById('cantidad').value);
                if (!productoSeleccionado || isNaN(cantidad) || cantidad < 1) {
                    alert('Selecciona un producto y una cantidad válida.');
                    return;
                }

                fetch(`../../api/obtener_producto.php?codigo=${productoSeleccionado.cod_producto}`)
                .then(res => res.json())
                .then(data => {
                    const precio = parseFloat(data.precio);
                    const stock = parseInt(data.cantidad);

                    if (cantidad > stock) {
                        alert(`No hay suficiente stock. Stock disponible: ${stock}`);
                        return;
                    }

                    // Restar el stock en la base de datos
                    fetch('../../api/actualizar_stock.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `codigo=${encodeURIComponent(productoSeleccionado.cod_producto)}&cantidad=${cantidad}`
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.status === 'ok') {
                            const total = (precio * cantidad).toFixed(2);
                            const fila = document.createElement('tr');
                            fila.innerHTML = `
                                <td><input type="hidden" name="productos[]" value="${productoSeleccionado.cod_producto}">${productoSeleccionado.cod_producto}</td>
                                <td>${productoSeleccionado.nombre_producto}</td>
                                <td><input type="number" name="precios[]" value="${precio}" readonly></td>
                                <td><input type="number" name="cantidades[]" value="${cantidad}" readonly></td>
                                <td>${total}</td>
                                <td><div class="botonesTabla"><button type="button" onclick="this.closest('tr').remove()">Eliminar</button></div></td>
                            `;
                            document.getElementById('tablaProductos').appendChild(fila);

                            // Limpiar y cerrar
                            productoSeleccionado = null;
                            buscador.value = '';
                            document.getElementById('cantidad').value = 1;
                            dialogo.close();
                        } else {
                            alert(resp.msg || 'Error al actualizar stock');
                        }
                    });
                });
            });

        </script>

    </div>

</body>
</html>
