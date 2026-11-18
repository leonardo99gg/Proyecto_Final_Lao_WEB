<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-dark text-white rounded-top-4 py-3">
                    <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Capturar Cotización Manual</h4>
                </div>
                <div class="card-body p-4 bg-light">
                    <form action="cotizacion.php?action=create" method="POST" enctype="multipart/form-data">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Nombre del Solicitante</label>
                                <input type="text" name="nombre_solicitante" class="form-control" placeholder="Ej. Carlos Martínez" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Nombre de la Empresa</label>
                                <input type="text" name="nombre_empresa" class="form-control" placeholder="Ej. Transportes del Bajío" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Correo Empresarial</label>
                                <input type="email" name="correo_empresarial" class="form-control" placeholder="ejemplo@empresa.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Teléfono de Contacto</label>
                                <input type="tel" name="telefono_contacto" class="form-control" placeholder="461 000 0000" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">Autos</label>
                                <input type="number" name="cantidad_autos" id="cantidadAutosCotizacion" class="form-control cantidad-cotizacion" min="0" value="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">Motos</label>
                                <input type="number" name="cantidad_motos" id="cantidadMotosCotizacion" class="form-control cantidad-cotizacion" min="0" value="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">Camionetas</label>
                                <input type="number" name="cantidad_camionetas" id="cantidadCamionetasCotizacion" class="form-control cantidad-cotizacion" min="0" value="0" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">Frecuencia</label>
                                <select name="frecuencia_plan" class="form-select" required>
                                    <option value="Semanal">Semanal</option>
                                    <option value="Quincenal">Quincenal</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="tipo_vehiculos" id="tipoVehiculosCotizacion">
                        <input type="hidden" name="numero_unidades" id="numeroUnidadesCotizacion" value="0">

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Notas Adicionales</label>
                            <textarea name="notas_adicionales" class="form-control" rows="3" placeholder="Requerimientos del patio, horarios..."></textarea>
                        </div>

                        <div class="mb-4 pt-3 border-top">
                            <label class="form-label fw-bold text-secondary">Foto del Estacionamiento / Patio (Opcional)</label>
                            <input type="file" name="foto_estacionamiento" class="form-control" accept="image/png, image/jpeg">
                            <div class="form-text">Adjuntar imagen del área de maniobras proporcionada por el cliente.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <a href="cotizacion.php" class="btn btn-outline-danger px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Guardar Prospecto</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const campos = [
                        document.getElementById('cantidadAutosCotizacion'),
                        document.getElementById('cantidadMotosCotizacion'),
                        document.getElementById('cantidadCamionetasCotizacion')
                    ];
                    const total = document.getElementById('numeroUnidadesCotizacion');
                    const tipo = document.getElementById('tipoVehiculosCotizacion');
                    const nombres = ['Autos', 'Motos', 'Camionetas'];

                    function actualizarVehiculos() {
                        let suma = 0;
                        const tipos = [];
                        campos.forEach(function(campo, index) {
                            const cantidad = Math.max(0, parseInt(campo.value, 10) || 0);
                            campo.value = cantidad;
                            suma += cantidad;
                            if (cantidad > 0) tipos.push(nombres[index]);
                        });
                        total.value = suma;
                        tipo.value = tipos.join(' y ');
                    }

                    campos.forEach(function(campo) {
                        campo.addEventListener('input', actualizarVehiculos);
                    });
                    actualizarVehiculos();
                });
            </script>
        </div>
    </div>
</div>