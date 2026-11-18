<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-dark text-white rounded-top-4 py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-clipboard2-plus me-2"></i>
                        <?php echo isset($orden) ? 'Editar Orden de Visita #' . $orden['id_orden'] : 'Generar Nueva Orden de Visita'; ?>
                    </h4>
                </div>
                <div class="card-body p-4 bg-light">
                    <?php if (!empty($_GET['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="orden.php?action=<?php echo isset($orden) ? 'update&id='.$orden['id_orden'] : 'create'; ?>" method="POST" enctype="multipart/form-data">
                        
                        <!-- FILA 1: Empresa, convenio seleccionado y fecha -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">Empresa a Visitar</label>
                                <select name="id_empresa" id="selectEmpresa" class="form-select" required>
                                    <option value="">-- Buscar o Seleccionar Empresa --</option>
                                    <?php foreach ($empresas as $e): ?>
                                        <option value="<?php echo $e['id_empresa']; ?>" <?php echo (isset($orden) && $orden['id_empresa'] == $e['id_empresa']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($e['nombre_empresa']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="infoConvenio" class="text-primary fw-bold d-block mt-1"></small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">Convenio / Flotilla</label>
                                <select name="id_convenio" id="selectConvenio" class="form-select">
                                    <option value="">-- Sin convenio específico --</option>
                                    <?php foreach ($convenios as $c): ?>
                                        <option value="<?php echo $c['id_convenio']; ?>"
                                                data-empresa="<?php echo $c['id_empresa']; ?>"
                                                <?php echo (isset($orden) && $orden['id_convenio'] == $c['id_convenio']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($c['empresa'] . ' - ' . $c['nombre_flotilla']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">Día de la Visita</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-regular fa-calendar-days"></i></span>
                                    <input type="date" name="fecha_visita" id="fechaVisita" class="form-control"
                                           value="<?php echo isset($orden['fecha_visita']) ? date('Y-m-d', strtotime($orden['fecha_visita'])) : ''; ?>"
                                           required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">Horario de la Visita</label>
                                <select name="horario_visita" id="horarioVisita" class="form-select" required>
                                    <?php $horaOrden = isset($orden['fecha_visita']) ? date('H:i', strtotime($orden['fecha_visita'])) : ''; ?>
                                    <option value="07:00:00" <?php echo $horaOrden === '07:00' ? 'selected' : ''; ?>>Matutino (07:00 - 13:00)</option>
                                    <option value="15:00:00" <?php echo $horaOrden === '15:00' ? 'selected' : ''; ?>>Vespertino (15:00 - 21:00)</option>
                                </select>
                                <div class="form-text" id="ayudaHorario">Selecciona un horario disponible.</div>
                            </div>
                        </div>

                        <!-- FILA 2: Calculadora (Jala precio de Servicios y cantidad de Convenios) -->
                        <div class="row mb-3 p-3 bg-white rounded-3 border mx-0">
                            <div class="col-md-5 mb-2 mb-md-0">
                                <label class="form-label fw-bold text-secondary">Tipo de Lavado / Paquete</label>
                                <select name="id_servicio" id="selectServicio" class="form-select" required>
                                    <option value="0" data-precio="0">-- Selecciona un servicio --</option>
                                    <?php if (!empty($servicios)): ?>
                                        <?php foreach ($servicios as $s): ?>
                                            <option value="<?php echo $s['id_servicio']; ?>"
                                                    <?php echo (isset($orden) && $orden['id_servicio'] == $s['id_servicio']) ? 'selected' : ''; ?>
                                                    data-precio-auto="<?php echo $s['precio']; ?>"
                                                    data-precio-camioneta="<?php echo $s['precio_camioneta']; ?>"
                                                    data-precio-moto="<?php echo $s['precio_moto']; ?>">
                                                <?php echo htmlspecialchars($s['nombre']); ?> (Auto $<?php echo number_format($s['precio'], 2); ?> / Camioneta $<?php echo number_format($s['precio_camioneta'], 2); ?> / Moto $<?php echo number_format($s['precio_moto'], 2); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">Autos</label>
                                <input type="number" name="cantidad_autos" id="cantidadAutosOrden" class="form-control cantidad-orden"
                                       value="<?php echo $orden['cantidad_autos'] ?? 0; ?>" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">Motos</label>
                                <input type="number" name="cantidad_motos" id="cantidadMotosOrden" class="form-control cantidad-orden"
                                       value="<?php echo $orden['cantidad_motos'] ?? 0; ?>" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">Camionetas</label>
                                <input type="number" name="cantidad_camionetas" id="cantidadCamionetasOrden" class="form-control cantidad-orden"
                                       value="<?php echo $orden['cantidad_camionetas'] ?? 0; ?>" min="0" required>
                            </div>
                            <input type="hidden" name="tipo_vehiculo" id="tipoVehiculo" value="<?php echo isset($orden) ? htmlspecialchars($orden['tipo_vehiculo']) : ''; ?>">
                            <input type="hidden" name="cantidad_vehiculos" id="cantidadVehiculos" value="<?php echo $orden['cantidad_vehiculos'] ?? 0; ?>">
                        </div>

                        <!-- FILA 3: Estado y Total Calculado -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Estado de la Visita</label>
                                <select name="estado" id="selectEstado" class="form-select" required>
                                    <option value="Programada" <?php echo (isset($orden) && $orden['estado'] == 'Programada') ? 'selected' : ''; ?>>Programada</option>
                                    <option value="En Proceso" <?php echo (isset($orden) && $orden['estado'] == 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
                                    <option value="Finalizada" <?php echo (isset($orden) && $orden['estado'] == 'Finalizada') ? 'selected' : ''; ?>>Finalizada</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Total a Cobrar ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white fw-bold">$</span>
                                    <input type="number" step="0.01" name="total_cobrado" id="totalCobrado" class="form-control fw-bold" 
                                           value="<?php echo isset($orden) ? $orden['total_cobrado'] : '0.00'; ?>" required>
                                </div>
                                <div class="form-text small">Se calcula automático (puedes ajustarlo si hay descuento).</div>
                            </div>
                        </div>

                        <!-- FILA 4: Comprobante (Solo visible si ya está Finalizada) -->
                        <div class="mb-4" id="bloqueComprobante" style="<?php echo (isset($orden) && ($orden['estado'] == 'Finalizada' || !empty($orden['comprobante_pdf']))) ? '' : 'display: none;'; ?>">
                            <label class="form-label fw-bold text-secondary">Comprobante o Factura (PDF)</label>
                            <?php if (isset($orden) && !empty($orden['comprobante_pdf'])): ?>
                                <div class="mb-2">
                                    <a href="../<?php echo htmlspecialchars($orden['comprobante_pdf']); ?>" target="_blank" class="text-danger fw-bold text-decoration-none">
                                        <i class="bi bi-file-pdf"></i> Ver comprobante actual
                                    </a>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="comprobante_pdf" class="form-control" accept="application/pdf">
                            <div class="form-text">Sube aquí la hoja de servicio firmada o factura una vez concluido el lavado.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <a href="orden.php" class="btn btn-outline-danger px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i><?php echo isset($orden) ? 'Actualizar Orden' : 'Generar Orden'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Recibimos los convenios desde PHP
        const convenios = <?php echo json_encode($convenios ?? []); ?>;

        // 1. Buscador de Empresas (Select2)
        $('#selectEmpresa').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Buscar o Seleccionar Empresa --'
        });
        $('#selectConvenio').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Seleccionar convenio --'
        });

        // 2. Día y bloque horario: los ocupados se deshabilitan en pantalla.
        const horariosOcupados = <?php echo json_encode($horariosOcupados ?? []); ?>;
        const fechaVisita = document.getElementById('fechaVisita');
        const horarioVisita = document.getElementById('horarioVisita');
        const ayudaHorario = document.getElementById('ayudaHorario');

        function actualizarDisponibilidad() {
            const fecha = fechaVisita.value;
            let disponibles = 0;
            Array.from(horarioVisita.options).forEach(function(option) {
                const ocupado = horariosOcupados.some(function(reserva) {
                    return reserva.fecha === fecha && reserva.horario.substring(0, 5) === option.value.substring(0, 5);
                });
                option.disabled = ocupado;
                if (!ocupado) disponibles++;
            });

            if (horarioVisita.selectedOptions[0] && horarioVisita.selectedOptions[0].disabled) {
                const primeraDisponible = Array.from(horarioVisita.options).find(function(option) {
                    return !option.disabled;
                });
                horarioVisita.value = primeraDisponible ? primeraDisponible.value : '';
            }
            ayudaHorario.textContent = disponibles
                ? 'Los horarios ocupados no se pueden seleccionar.'
                : 'No hay horarios disponibles para ese día.';
        }

        fechaVisita.addEventListener('change', actualizarDisponibilidad);
        actualizarDisponibilidad();

        const selectServicio = document.getElementById('selectServicio');
        const selectConvenio = document.getElementById('selectConvenio');
        const inputTotal = document.getElementById('totalCobrado');
        const selectTipoVehiculo = document.getElementById('tipoVehiculo');
        const inputCantidadTotal = document.getElementById('cantidadVehiculos');
        const cantidades = {
            Auto: document.getElementById('cantidadAutosOrden'),
            Moto: document.getElementById('cantidadMotosOrden'),
            Camioneta: document.getElementById('cantidadCamionetasOrden')
        };
        const infoConvenio = document.getElementById('infoConvenio');

        function calcularTotal() {
            const opcionSeleccionada = selectServicio.options[selectServicio.selectedIndex];
            const precios = {
                Auto: parseFloat(opcionSeleccionada.getAttribute('data-precio-auto')) || 0,
                Moto: parseFloat(opcionSeleccionada.getAttribute('data-precio-moto')) || 0,
                Camioneta: parseFloat(opcionSeleccionada.getAttribute('data-precio-camioneta')) || 0
            };
            let total = 0;
            let cantidadTotal = 0;
            const tipos = [];

            Object.keys(cantidades).forEach(function(tipo) {
                const cantidad = Math.max(0, parseInt(cantidades[tipo].value, 10) || 0);
                cantidades[tipo].value = cantidad;
                cantidadTotal += cantidad;
                total += precios[tipo] * cantidad;
                if (cantidad > 0) tipos.push(tipo);
            });

            inputCantidadTotal.value = cantidadTotal;
            selectTipoVehiculo.value = tipos.join(' y ');
            inputTotal.value = total.toFixed(2);
        }

        function aplicarConvenio(convenio) {
            if (convenio) {
                cantidades.Auto.value = convenio.cantidad_autos || 0;
                cantidades.Moto.value = convenio.cantidad_motos || 0;
                cantidades.Camioneta.value = convenio.cantidad_camionetas || 0;
                infoConvenio.textContent = `Convenio: ${convenio.nombre_flotilla} (${convenio.frecuencia_lavado})`;
            } else {
                cantidades.Auto.value = 1;
                cantidades.Moto.value = 0;
                cantidades.Camioneta.value = 0;
                infoConvenio.textContent = 'Sin convenio registrado (1 auto por defecto)';
            }
            calcularTotal();
        }

        function filtrarConveniosPorEmpresa(idEmpresa) {
            let convenioActualValido = false;

            Array.from(selectConvenio.options).forEach(function(option) {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                const perteneceAEmpresa = option.getAttribute('data-empresa') == idEmpresa;
                option.hidden = !perteneceAEmpresa;
                option.disabled = !perteneceAEmpresa;

                if (perteneceAEmpresa && option.value == selectConvenio.value) {
                    convenioActualValido = true;
                }
            });

            if (!convenioActualValido && selectConvenio.value) {
                selectConvenio.value = '';
                $('#selectConvenio').trigger('change.select2');
                aplicarConvenio(null);
            }

            $('#selectConvenio').trigger('change.select2');
        }

        // Al elegir un convenio se carga su empresa y sus cantidades como propuesta editable.
        $('#selectConvenio').on('change', function() {
            const convenio = convenios.find(c => c.id_convenio == $(this).val());
            if (convenio) {
                $('#selectEmpresa').val(convenio.id_empresa).trigger('change.select2');
            }
            aplicarConvenio(convenio);
        });

        // Al cambiar la empresa solo se muestran sus convenios disponibles.
        $('#selectEmpresa').on('change', function() {
            filtrarConveniosPorEmpresa($(this).val());
            const convenioSeleccionado = convenios.find(c => c.id_convenio == selectConvenio.value);
            if (!convenioSeleccionado || convenioSeleccionado.id_empresa != $(this).val()) {
                selectConvenio.value = '';
                $('#selectConvenio').trigger('change.select2');
                aplicarConvenio(null);
            }
        });

        const convenioInicial = convenios.find(c => c.id_convenio == selectConvenio.value);
        filtrarConveniosPorEmpresa($('#selectEmpresa').val());
        if (convenioInicial) aplicarConvenio(convenioInicial);

        // Al cambiar servicio o cantidades, recalcular.
        selectServicio.addEventListener('change', calcularTotal);
        Object.values(cantidades).forEach(function(input) {
            input.addEventListener('input', calcularTotal);
        });
        calcularTotal();

        // 5. Mostrar Comprobante PDF solo si el estado es Finalizada
        const selectEstado = document.getElementById('selectEstado');
        const bloqueComprobante = document.getElementById('bloqueComprobante');

        selectEstado.addEventListener('change', function() {
            bloqueComprobante.style.display = (this.value === 'Finalizada') ? 'block' : 'none';
        });
    });
</script>