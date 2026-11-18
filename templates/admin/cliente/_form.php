<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-dark text-white rounded-top-4 py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-building-<?php echo isset($cliente) ? 'up' : 'add'; ?> me-2"></i>
                        <?php echo isset($cliente) ? 'Editar Empresa: ' . htmlspecialchars($cliente['nombre_empresa']) : 'Registrar Nueva Empresa'; ?>
                    </h4>
                </div>
                <div class="card-body p-4 bg-light">
                    
                    <!-- EL ENCTYPE ES OBLIGATORIO PARA SUBIR ARCHIVOS -->
                    <form action="cliente.php?action=<?php echo isset($cliente) ? 'update&id='.$cliente['id_empresa'] : 'create'; ?>" method="POST" enctype="multipart/form-data">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Nombre de la Empresa</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" name="nombre_empresa" class="form-control" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['nombre_empresa']) : ''; ?>" placeholder="Ej. Transportes del Bajío" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Teléfono Corporativo</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" name="telefono" class="form-control" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['telefono']) : ''; ?>" placeholder="461 000 0000" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Contacto Principal</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                    <input type="text" name="contacto_principal" class="form-control" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['contacto_principal']) : ''; ?>" placeholder="Ej. Ing. Carlos Martínez" required>
                                </div>
                            </div>
                            
                            <!-- ✅ CAMPO INTEGRADO CON FLATPICKR -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Fecha de Registro / Cita</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <input type="text" name="fecha_registro" class="form-control datepicker" value="<?php echo isset($cliente['fecha_registro']) ? htmlspecialchars($cliente['fecha_registro']) : ''; ?>" placeholder="Selecciona una fecha">
                                </div>
                            </div>
                        </div>

                        <!-- CAMPO DE CARGA DE ARCHIVO -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Logo de la Empresa (Opcional)</label>
                            
                            <?php if (isset($cliente) && !empty($cliente['logo_empresa'])): ?>
                                <div class="d-flex align-items-center mb-3 p-2 border rounded bg-white">
                                    <img src="../<?php echo htmlspecialchars($cliente['logo_empresa']); ?>" alt="Logo Actual" width="50" height="50" class="rounded-circle object-fit-cover me-3 shadow-sm">
                                    <span class="text-muted small">Logo actual (Sube uno nuevo para reemplazarlo)</span>
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" name="logo_empresa" class="form-control form-control-lg" accept="image/png, image/jpeg, image/webp">
                            <div class="form-text">Formatos permitidos: JPG, PNG.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <a href="cliente.php" class="btn btn-outline-danger px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i><?php echo isset($cliente) ? 'Actualizar Empresa' : 'Guardar Empresa'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Inicialización de Flatpickr -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr(".datepicker", {
            locale: "es",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            defaultDate: "today"
        });
    });
</script>