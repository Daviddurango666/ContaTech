function cards() {
    var amount = getElementByClass("amount").value;
    let positive = getElementByClass("trend positive").value;
    let negative = getElementByClass("negative").value; 
    if(amount < 0) {
        positive.style.color = "red";
    }
}

// ventana modal
function modal() {

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
}