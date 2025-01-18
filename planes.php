<?php require 'partials/header2.view.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Plan de Suscripción</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <!-- SDK de PayPal (SANDBOX) -->
  <script src="https://www.paypal.com/sdk/js?client-id=AbRZMXJlSsa4gssluoNXdC1mq5DMl7tU-GBK_yHfAyEimULW-WzWLzPeDRpUGp-NrHcojhsQf0SNL8kX&currency=EUR"></script>

  <style>
    html, body {height: 100%; margin: 0; padding: 0;}
    body {display: flex; flex-direction: column;}
    .main {flex: 1; display: flex; align-items: center; justify-content: center;}
    .option-selected input[type="radio"], .option-selected input[type="checkbox"] {display: none;}
    .option-selected label {
      display: inline-block; padding: 10px 20px; background-color: #0f8b8d; color: #fff;
      border: 2px solid #0f8b8d; font-weight: bold; cursor: pointer;
      transition: background-color 0.3s, color 0.3s, transform 0.3s; text-align: center;
      border-radius: 5px; width: 100%;
    }
    .option-selected label:hover {background-color: #0b7375; border-color: #0b7375;}
    .option-selected input[type="radio"]:checked + label,
    .option-selected input[type="checkbox"]:checked + label {
      background-color: #0b7375; border-color: #0b7375; transform: scale(1.05);
    }
    button {margin-top: 15px;}
    .card {border: none; border-radius: 8px; transition: box-shadow 0.3s;}
    .card:hover {box-shadow: 0 4px 12px rgba(0,0,0,0.1);}
    .card .badge {border-radius: 5px; font-size: 0.8rem;}
    .fw-bold {font-weight: 600 !important;}
    .summary-box {
      background: #fff; border-radius: 8px; padding: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    #summary-text {font-size: 0.9rem; color: #6c757d;}
    .step-header {font-size: 1.25rem; margin-bottom: 15px; color: #0f8b8d; font-weight: 700;}
    .option-list ul {padding-left: 20px;}
    .option-list li {margin-bottom: 5px;}
    .section-note {font-size: 0.9rem; color: #6c757d;}
    .form-section .form-label {font-weight: 500;}
    .btn-success {
      background-color: #17a2b8 !important; border-color: #17a2b8 !important;
    }
    .btn-success:hover {
      background-color: #138f9f !important; border-color: #138f9f !important;
    }
  </style>
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

          <form action="procesar_plan.php" method="post" class="form-section" id="form-suscripcion">
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
                <button type="button" class="btn btn-success w-100" onclick="verificarSeleccion()" id="cont1">Continuar</button>
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
                <button type="button" class="btn btn-success w-100" onclick="mostrarDatos()" id="cont2">Continuar</button>
              </div>
            </div>

            <!-- Sección DATOS -->
            <div id="datos-section" style="display:none;">
              <h2 class="step-header">DATOS DE PAGO</h2>
              <p class="section-note">Por favor, completa la siguiente información para procesar tu suscripción.</p>
              <input type="text" name="extrasSelected" id="extrasSelected" hidden>

              <div class="mb-3">
                <label for="dni" class="form-label fw-bold">DNI:</label>
                <input
                  type="text" class="form-control" id="dni" name="dni"
                  placeholder="Ej: X1234567A"
                  pattern="^[A-Za-z]?[0-9]{7,8}[A-Za-z]$"
                  title="El DNI debe contener opcionalmente una letra al principio, seguida de 7 u 8 dígitos y una letra al final (ej: X1234567A)"
                  required
                >
              </div>

              <div class="mb-3 row">
                <div class="col-md-5">
                  <label for="nombre" class="form-label fw-bold">Nombre:</label>
                  <input
                    type="text" class="form-control" id="nombre" name="nombre"
                    placeholder="Ej: Juan"
                    pattern="[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+"
                    title="Ingrese solo letras y espacios"
                    required
                  >
                </div>
                <div class="col-md-7">
                  <label for="apellidos" class="form-label fw-bold">Apellidos:</label>
                  <input
                    type="text" class="form-control" id="apellidos" name="apellidos"
                    placeholder="Ej: Pérez García"
                    pattern="[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+"
                    title="Ingrese solo letras y espacios"
                    required
                  >
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label fw-bold">Correo Electrónico:</label>
                <input
                  type="email" class="form-control" id="email" name="email"
                  placeholder="Ej: juan.perez@example.com"
                  required
                >
              </div>

              <div class="mb-3">
                <label for="telefono" class="form-label fw-bold">Teléfono:</label>
                <input
                  type="tel" class="form-control" id="telefono" name="telefono"
                  placeholder="Ej: +34 600 123 456"
                  pattern="^(\+?\d{1,3})?\s?\d{9,13}$"
                  title="Ingrese un número de teléfono válido. Ej: +34 600 123 456 o 600123456"
                  required
                >
              </div>

              <div class="mb-3">
                <label for="direccion" class="form-label fw-bold">Dirección Completa:</label>
                <input
                  type="text" class="form-control" id="direccion" name="direccion"
                  placeholder="Ej: Calle Mayor, 123, Madrid"
                  required
                >
              </div>

              <div class="mb-3">
                <label for="codigo_postal" class="form-label fw-bold">Código Postal:</label>
                <input
                  type="text" class="form-control" id="codigo_postal" name="codigo_postal"
                  placeholder="Ej: 28080"
                  pattern="^\d{5}$"
                  title="El código postal debe contener 5 dígitos"
                  required
                >
              </div>

              <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento:</label>
                <input
                  type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                  required
                >
              </div>

              <div class="mb-3">
                <label for="genero" class="form-label fw-bold">Género:</label>
                <select id="genero" name="genero" class="form-select" required>
                  <option value="" disabled selected>Seleccione su género</option>
                  <option value="masculino">Masculino</option>
                  <option value="femenino">Femenino</option>
                </select>
              </div>

              <!-- Botón para pasar a la sección de Pago -->
              <button type="button" class="btn btn-success w-100" onclick="mostrarPago()">
                Proceder al Pago
              </button>
            </div>

            <!-- Sección PAGO (aquí se muestra el botón PayPal) -->
            <div id="pago-section" style="display:none;">
              <h2 class="step-header">REALIZAR PAGO</h2>
              <p class="section-note">Verifica tu resumen y finaliza el pago con PayPal.</p>

              <!-- Contenedor donde se mostrará el botón de PayPal -->
              <div id="paypal-button-container" class="mb-4"></div>
            </div>
          </form>
        </div>

        <div class="col-lg-4">
          <div class="summary-box">
            <h3 class="fw-bold mb-3">RESUMEN DEL PEDIDO</h3>
            <p id="summary-text">
              Sus opciones aparecen aquí en un práctico resumen. Elige tu club para iniciar tu registro.
            </p>
            <div id="planes-resumen">
              <h4 id="plan-sel" class="fw-bold" style="display:none">Plan seleccionado:</h4>
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

  <script>
    // Referencias de elementos
    const planSection = document.getElementById("plan-section");
    const extrasSection = document.getElementById("extras-section");
    const datosSection = document.getElementById("datos-section");
    const pagoSection = document.getElementById("pago-section");

    const comfort = document.getElementById("comfort");
    const premium = document.getElementById("premium");
    const entrenador = document.getElementById("entrenador");
    const agua = document.getElementById("agua");

    const planSel = document.getElementById("plan-sel");
    const extraSel = document.getElementById("extra-sel");
    const summaryText = document.getElementById("summary-text");
    const extrasList = document.getElementById("extras-list");
    const planesList = document.getElementById("planes-list");
    const totalElement = document.getElementById("total");
    const extrasSelectedInput = document.getElementById("extrasSelected");

    // Variable global para almacenar el total y usarlo en PayPal
    let orderTotal = 0;

    function mostrarPlan() {
      planSection.style.display = "block";
      extrasSection.style.display = "none";
      datosSection.style.display = "none";
      pagoSection.style.display = "none";
    }

    function verificarSeleccion() {
      if (!comfort.checked && !premium.checked) {
        alert("Por favor, selecciona un plan antes de continuar.");
      } else {
        mostrarExtras();
      }
    }

    function mostrarExtras() {
      planSection.style.display = "none";
      extrasSection.style.display = "block";
      datosSection.style.display = "none";
      pagoSection.style.display = "none";
    }

    function mostrarDatos() {
      planSection.style.display = "none";
      extrasSection.style.display = "none";
      datosSection.style.display = "block";
      pagoSection.style.display = "none";
    }

    function mostrarPago() {
      // Podrías añadir aquí validaciones extra si quieres asegurarte
      // de que el usuario ha llenado todo correctamente.

      planSection.style.display = "none";
      extrasSection.style.display = "none";
      datosSection.style.display = "none";
      pagoSection.style.display = "block";
    }

    function actualizarResumen() {
      extrasList.innerHTML = "";
      planesList.innerHTML = "";
      summaryText.innerHTML = "";
      totalElement.style.display = "block";
      planSel.style.display = "none";
      extraSel.style.display = "none";

      let total = 0;
      let extrasSeleccionados = [];

      // Planes
      if (comfort.checked) {
        planesList.innerHTML += "<li>Comfort</li>";
        planSel.style.display = "block";
        total += 19.99;
      }
      if (premium.checked) {
        planesList.innerHTML += "<li>Premium</li>";
        planSel.style.display = "block";
        total += 25.99;
      }

      // Extras
      if (entrenador.checked) {
        extrasList.innerHTML += "<li>Entrenador Personal</li>";
        extraSel.style.display = "block";
        total += 9.99;
        extrasSeleccionados.push("Entrenador Personal");
      }
      if (agua.checked) {
        extrasList.innerHTML += "<li>Agua</li>";
        extraSel.style.display = "block";
        total += 3.00;
        extrasSeleccionados.push("Agua");
      }

      // Pasar extras al input hidden
      extrasSelectedInput.value = extrasSeleccionados.join(",");

      // Guardamos el total en variable global (para PayPal)
      orderTotal = total;

      // Mostrar total en pantalla
      totalElement.textContent = "Total: €" + total.toFixed(2);
    }

    // Integración con PayPal
    paypal.Buttons({
    createOrder: function(data, actions) {
        // Crear la orden de PayPal
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: orderTotal.toFixed(2) // Total de la compra
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        // Capturar el pago aprobado
        return actions.order.capture().then(function(details) {
            console.log(details); // Inspecciona los detalles para depurar

            // Determina el método de pago
            let metodoPago = 'PayPal'; // Asume PayPal como predeterminado
            if (details.payer && details.payer.payment_method) {
                metodoPago = details.payer.payment_method; // Si existe, usa el método exacto
            }

            // Agrega el campo oculto "metodo_pago" al formulario
            const metodoPagoInput = document.createElement('input');
            metodoPagoInput.type = 'hidden';
            metodoPagoInput.name = 'metodo_pago';
            metodoPagoInput.value = metodoPago; // Puede ser "PayPal" o "CREDIT_CARD"
            document.getElementById('form-suscripcion').appendChild(metodoPagoInput);

            // Enviar el formulario
            document.getElementById('form-suscripcion').submit();
        });
    },
    onError: function(err) {
        console.error('Error en el pago:', err);
        alert('Hubo un error con el pago. Por favor, inténtalo de nuevo.');
    }
}).render('#paypal-button-container');

  </script>

  <?php require 'partials/chatbot.php'; ?>
  <?php include 'partials/footer.view.php'; ?>
</body>
</html>
