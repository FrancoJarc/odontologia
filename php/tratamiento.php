<?php
$conn = new mysqli("localhost", "root", "", "odontologia");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Insertar tratamiento si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST["codigo-tratamiento"];
    $nombre = $_POST["nombre-tratamiento"];
    $costo = $_POST["costo-tratamiento"];

    $stmt = $conn->prepare("INSERT INTO Tratamiento (Código, Nombre, Costo) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $codigo, $nombre, $costo);
    $stmt->execute();
    $stmt->close();
}

// Obtener todos los tratamientos
$tratamientos = $conn->query("SELECT Código, Nombre, Costo FROM Tratamiento");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tratamientos - Clínica Odontológica</title>
    <link rel="stylesheet" href="../css/estilos.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
</head>

<body>
    <header>
        <div class="container-nav">
            <img src="../imagenes/logo.png" alt="Logo Clínica Odontológica" class="logo" />
            <nav>
                <ul>
                    <li><a href="../html/index.html">Inicio</a></li>
                    <li><a href="../php/consultorio.php">Sedes</a></li>
                    <li><a href="../html/equipamiento.html">Equipamiento/Insumos</a></li>
                    <li><a href="../php/afiliados.php">Afiliados</a></li>
                    <li><a href="../php/odontologos.php">Odontólogos</a></li>
                    <li><a href="../php/citas.php">Citas</a></li>
                    <li><a href="../php/tratamiento.php">Tratamientos</a></li>
                    <li><a href="../html/historia-clinica.html">Historia Clínica</a></li>
                    <li><a href="../html/portal-cliente.html">Portal Paciente</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="tratamientos">
        <div class="container">
            <h3>Lista de Tratamientos</h3>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Costo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($t = $tratamientos->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($t["Código"]) ?></td>
                            <td><?= htmlspecialchars($t["Nombre"]) ?></td>
                            <td>$<?= number_format($t["Costo"], 2, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Registrar Tratamiento</h2>

            <form method="POST" action="">
                <label for="codigo-tratamiento">Código Tratamiento:</label>
                <input type="text" id="codigo-tratamiento" name="codigo-tratamiento" required />

                <label for="nombre-tratamiento">Nombre:</label>
                <input type="text" id="nombre-tratamiento" name="nombre-tratamiento" required />

                <label for="costo-tratamiento">Costo:</label>
                <input type="number" id="costo-tratamiento" name="costo-tratamiento" step="0.01" required />

                <button type="submit">Guardar Tratamiento</button>
            </form>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2025 Clínica Odontológica. Todos los derechos reservados.</p>
            <ul class="footer-links">
                <li><a href="../html/contacto.html">Contacto</a></li>
                <li><a href="#">Política de Privacidad</a></li>
            </ul>
        </div>
    </footer>

</body>

</html>

<?php $conn->close(); ?>