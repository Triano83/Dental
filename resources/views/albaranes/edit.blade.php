@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Editar Albarán (Código: {{ $albaran->codigo_albaran }})</h2>
        <a href="{{ route('albaranes.index') }}" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <form action="{{ route('albaranes.update', ['albarane' => $albaran->id]) }}" method="POST" id="formAlbaran">
        @csrf
        @method('PUT')

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
                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $albaran->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre_clinica }} ({{ $cliente->nif }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha_envio" class="form-label">Fecha de Envío:</label>
                        <input type="date" name="fecha_envio" class="form-control" required value="{{ old('fecha_envio', $albaran->fecha_envio->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nombre_paciente" class="form-label">Nombre del Paciente:</label>
                        <input type="text" name="nombre_paciente" class="form-control" required value="{{ old('nombre_paciente', $albaran->nombre_paciente) }}">
                    </div>
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
                        @forelse(old('productos', $albaran->detalleAlbaranes) as $index => $detalle)
                            <tr class="product-row">
                                <td>
                                    <select name="productos[{{ $index }}][producto_id]" class="form-select product-select" required>
                                        <option value="">Seleccione un producto</option>
                                        @foreach($productos as $producto)
                                            <option value="{{ $producto->id }}" data-price="{{ $producto->precio }}"
                                                {{ (old('productos.' . $index . '.producto_id', $detalle->producto_id)) == $producto->id ? 'selected' : '' }}>
                                                {{ $producto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="productos[{{ $index }}][unidades]" class="form-control units-input" min="1" value="{{ old('productos.' . $index . '.unidades', $detalle->unidades) }}" required></td>
                                <td><input type="text" class="form-control price-input" value="{{ number_format(old('productos.' . $index . '.precio_unitario', $detalle->precio_unitario), 2, ',', '.') }}" readonly></td>
                                <td><input type="text" class="form-control importe-input" value="{{ number_format(old('productos.' . $index . '.importe', $detalle->importe), 2, ',', '.') }}" readonly></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-product-row">X</button></td>
                                <input type="hidden" name="productos[{{ $index }}][precio_unitario]" class="hidden-price-input" value="{{ old('productos.' . $index . '.precio_unitario', $detalle->precio_unitario) }}">
                                <input type="hidden" name="productos[{{ $index }}][importe]" class="hidden-importe-input" value="{{ old('productos.' . $index . '.importe', $detalle->importe) }}">
                            </tr>
                        @empty
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
                                <td><input type="text" class="form-control price-input" readonly></td>
                                <td><input type="text" class="form-control importe-input" readonly></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-product-row">X</button></td>
                                <input type="hidden" name="productos[0][precio_unitario]" class="hidden-price-input">
                                <input type="hidden" name="productos[0][importe]" class="hidden-importe-input">
                            </tr>
                        @endforelse
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
                            <input type="number" step="0.01" name="descuento" id="descuento" class="form-control" value="{{ old('descuento', $albaran->descuento) }}" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="total_albaran_display" class="form-label">Total del Albarán:</label>
                            <input type="text" id="total_albaran_display" class="form-control font-weight-bold" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Actualizar Albarán</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addProductBtn = document.getElementById('add-product-row');
            const productDetailsTable = document.getElementById('product-details-table').getElementsByTagName('tbody')[0];
            let rowCount = productDetailsTable.rows.length;

            function formatToEuro(value) {
                return parseFloat(value).toFixed(2).replace('.', ',');
            }

            function parseEuroToFloat(value) {
                return parseFloat(String(value).replace(',', '.')) || 0;
            }

            function calculateTotals() {
                let totalSumaProductos = 0;
                productDetailsTable.querySelectorAll('.product-row').forEach(row => {
                    const importe = parseEuroToFloat(row.querySelector('.importe-input').value);
                    totalSumaProductos += importe;
                });

                document.getElementById('total_productos_display').value = formatToEuro(totalSumaProductos);

                const descuento = parseEuroToFloat(document.getElementById('descuento').value);
                let totalAlbaran = totalSumaProductos - descuento;
                if (totalAlbaran < 0) totalAlbaran = 0;

                document.getElementById('total_albaran_display').value = formatToEuro(totalAlbaran);
            }

            function addProductRow(initialData = {}) {
                const newRow = productDetailsTable.insertRow();
                newRow.className = 'product-row';

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
                    <td><input type="text" class="form-control price-input" value="${initialData.precio_unitario ? formatToEuro(initialData.precio_unitario) : ''}" readonly></td>
                    <td><input type="text" class="form-control importe-input" value="${initialData.importe ? formatToEuro(initialData.importe) : ''}" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-product-row">X</button></td>
                    <input type="hidden" name="productos[${rowCount}][precio_unitario]" class="hidden-price-input">
                    <input type="hidden" name="productos[${rowCount}][importe]" class="hidden-importe-input">
                `;
                rowCount++;

                const productSelect = newRow.querySelector('.product-select');
                const unitsInput = newRow.querySelector('.units-input');
                const removeBtn = newRow.querySelector('.remove-product-row');
                const priceDisplayInput = newRow.querySelector('.price-input');
                const importeDisplayInput = newRow.querySelector('.importe-input');
                const hiddenPriceInput = newRow.querySelector('.hidden-price-input');
                const hiddenImporteInput = newRow.querySelector('.hidden-importe-input');

                productSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.dataset.price;

                    priceDisplayInput.value = price ? formatToEuro(price) : '';
                    hiddenPriceInput.value = price ? parseFloat(price).toFixed(2) : '';

                    unitsInput.dispatchEvent(new Event('input'));
                });

                unitsInput.addEventListener('input', function() {
                    const price = parseEuroToFloat(priceDisplayInput.value);
                    const units = parseInt(this.value) || 0;
                    const importe = price * units;

                    importeDisplayInput.value = formatToEuro(importe);
                    hiddenImporteInput.value = importe.toFixed(2);

                    calculateTotals();
                });

                removeBtn.addEventListener('click', function() {
                    newRow.remove();
                    calculateTotals();
                });

                if (initialData.producto_id) {
                    productSelect.dispatchEvent(new Event('change'));
                }
            }

            productDetailsTable.querySelectorAll('.product-row').forEach((row, index) => {
                const productSelect = row.querySelector('.product-select');
                const unitsInput = row.querySelector('.units-input');
                const removeBtn = row.querySelector('.remove-product-row');
                const priceDisplayInput = row.querySelector('.price-input');
                const importeDisplayInput = row.querySelector('.importe-input');
                const hiddenPriceInput = row.querySelector('.hidden-price-input');
                const hiddenImporteInput = row.querySelector('.hidden-importe-input');

                hiddenPriceInput.value = parseEuroToFloat(priceDisplayInput.value).toFixed(2);
                hiddenImporteInput.value = parseEuroToFloat(importeDisplayInput.value).toFixed(2);

                productSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.dataset.price;
                    priceDisplayInput.value = price ? formatToEuro(price) : '';
                    hiddenPriceInput.value = price ? parseFloat(price).toFixed(2) : '';
                    unitsInput.dispatchEvent(new Event('input'));
                });

                unitsInput.addEventListener('input', function() {
                    const price = parseEuroToFloat(priceDisplayInput.value);
                    const units = parseInt(this.value) || 0;
                    const importe = price * units;
                    importeDisplayInput.value = formatToEuro(importe);
                    hiddenImporteInput.value = importe.toFixed(2);
                    calculateTotals();
                });

                removeBtn.addEventListener('click', function() {
                    row.remove();
                    calculateTotals();
                });
            });

            addProductBtn.addEventListener('click', () => addProductRow());

            const descuentoInput = document.getElementById('descuento');
            descuentoInput.addEventListener('input', calculateTotals);

            document.getElementById('formAlbaran').addEventListener('submit', function() {
                if (descuentoInput) {
                    descuentoInput.value = parseEuroToFloat(descuentoInput.value).toFixed(2);
                }
            });

            calculateTotals();
        });
    </script>
@endsection