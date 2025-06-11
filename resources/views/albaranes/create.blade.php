@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Crear Nuevo Albarán</h2>
        <a href="{{ route('albaranes.index') }}" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <form action="{{ route('albaranes.store') }}" method="POST" id="formAlbaran">
        @csrf

        {{-- Datos del Cliente y Albarán --}}
        <div class="card mb-4">
            <div class="card-header">Datos Generales del Albarán</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cliente_id" class="form-label">Clínica (Cliente):</label>
                        <select name="cliente_id" id="cliente_id" class="form-select" required>
                            <option value="">Seleccione una clínica</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre_clinica }} ({{ $cliente->nif }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha_envio" class="form-label">Fecha de Envío:</label>
                        <input type="date" name="fecha_envio" class="form-control" required value="{{ old('fecha_envio', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nombre_paciente" class="form-label">Nombre del Paciente:</label>
                        <input type="text" name="nombre_paciente" class="form-control" required value="{{ old('nombre_paciente') }}">
                    </div>
                </div>
                {{-- Datos del Emisor (S.M. Dental) - Opcional mostrar, pero fijo en la lógica --}}
                <div class="mt-3 p-3 bg-light border rounded">
                    <h6>Datos del Emisor:</h6>
                    <p><strong>Nombre:</strong> {{ $emisor['nombre'] }}</p>
                    <p><strong>Dirección:</strong> {{ $emisor['direccion'] }}</p>
                    <p><strong>DNI:</strong> {{ $emisor['dni'] }}</p>
                    <p><strong>Teléfono:</strong> {{ $emisor['telefono'] }}</p>
                </div>
            </div>
        </div>

        {{-- Detalle de Productos --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                Detalle de Productos
                <button type="button" class="btn btn-success btn-sm" id="add-product-row">Añadir Producto</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="product-details-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th width="120px">Unidades</th>
                            <th width="150px">Precio Unitario (€)</th>
                            <th width="150px">Importe (€)</th>
                            <th width="80px">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Las filas de productos se añadirán aquí con JavaScript --}}
                        {{-- Una fila inicial siempre presente para el primer producto --}}
                        <tr class="product-row">
                            <td>
                                <select name="productos[0][producto_id]" class="form-select product-select" required>
                                    <option value="">Seleccione un producto</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}" data-price="{{ $producto->precio }}">
                                            {{ $producto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="productos[0][unidades]" class="form-control units-input" min="1" value="1" required></td>
                            <td><input type="text" name="productos[0][precio_unitario]" class="form-control price-input" readonly></td>
                            <td><input type="text" name="productos[0][importe]" class="form-control importe-input" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-product-row">X</button></td>
                        </tr>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <div class="mb-3">
                            <label for="total_productos_display" class="form-label">Total Suma de Productos:</label>
                            <input type="text" id="total_productos_display" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="descuento" class="form-label">Descuento (€):</label>
                            <input type="number" step="0.01" name="descuento" id="descuento" class="form-control" value="{{ old('descuento', 0.00) }}" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="total_albaran_display" class="form-label">Total del Albarán:</label>
                            <input type="text" id="total_albaran_display" class="form-control font-weight-bold" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Guardar Albarán</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addProductBtn = document.getElementById('add-product-row');
            const productDetailsTable = document.getElementById('product-details-table').getElementsByTagName('tbody')[0];
            let rowCount = productDetailsTable.rows.length; // Para el index del array de productos

            // Función para formatear un número a 2 decimales con coma para la UI
            function formatToEuro(value) {
                return parseFloat(value).toFixed(2).replace('.', ',');
            }

            // Función para parsear un número con coma a float con punto para cálculos
            function parseEuroToFloat(value) {
                return parseFloat(value.replace(',', '.')) || 0;
            }

            // Función para calcular totales
            function calculateTotals() {
                let totalSumaProductos = 0;
                productDetailsTable.querySelectorAll('.product-row').forEach(row => {
                    const importe = parseEuroToFloat(row.querySelector('.importe-input').value);
                    totalSumaProductos += importe;
                });

                // Asignar el valor para mostrar con coma
                document.getElementById('total_productos_display').value = formatToEuro(totalSumaProductos);

                const descuento = parseEuroToFloat(document.getElementById('descuento').value);
                let totalAlbaran = totalSumaProductos - descuento;
                if (totalAlbaran < 0) totalAlbaran = 0; // Evitar negativos

                // Asignar el valor para mostrar con coma
                document.getElementById('total_albaran_display').value = formatToEuro(totalAlbaran);
            }

            // Función para añadir una nueva fila de producto
            function addProductRow(initialData = {}) {
                const newRow = productDetailsTable.insertRow();
                newRow.className = 'product-row';

                // Generar el HTML de la fila. Los inputs que se envían tienen el nombre correcto.
                // Los valores se formatean para la UI con coma, pero se manejarán para el envío con punto.
                newRow.innerHTML = `
                    <td>
                        <select name="productos[${rowCount}][producto_id]" class="form-select product-select" required>
                            <option value="">Seleccione un producto</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}" data-price="{{ $producto->precio }}" ${initialData.producto_id == {{ $producto->id }} ? 'selected' : ''}>
                                    {{ $producto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="productos[${rowCount}][unidades]" class="form-control units-input" min="1" value="${initialData.unidades || 1}" required></td>
                    <!-- Los inputs price-input e importe-input son readonly y solo para mostrar -->
                    <!-- Sus valores se leerán y convertirán a punto antes de enviar el formulario -->
                    <td><input type="text" class="form-control price-input" value="${initialData.precio_unitario ? formatToEuro(initialData.precio_unitario) : ''}" readonly></td>
                    <td><input type="text" class="form-control importe-input" value="${initialData.importe ? formatToEuro(initialData.importe) : ''}" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-product-row">X</button></td>
                    <!-- Campos ocultos para enviar los valores con punto decimal al servidor -->
                    <input type="hidden" name="productos[${rowCount}][precio_unitario]" class="hidden-price-input">
                    <input type="hidden" name="productos[${rowCount}][importe]" class="hidden-importe-input">
                `;
                rowCount++; // Incrementa el contador de filas

                // Añadir event listeners a los nuevos elementos
                const productSelect = newRow.querySelector('.product-select');
                const unitsInput = newRow.querySelector('.units-input');
                const removeBtn = newRow.querySelector('.remove-product-row');
                const priceDisplayInput = newRow.querySelector('.price-input'); // Campo visible
                const importeDisplayInput = newRow.querySelector('.importe-input'); // Campo visible
                const hiddenPriceInput = newRow.querySelector('.hidden-price-input'); // Campo oculto para envío
                const hiddenImporteInput = newRow.querySelector('.hidden-importe-input'); // Campo oculto para envío


                productSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.dataset.price; // Este viene ya con punto desde PHP

                    priceDisplayInput.value = price ? formatToEuro(price) : '';
                    hiddenPriceInput.value = price ? parseFloat(price).toFixed(2) : ''; // Guardar con punto para envío

                    unitsInput.dispatchEvent(new Event('input')); // Disparar input para recalcular importe
                });

                unitsInput.addEventListener('input', function() {
                    const price = parseEuroToFloat(priceDisplayInput.value); // Lee el valor visible
                    const units = parseInt(this.value) || 0;
                    const importe = price * units;

                    importeDisplayInput.value = formatToEuro(importe); // Mostrar con coma
                    hiddenImporteInput.value = importe.toFixed(2); // Guardar con punto para envío

                    calculateTotals();
                });

                removeBtn.addEventListener('click', function() {
                    newRow.remove();
                    calculateTotals(); // Recalcular después de eliminar
                });

                // Si hay datos iniciales, forzar el cálculo inicial de precio e importe
                if (initialData.producto_id) {
                    productSelect.dispatchEvent(new Event('change'));
                }
            }

            // Si hay datos de old() (después de un error de validación), rellenar las filas
            @if(old('productos'))
                // Limpiar la fila inicial vacía antes de rellenar con old data
                if (productDetailsTable.rows.length > 0 && productDetailsTable.rows[0].querySelector('.product-select').value === "") {
                    productDetailsTable.rows[0].remove();
                    rowCount = 0; // Reset rowCount as we're rebuilding
                }

                @foreach(old('productos') as $index => $oldProduct)
                    addProductRow({
                        producto_id: {{ $oldProduct['producto_id'] ?? 'null' }},
                        unidades: {{ $oldProduct['unidades'] ?? '1' }},
                        // Obtener precio_unitario e importe de Producto para asegurar el formato correcto desde PHP
                        precio_unitario: {{ \App\Models\Producto::find($oldProduct['producto_id'] ?? null)->precio ?? '0' }},
                        importe: ({{ \App\Models\Producto::find($oldProduct['producto_id'] ?? null)->precio ?? '0' }} * {{ $oldProduct['unidades'] ?? '1' }}).toFixed(2)
                    });
                @endforeach
            @else
                // Asegúrate de que la primera fila se inicialice correctamente si no hay old data
                const firstRowProductSelect = productDetailsTable.rows[0].querySelector('.product-select');
                const firstRowUnitsInput = productDetailsTable.rows[0].querySelector('.units-input');
                const firstRowPriceDisplayInput = productDetailsTable.rows[0].querySelector('.price-input');
                const firstRowImporteDisplayInput = productDetailsTable.rows[0].querySelector('.importe-input');
                const firstRowHiddenPriceInput = productDetailsTable.rows[0].querySelector('.hidden-price-input');
                const firstRowHiddenImporteInput = productDetailsTable.rows[0].querySelector('.hidden-importe-input');
                const firstRowRemoveBtn = productDetailsTable.rows[0].querySelector('.remove-product-row');

                firstRowProductSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.dataset.price;
                    firstRowPriceDisplayInput.value = price ? formatToEuro(price) : '';
                    firstRowHiddenPriceInput.value = price ? parseFloat(price).toFixed(2) : '';
                    firstRowUnitsInput.dispatchEvent(new Event('input'));
                });
                firstRowUnitsInput.addEventListener('input', function() {
                    const price = parseEuroToFloat(firstRowPriceDisplayInput.value);
                    const units = parseInt(this.value) || 0;
                    const importe = price * units;
                    firstRowImporteDisplayInput.value = formatToEuro(importe);
                    firstRowHiddenImporteInput.value = importe.toFixed(2);
                    calculateTotals();
                });
                firstRowRemoveBtn.addEventListener('click', function() {
                    productDetailsTable.rows[0].remove();
                    calculateTotals();
                });
            @endif

            // Event listener para el botón de añadir producto
            addProductBtn.addEventListener('click', () => addProductRow());

            // Event listener para el cambio de descuento
            document.getElementById('descuento').addEventListener('input', calculateTotals);

            // Antes de enviar el formulario, asegurarse de que los campos numéricos visibles
            // que se usarán para los cálculos finales, se conviertan a punto decimal.
            // Para `descuento` directamente en el input, el `type="number"` y el `step="0.01"`
            // suelen manejar bien el formato, pero si hay problemas, puedes añadir un listener
            // aquí para limpiar el valor antes de que la validación lo lea.
            document.getElementById('formAlbaran').addEventListener('submit', function() {
                // Iterar sobre todos los campos de descuento para asegurarse que se envíen con punto
                const descuentoInput = document.getElementById('descuento');
                if (descuentoInput) {
                    descuentoInput.value = parseEuroToFloat(descuentoInput.value);
                }
            });


            // Calcular totales iniciales al cargar la página
            calculateTotals();
        });
    </script>
@endsection