<?php require 'partials/header2.view.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan de Suscripción</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            }
        body {
            display: flex;
            flex-direction: column;
        }
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Hide radios/checkboxes and style labels */
        .option-selected input[type="radio"], .option-selected input[type="checkbox"] {
            display: none;
        }

        .option-selected label {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0f8b8d;
            color: white;
            border: 2px solid #0f8b8d;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s, transform 0.3s;
            text-align: center;
            border-radius: 5px;
            width: 100%;
        }

        .option-selected label:hover {
            background-color: #0b7375;
            border-color: #0b7375;
        }

        .option-selected input[type="radio"]:checked + label, 
        .option-selected input[type="checkbox"]:checked + label {
            background-color: #0b7375;
            border-color: #0b7375;
            transform: scale(1.05);
        }

        button { margin-top: 15px; }

        .card {
            border: none;
            border-radius: 8px;
            transition: box-shadow 0.3s;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card .badge {
            border-radius: 5px;
            font-size: 0.8rem;
        }

        .fw-bold {
            font-weight: 600 !important;
        }

        .summary-box {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        #summary-text {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .step-header {
            font-size: 1.25rem;
            margin-bottom: 15px;
            color: #0f8b8d;
            font-weight: 700;
        }

        .option-list ul {
            padding-left: 20px;
        }

        .option-list li {
            margin-bottom: 5px;
        }

        .section-note {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .form-section .form-label {
            font-weight: 500;
        }

        .btn-success {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
        }

        .btn-success:hover {
            background-color: #138f9f !important;
            border-color: #138f9f !important;
        }

    </style>

    <script>
        function mostrarPlan() {
            document.getElementById("plan-section").style.display = "block";
            document.getElementById("extras-section").style.display = "none";
            document.getElementById("datos-section").style.display = "none";
        }

        function verificarSeleccion() {
            const planComfort = document.getElementById('comfort');
            const planPremium = document.getElementById('premium');

            if (!planComfort.checked && !planPremium.checked) {
                alert("Por favor, selecciona un plan antes de continuar.");
            } else {
                mostrarExtras();
            }
        }

        function mostrarExtras() {
            document.getElementById("plan-section").style.display = "none";
            document.getElementById("extras-section").style.display = "block";
            document.getElementById("datos-section").style.display = "none";
        }

        function mostrarDatos() {
            document.getElementById("plan-section").style.display = "none";
            document.getElementById("extras-section").style.display = "none";
            document.getElementById("datos-section").style.display = "block";
        }

        function actualizarResumen() {
            const extrasList = document.getElementById('extras-list');
            const planesList = document.getElementById('planes-list');
            const totalElement = document.getElementById('total');
            var summaryText = document.getElementById('summary-text');

            document.getElementById("total").style.display = "block";
            extrasList.innerHTML = '';
            planesList.innerHTML = '';
            summaryText.innerHTML = '';
            let total = 0;

            var comfort = document.getElementById('comfort');
            if (comfort.checked) {
                const listItem = document.createElement('li');
                listItem.textContent = 'Comfort';
                planesList.appendChild(listItem);
                total += 19.99;
                document.getElementById("plan-sel").style.display = "block";
            }
            var premium = document.getElementById('premium');
            if (premium.checked) {
                const listItem = document.createElement('li');
                listItem.textContent = 'Premium';
                planesList.appendChild(listItem);
                total += 25.99;
                document.getElementById("plan-sel").style.display = "block";
            }

            var entrenador = document.getElementById('entrenador');
            if (entrenador.checked) {
                const listItem = document.createElement('li');
                listItem.textContent = 'Entrenador Personal';
                extrasList.appendChild(listItem);
                total += 9.99;
                document.getElementById("extra-sel").style.display = "block";
            }
            var agua = document.getElementById('agua');
            if (agua.checked) {
                const listItem = document.createElement('li');
                listItem.textContent = 'Agua';
                extrasList.appendChild(listItem);
                total += 3.00;
                document.getElementById("extra-sel").style.display = "block";
            }

            var extrasSeleccionados = [];
            if (entrenador.checked) {
                extrasSeleccionados.push('Entrenador Personal');
            }
            if (agua.checked) {
                extrasSeleccionados.push('Agua');
            }
            document.getElementById('extrasSelected').value = extrasSeleccionados.join(',');

            totalElement.textContent = 'Total: €' + total.toFixed(2);
        }
    </script>
</head>
<body>
    <div class="main d-flex justify-content-center py-4">
        <div class="container bg-white rounded shadow-sm p-4">
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <p class="mb-0 fw-bold">EnerGym</p>
                            <p class="mb-0 text-muted" style="font-size:0.9rem;">C/ José María Roquero 4</p>
                        </div>
                        <a href="javascript:void(0);" class="text-decoration-none text-info fw-bold" onclick="mostrarPlan()">Atrás</a>
                    </div>

                    <form action="procesar_plan.php" method="post" class="form-section">

                        <!-- Sección PLAN -->
                        <div id="plan-section">
                            <h2 class="step-header">PLAN</h2>
                            <p class="section-note">Escoge la suscripción que mejor se adapte a tus necesidades.</p>
                            <div class="row g-3">
                                <!-- Plan Comfort -->
                                <div class="col-md-6">
                                    <div class="card h-100 p-3">
                                        <div class="card-body d-flex flex-column">
                                            <h3 class="card-title fs-5 fw-bold text-dark">COMFORT</h3>
                                            <p class="text-decoration-line-through text-muted mb-0">€24,99</p>
                                            <p class="fw-bold text-info fs-5">€19,99</p>
                                            <ul class="list-unstyled mb-3 option-list">
                                                <li>✔️ Reserva con 36h de antelación 1 clase</li>
                                                <li>✔️ Planes de entrenamiento en tu app EnerGym</li>
                                                <li>✔️ YONGO Sports Water ahora por solo ¡3,90€!</li>
                                            </ul>
                                            <p class="text-muted mb-2">€0.00 cuota de inscripción</p>
                                            <div class="text-center mt-auto option-selected">
                                                <input type="radio" id="comfort" name="plan" value="1" onchange="actualizarResumen()">
                                                <label for="comfort">Elegir Comfort</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Plan Premium -->
                                <div class="col-md-6">
                                    <div class="card h-100 p-3" style="border: 1px solid #0f8b8d">
                                        <div class="card-body d-flex flex-column">
                                            <div class="mb-2">
                                                <span class="badge bg-info">MEJOR VALORADA</span>
                                            </div>
                                            <h3 class="card-title fs-5 fw-bold text-dark">PREMIUM</h3>
                                            <p class="text-decoration-line-through text-muted mb-0">€29,99</p>
                                            <p class="fw-bold text-info fs-5">€25,99</p>
                                            <ul class="list-unstyled mb-3 option-list">
                                                <li>✔️ Todo desde Plan Comfort, y</li>
                                                <li>✔️ Reserva con 48h de antelación ¡DOS CLASES!</li>
                                                <li>✔️ YONGO Sports Water ahora por solo !1,90€! (precio oficial 4,90€)</li>
                                                <li>✔️ IA para asesoramientos de entrenamientos y nutrición</li>
                                            </ul>
                                            <p class="text-muted mb-2">€0,00 cuota de inscripción</p>
                                            <div class="text-center mt-auto option-selected">
                                                <input type="radio" id="premium" name="plan" value="2" onchange="actualizarResumen()">
                                                <label for="premium">Elegir Premium</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="button" class="btn btn-success w-100" onclick="verificarSeleccion()" id='cont1'>Continuar</button>
                            </div>
                        </div>

                        <!-- Sección EXTRAS -->
                        <div id="extras-section" style="display:none;">
                            <h2 class="step-header">EXTRAS</h2>
                            <p class="section-note">Servicios adicionales para mejorar tu experiencia.</p>
                            <div class="row g-3">
                                <!-- Entrenador Personal -->
                                <div class="col-md-6">
                                    <div class="card h-100 p-3">
                                        <div class="card-body d-flex flex-column">
                                            <h2 class="h5 fw-bold text-dark">Entrenador Personal</h2>
                                            <ul class="list-unstyled mb-3 option-list">
                                                <li>✔️ Asesoramiento personalizado</li>
                                                <li>✔️ Sesiones privadas de entrenamiento</li>
                                            </ul>
                                            <p class="fw-bold text-info fs-5">€9,99 / sesión</p>
                                            <div class="text-center mt-auto option-selected">
                                                <input type="checkbox" id="entrenador" name="extra" value="Entrenador Personal" onchange="actualizarResumen()">
                                                <label for="entrenador">Elegir Entrenador Personal</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Agua -->
                                <div class="col-md-6">
                                    <div class="card h-100 p-3">
                                        <div class="card-body d-flex flex-column">
                                            <h2 class="h5 fw-bold text-dark">Yongo</h2>
                                            <ul class="list-unstyled mb-3 option-list">
                                                <li>✔️ Botella de agua premium</li>
                                                <li>✔️ Reposición ilimitada</li>
                                            </ul>
                                            <p class="fw-bold text-info fs-5">€3,00</p>
                                            <div class="text-center mt-auto option-selected">
                                                <input type="checkbox" id="agua" name="extra" value="Agua" onchange="actualizarResumen()">
                                                <label for="agua">Elegir Agua de Pago</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="button" class="btn btn-success w-100" onclick="mostrarDatos()" id='cont2'>Continuar</button>
                            </div>
                        </div>

                        <!-- Sección DATOS -->
                        <div id="datos-section" style="display:none;">
                            <h2 class="step-header">DATOS DE PAGO</h2>
                            <p class="section-note">Por favor, completa la siguiente información para procesar tu pago.</p>
                            <input type="text" name="extrasSelected" id="extrasSelected" hidden>

                            <div class="mb-3">
                                <label for="dni" class="form-label fw-bold">DNI:</label>
                                <input type="text" class="form-control" id="dni" name="dni" placeholder="Ej: 000000X" required>
                            </div>

                            <div class="mb-3 row">
                            <div class="col-md-5">
                                <label for="nombre" class="form-label fw-bold">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Juan Pérez" required>
                            </div>
                            <div class="col-md-7">
                                <label for="apellidos" class="form-label fw-bold">Apellidos:</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Ej: Juan Pérez" required>
                            </div>
                        </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Correo Electrónico:</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Ej: juan.perez@example.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label fw-bold">Teléfono:</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej: +34 600 123 456" required>
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label fw-bold">Dirección Completa:</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ej: Calle Mayor, 123, Madrid" required>
                            </div>

                            <div class="mb-3">
                                <label for="codigo_postal" class="form-label fw-bold">Código Postal:</label>
                                <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" placeholder="Ej: 28080" required>
                            </div>

                            <div class="mb-3">
                                <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento:</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                            </div>

                            <div class="mb-3">
                                <label for="genero" class="form-label fw-bold">Género:</label>
                                <select id="genero" name="genero" class="form-select" required>
                                    <option value="" disabled selected>Seleccione su género</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="femenino">Femenino</option>
                                </select>
                            </div>

                            <h3 class="fs-5 mb-3 fw-bold">Método de Pago:</h3>
                            <div class="form-check mb-4">
                                <input type="radio" class="form-check-input" id="paypal" name="metodo_pago" value="paypal" checked>
                                <label class="form-check-label" for="paypal">PayPal</label>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Proceder con el Pago</button>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4">
                    <div class="summary-box">
                        <h3 class="fw-bold mb-3">RESUMEN DEL PEDIDO</h3>
                        <p id="summary-text">Sus opciones aparecen aquí en un práctico resumen. Elige tu club para iniciar tu registro.</p>

                        <div id="planes-resumen">
                            <h4 id="plan-sel" class="fw-bold" style="display:none">Planes seleccionados:</h4>
                            <ul id="planes-list" class="list-unstyled"></ul>
                        </div>

                        <div id="extras-resumen" class="mt-3">
                            <h4 id="extra-sel" class="fw-bold" style="display:none">Extras seleccionados:</h4>
                            <ul id="extras-list" class="list-unstyled"></ul>
                        </div>

                        <h4 id="total" class="fw-bold mt-3" style="display:none">Total: €0.00</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require 'partials/chatbot.php'; ?>
    <?php include 'partials/footer.view.php'; ?>
</body>
</html>
