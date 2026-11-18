<?php $permisos = $permisos ?? []; ?>
<?php $datos = $datos ?? []; ?>

<form enctype="multipart/form-data" action="correo.php?accion=<?php echo $accion ?? 'create'; ?>&id=<?php echo $_GET['id'] ?? $datos['id'] ?? ''; ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $_GET['id'] ?? $datos['id'] ?? ''; ?>">


    <div class="mb-3">
        <label class="form-label">correo</label>
        <input type="text" name="data[correo]" value="<?php echo $datos['correo'] ?? ''; ?>" class="form-control" id="correo" required>
    </div>
    
    <input type="submit" name="enviar" class="btn btn-success" value="Guardar">
</form>