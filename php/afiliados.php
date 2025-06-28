<?php
$conn = new mysqli("localhost", "root", "", "odontologia");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Insertar afiliado grupal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre-convenio"])) {
    $nombreConvenio = $_POST["nombre-convenio"];
    $porcentaje = $_POST["porcentaje-descuento"];

    $stmt = $conn->prepare("INSERT INTO afiliadosgrupales (Nombre_Convenio, Porcentaje_Descuento) VALUES (?, ?)");
    $stmt->bind_param("sd", $nombreConvenio, $porcentaje);
    $stmt->execute();
    $stmt->close();
}

// Insertar afiliado individual
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["dni"])) {
    $dni = $_POST["dni"];
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $fechaNacimiento = $_POST["fecha-nacimiento"];
    $sexo = $_POST["sexo"];
    $direccion = $_POST["direccion"];
    $telefonoFijo = $_POST["telefono-fijo"];
    $telefonoMovil = $_POST["telefono-movil"];
    $email = $_POST["email"];
    $costos = $_POST["costo-tratamiento"];
    $idGrupal = $_POST["id-afiliado-grupal"] ?: NULL;

    $stmt = $conn->prepare("INSERT INTO afiliado (DNI, Nombre, Apellido, Fecha_Nacimiento, Sexo, Direccion, Telefono_Fijo, Telefono_Movil, Email, Costos_Tratamientos, ID_AfiliadosGrupales) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssds", $dni, $nombre, $apellido, $fechaNacimiento, $sexo, $direccion, $telefonoFijo, $telefonoMovil, $email, $costos, $idGrupal);
    $stmt->execute();
    $stmt->close();
}

// Obtener listas para mostrar
$grupales = $conn->query("SELECT * FROM afiliadosgrupales");
$individuales = $conn->query("SELECT a.*, g.Nombre_Convenio FROM afiliado a LEFT JOIN afiliadosgrupales g ON a.ID_AfiliadosGrupales = g.ID_AfiliadosGrupales");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Afiliados - Clínica Odontológica</title>
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

    <section class="afiliados">
        <div class="container">
            <h3>Lista de Afiliados Grupales</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Convenio</th>
                        <th>Descuento (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($g = $grupales->fetch_assoc()): ?>
                        <tr>
                            <td><?= $g["ID_AfiliadosGrupales"] ?></td>
                            <td><?= $g["Nombre_Convenio"] ?></td>
                            <td><?= $g["Porcentaje_Descuento"] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Afiliados Grupales</h2>
            <form method="POST">
                <label for="nombre-convenio">Nombre Convenio:</label>
                <input type="text" id="nombre-convenio" name="nombre-convenio" required />

                <label for="porcentaje-descuento">Porcentaje de Descuento:</label>
                <input type="number" id="porcentaje-descuento" name="porcentaje-descuento" min="0" max="100" required />

                <button type="submit">Crear Grupo</button>
            </form>

            <h3>Lista de Afiliados Individuales</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Convenio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($a = $individuales->fetch_assoc()): ?>
                        <tr>
                            <td><?= $a["ID_Afiliado"] ?></td>
                            <td><?= $a["DNI"] ?></td>
                            <td><?= $a["Nombre"] ?></td>
                            <td><?= $a["Apellido"] ?></td>
                            <td><?= $a["Nombre_Convenio"] ?? "Ninguno" ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Afiliados Individuales</h2>
            <form method="POST">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" required />

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required />

                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required />

                <label for="fecha-nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha-nacimiento" name="fecha-nacimiento" required />

                <label for="sexo">Sexo:</label>
                <select id="sexo" name="sexo" required>
                    <option value="">Seleccionar</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                    <option value="X">Otro</option>
                </select>

                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" required />

                <label for="telefono-fijo">Teléfono Fijo:</label>
                <input type="text" id="telefono-fijo" name="telefono-fijo" />

                <label for="telefono-movil">Teléfono Móvil:</label>
                <input type="text" id="telefono-movil" name="telefono-movil" required />

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required />

                <label for="costo-tratamiento">Costos Tratamientos:</label>
                <input type="number" id="costo-tratamiento" name="costo-tratamiento" min="0" step="0.01" required />

                <label for="id-afiliado-grupal">Afiliado Grupal:</label>
                <select id="id-afiliado-grupal" name="id-afiliado-grupal">
                    <option value="">Ninguno</option>
                    <?php
                    $resultGrupos = $conn->query("SELECT ID_AfiliadosGrupales, Nombre_Convenio FROM afiliadosgrupales");
                    while ($row = $resultGrupos->fetch_assoc()):
                    ?>
                        <option value="<?= $row['ID_AfiliadosGrupales'] ?>"><?= $row['Nombre_Convenio'] ?></option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">Crear Afiliado</button>
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