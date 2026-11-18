<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-dark text-white rounded-top-4 py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-file-earmark-plus me-2"></i>
                        <?php echo isset($convenio) ? 'Editar Convenio B2B' : 'Registrar Nuevo Convenio B2B'; ?>
                    </h4>
                </div>
                <div class="card-body p-4 bg-light">
                    
                    <form action="convenio.php?action=<?php echo isset($convenio) ? 'update&id='.$convenio['id_convenio'] : 'create'; ?>" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Empresa Asociada</label>
                            <select name="id_empresa" id="selectEmpresaConvenio" class="form-select form-select-lg" required>
                                <option value="">-- Seleccionar Empresa --</option>
                                <?php foreach ($empresas as $e): ?>
                                    <option value="<?php echo $e['id_empresa']; ?>" <?php echo (isset($convenio) && $convenio['id_empresa'] == $e['id_empresa']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($e['nombre_empresa']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="alert alert-info d-flex align-items-center gap-2 mb-4" role="status">
                            <i class="bi bi-clock-history"></i>
                            <span>La fecha y hora de registro se asignan automáticamente al guardar el convenio.</span>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Nombre de la Flotilla</label>
                                <input type="text" name="nombre_flotilla" class="form-control" value="<?php echo isset($convenio) ? htmlspecialchars($convenio['nombre_flotilla']) : ''; ?>" placeholder="Ej. Flotilla de Ventas Norte" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">Autos</label>
                                <input type="number" name="cantidad_autos" id="cantidadAutosConvenio" class="form-control cantidad-convenio" value="<?php echo $convenio['cantidad_autos'] ?? 0; ?>" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">Motos</label>
                                <input type="number" name="cantidad_motos" id="cantidadMotosConvenio" class="form-control cantidad-convenio" value="<?php echo $convenio['cantidad_motos'] ?? 0; ?>" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">Camionetas</label>
                                <input type="number" name="cantidad_camionetas" id="cantidadCamionetasConvenio" class="form-control cantidad-convenio" value="<?php echo $convenio['cantidad_camionetas'] ?? 0; ?>" min="0" required>
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Composición</label>
                                <input type="text" id="tipoVehiculosConvenio" name="tipo_vehiculos" class="form-control" value="<?php echo isset($convenio) ? htmlspecialchars($convenio['tipo_vehiculos']) : ''; ?>" readonly required>
                                <input type="hidden" name="cantidad_vehiculos" id="cantidadVehiculosConvenio" value="<?php echo $convenio['cantidad_vehiculos'] ?? 0; ?>">
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    $('#selectEmpresaConvenio').select2({
                                        theme: 'bootstrap-5',
                                        width: '100%',
                                        placeholder: '-- Buscar o Seleccionar Empresa --'
                                    });

                                    const cantidades = [
                                        document.getElementById('cantidadAutosConvenio'),
                                        document.getElementById('cantidadMotosConvenio'),
                                        document.getElementById('cantidadCamionetasConvenio')
                                    ];
                                    const total = document.getElementById('cantidadVehiculosConvenio');
                                    const composicion = document.getElementById('tipoVehiculosConvenio');

                                    function actualizarComposicion() {
                                        const nombres = ['Autos', 'Motos', 'Camionetas'];
                                        const seleccionados = [];
                                        let suma = 0;
                                        cantidades.forEach(function(input, index) {
                                            const cantidad = Math.max(0, parseInt(input.value, 10) || 0);
                                            input.value = cantidad;
                                            suma += cantidad;
                                            if (cantidad > 0) seleccionados.push(nombres[index]);
                                        });
                                        total.value = suma;
                                        composicion.value = seleccionados.join(' y ');
                                    }

                                    cantidades.forEach(function(input) {
                                        input.addEventListener('input', actualizarComposicion);
                                    });
                                    actualizarComposicion();
                                });
                            </script>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Frecuencia de Lavado</label>
                                <select name="frecuencia_lavado" class="form-select" required>
                                    <option value="Semanal" <?php echo (isset($convenio) && $convenio['frecuencia_lavado'] == 'Semanal') ? 'selected' : ''; ?>>Semanal</option>
                                    <option value="Quincenal" <?php echo (isset($convenio) && $convenio['frecuencia_lavado'] == 'Quincenal') ? 'selected' : ''; ?>>Quincenal</option>
                                    <option value="Mensual" <?php echo (isset($convenio) && $convenio['frecuencia_lavado'] == 'Mensual') ? 'selected' : ''; ?>>Mensual</option>
                                    <option value="Bajo Demanda" <?php echo (isset($convenio) && $convenio['frecuencia_lavado'] == 'Bajo Demanda') ? 'selected' : ''; ?>>Bajo Demanda</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Contrato / Orden de Compra (PDF)</label>
                            <?php if (isset($convenio) && !empty($convenio['pdf_contrato'])): ?>
                                <div class="mb-2">
                                    <a href="../<?php echo htmlspecialchars($convenio['pdf_contrato']); ?>" target="_blank" class="text-danger fw-bold text-decoration-none">
                                        <i class="bi bi-file-pdf"></i> Ver contrato actual
                                    </a>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="pdf_contrato" class="form-control" accept="application/pdf">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <a href="convenio.php" class="btn btn-outline-danger px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>