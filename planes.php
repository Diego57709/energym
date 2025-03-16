<?php require 'partials/header1.view.php'; ?>
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
    /* Estilo del preloader */
    #preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #ffffff;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
    .spinner {
      border: 16px solid #f3f3f3; /* Gris claro */
      border-top: 16px solid #0f8b8d; /* Color principal */
      border-radius: 50%;
      width: 120px;
      height: 120px;
      animation: spin 2s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .loading-text {
      margin-top: 20px;
      font-size: 1.2rem;
      color: #0f8b8d;
      font-weight: bold;
    }

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

    .option-selected input[type="radio"],
    .option-selected input[type="checkbox"] {
      display: none;
    }
    .option-selected label {
      display: inline-block;
      padding: 10px 20px;
      background-color: #004c5c;
      color: #ffffff;
      border: 2px solid #004c5c;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s, color 0.3s, transform 0.3s;
      text-align: center;
      border-radius: 5px;
      width: 100%;
    }
    .option-selected label:hover {
      background-color: #003b47;
      border-color: #003b47;
    }
    .option-selected input[type="radio"]:checked + label,
    .option-selected input[type="checkbox"]:checked + label {
      background-color: #002a34;
      border-color: #002a34;
      transform: scale(1.05);
    }
    button {
      margin-top: 15px;
    }
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
    .badge.bg-info {
      background-color: #005f73 !important;
      color: #ffffff !important;
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
    .step-header {
      font-size: 1.25rem;
      margin-bottom: 15px;
      color: #0f8b8d;
      font-weight: 700;
    }
    .section-note {
      font-size: 0.9rem;
      color: #6c757d;
    }
    .form-section .form-label {
      font-weight: 500;
    }
    .btn-success {
      background-color: #005f73 !important;
      border-color: #005f73 !important;
      color: #ffffff !important;
    }
    .btn-success:hover {
      background-color: #003f5c !important;
      border-color: #003f5c !important;
    }
    header, footer {
      position: relative;
      z-index: 10000;
    }
    .fixed-table {
      table-layout: fixed;
      width: 100%;
    }
    .fixed-table th,
    .fixed-table td {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
  </style>
</head>

<body>
  <!-- Preloader -->
  <div id="preloader" role="status" aria-label="Cargando la página">
    <div class="spinner"></div>
    <p class="loading-text" aria-live="polite">Cargando...</p>
  </div>
  
  <div class="main d-flex justify-content-center py-4">
    <div class="container bg-white rounded shadow-sm p-4">
      <div class="row">
        <!-- Columna del Formulario -->
        <div class="col-lg-8 mb-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <p class="mb-0 fw-bold">EnerGym</p>
              <p class="mb-0 text-muted" style="font-size:0.9rem;">C/ José María Roquero 4</p>
            </div>
            <a href="javascript:void(0);"
               class="text-decoration-none text-info fw-bold"
               onclick="mostrarPlan()"
               style="opacity: 1; color: #003b47 !important;"
               role="button"
               aria-label="Volver al plan">
              Atrás
            </a>
          </div>
          
          <form action="procesar_plan.php"
                method="post"
                class="form-section"
                id="form-suscripcion"
                aria-labelledby="tituloForm"
                novalidate>
            
            <!-- Sección del PLAN -->
            <div id="plan-section"
                 aria-label="Sección de elección de plan"
                 role="region">
              <h2 class="step-header" id="tituloForm">PLAN</h2>
              <p class="section-note">Escoge la suscripción que mejor se adapte a tus necesidades.</p>
              
              <div class="row g-3">
                <!-- Plan Comfort -->
                <div class="col-md-6">
                  <div class="card h-100 p-3">
                    <div class="card-body d-flex flex-column">
                      <h3 class="card-title fs-5 fw-bold text-dark">COMFORT</h3>
                      <p class="text-decoration-line-through text-muted mb-0">€24,99</p>
                      <p class="fw-bold text-info fs-5" style="opacity: 1; color: #003b47 !important;">€19,99</p>
                      <ul class="list-unstyled mb-3 option-list">
                        <li>✔️ Reserva con 36h de antelación 1 clase</li>
                        <li>✔️ Planes de entrenamiento en tu app EnerGym</li>
                        <li>✔️ YONGO Sports Water ahora por solo ¡3,90€!</li>
                      </ul>
                      <p class="text-muted mb-2">€0.00 cuota de inscripción</p>
                      <div class="text-center mt-auto option-selected">
                        <input type="radio"
                               id="comfort"
                               name="plan"
                               value="1"
                               onchange="actualizarResumen()"
                               aria-required="true">
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
                      <p class="fw-bold text-info fs-5" style="opacity: 1; color: #003b47 !important;">€25,99</p>
                      <ul class="list-unstyled mb-3 option-list">
                        <li>✔️ Todo desde Plan Comfort, y</li>
                        <li>✔️ Reserva con 48h de antelación ¡DOS CLASES!</li>
                        <li>✔️ YONGO Sports Water ahora por solo ¡1,90€!</li>
                        <li>✔️ IA para asesoramientos de entrenamiento/nutrición</li>
                      </ul>
                      <p class="text-muted mb-2">€0,00 cuota de inscripción</p>
                      <div class="text-center mt-auto option-selected">
                        <input type="radio"
                               id="premium"
                               name="plan"
                               value="2"
                               onchange="actualizarResumen()"
                               aria-required="true">
                        <label for="premium">Elegir Premium</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mt-4">
                <button type="button"
                        class="btn btn-success w-100"
                        onclick="verificarSeleccion()"
                        id="cont1"
                        aria-label="Continuar con el plan">
                  Continuar
                </button>
              </div>
            </div>
            
            <!-- Sección de EXTRAS -->
            <div id="extras-section"
                 style="display:none;"
                 aria-label="Sección de extras"
                 role="region">
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
                      <p class="fw-bold text-info fs-5" style="opacity: 1; color: #003b47 !important;">€9,99 / sesión</p>
                      <div class="text-center mt-auto option-selected">
                        <input type="checkbox"
                               id="entrenador"
                               name="extra"
                               value="Entrenador Personal"
                               onchange="actualizarResumen()"
                               aria-label="Seleccionar entrenador personal">
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
                      <p class="fw-bold text-info fs-5" style="opacity: 1; color: #003b47 !important;">€3,00</p>
                      <div class="text-center mt-auto option-selected">
                        <input type="checkbox"
                               id="agua"
                               name="extra"
                               value="Agua"
                               onchange="actualizarResumen()"
                               aria-label="Seleccionar agua de pago">
                        <label for="agua">Elegir Agua de Pago</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mt-4">
                <button type="button"
                        class="btn btn-success w-100"
                        onclick="mostrarDatos()"
                        id="cont2"
                        aria-label="Continuar con extras">
                  Continuar
                </button>
              </div>
            </div>
            
            <!-- Sección DATOS -->
            <div id="datos-section"
                 style="display:none;"
                 aria-label="Sección de datos de pago"
                 role="region">
              <h2 class="step-header">DATOS DE PAGO</h2>
              <p class="section-note">Completa la siguiente información para procesar tu suscripción.</p>
              
              <input type="text" name="extrasSelected" id="extrasSelected" hidden>

              <div class="mb-3">
                <label for="dni" class="form-label fw-bold">DNI:</label>
                <input type="text"
                       class="form-control"
                       id="dni"
                       name="dni"
                       placeholder="Ej: X1234567A"
                       pattern="^[A-Za-z]?[0-9]{7,8}[A-Za-z]?$"
                       title="Puede incluir letra opcional al inicio, 7 u 8 dígitos y una letra al final"
                       aria-required="true"
                       required>
              </div>

              <div class="mb-3 row">
                <div class="col-md-5">
                  <label for="nombre" class="form-label fw-bold">Nombre:</label>
                  <input type="text"
                         class="form-control"
                         id="nombre"
                         name="nombre"
                         placeholder="Ej: Juan"
                         pattern="[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+"
                         title="Solo letras y espacios"
                         aria-required="true"
                         required>
                </div>
                <div class="col-md-7">
                  <label for="apellidos" class="form-label fw-bold">Apellidos:</label>
                  <input type="text"
                         class="form-control"
                         id="apellidos"
                         name="apellidos"
                         placeholder="Ej: Pérez García"
                         pattern="[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+"
                         title="Solo letras y espacios"
                         aria-required="true"
                         required>
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label fw-bold">Correo Electrónico:</label>
                <input type="email"
                       class="form-control"
                       id="email"
                       name="email"
                       placeholder="Ej: juan.perez@example.com"
                       aria-required="true"
                       required>
              </div>

              <div class="mb-3">
                <label for="telefono" class="form-label fw-bold">Teléfono:</label>
                <input type="tel"
                       class="form-control"
                       id="telefono"
                       name="telefono"
                       placeholder="Ej: +34 600 123 456"
                       pattern="^\+?\d{1,3}[\s-]?\d{3}[\s-]?\d{2,3}[\s-]?\d{2,3}$"
                       title="Ej: +34 600 123 456 o 600123456"
                       aria-required="true"
                       required>
              </div>

              <div class="mb-3">
                <label for="direccion" class="form-label fw-bold">Dirección Completa:</label>
                <input type="text"
                       class="form-control"
                       id="direccion"
                       name="direccion"
                       placeholder="Ej: Calle Mayor, 123, Madrid"
                       aria-required="true"
                       required>
              </div>

              <div class="mb-3">
                <label for="codigo_postal" class="form-label fw-bold">Código Postal:</label>
                <input type="text"
                       class="form-control"
                       id="codigo_postal"
                       name="codigo_postal"
                       placeholder="Ej: 28080"
                       pattern="^\d{5}"
                       title="5 dígitos para el código postal"
                       aria-required="true"
                       required>
              </div>

              <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento:</label>
                <input type="date"
                       class="form-control"
                       id="fecha_nacimiento"
                       name="fecha_nacimiento"
                       aria-required="true"
                       required>
              </div>

              <div class="mb-3">
                <label for="genero" class="form-label fw-bold">Género:</label>
                <select id="genero" name="genero" class="form-select" aria-required="true" required>
                  <option value="" disabled selected>Seleccione su género</option>
                  <option value="masculino">Masculino</option>
                  <option value="femenino">Femenino</option>
                </select>
              </div>

              <!-- Botón para pasar a la sección de Pago -->
              <button type="button"
                      class="btn btn-success w-100"
                      onclick="mostrarPago()"
                      aria-label="Proceder al pago">
                Proceder al Pago
              </button>
            </div>
            
            <!-- Sección PAGO (PayPal Button) -->
            <div id="pago-section"
                 style="display:none;"
                 aria-label="Sección de pago final"
                 role="region">
              <h2 class="step-header">REALIZAR PAGO</h2>
              <p class="section-note">Verifica tu resumen y finaliza el pago con PayPal.</p>
              <div id="paypal-button-container" class="mb-4"></div>
            </div>
          </form>
        </div>
        
        <!-- Resumen del Pedido -->
        <div class="col-lg-4">
          <div class="summary-box" role="region" aria-label="Resumen del Pedido">
            <h3 class="fw-bold mb-3">RESUMEN DEL PEDIDO</h3>
            <table class="table table-striped fixed-table"
                   aria-describedby="resumenDescript"
                   role="table">
              <colgroup>
                <col style="width: 30%;">
                <col style="width: 50%;">
                <col style="width: 20%;">
              </colgroup>
              <thead>
                <tr role="row">
                  <th role="columnheader" scope="col">Concepto</th>
                  <th role="columnheader" scope="col">Detalle</th>
                  <th role="columnheader" scope="col" class="text-end">Precio</th>
                </tr>
              </thead>
              <tbody id="summary-body" role="rowgroup">
                <tr id="plan-summary" style="display:none;" role="row">
                  <td role="cell">Plan</td>
                  <td id="plan-detail" role="cell"></td>
                  <td id="plan-price" role="cell" class="text-end"></td>
                </tr>
                <!-- Filas de extras se añaden dinámicamente -->
                <tr id="total-summary" style="display:none;" role="row">
                  <td role="cell" colspan="2" class="text-end fw-bold">Total</td>
                  <td id="total-price" role="cell" class="fw-bold text-end"></td>
                </tr>
              </tbody>
            </table>
            <p id="resumenDescript" class="visually-hidden">
              Aquí se muestra un resumen del plan y los extras seleccionados, así como el total.
            </p>
          </div>
        </div>
        <!-- Fin Resumen -->
      </div>
    </div>
  </div>
  
  <script>
    window.addEventListener('load', function(){
      const preloader = document.getElementById('preloader');
      if(preloader){
        preloader.style.display = 'none';
      }
    });
    
    // Secciones del formulario
    const planSection = document.getElementById('plan-section');
    const extrasSection = document.getElementById('extras-section');
    const datosSection = document.getElementById('datos-section');
    const pagoSection = document.getElementById('pago-section');
    
    // Opciones de plan y extras
    const comfort = document.getElementById('comfort');
    const premium = document.getElementById('premium');
    const entrenador = document.getElementById('entrenador');
    const agua = document.getElementById('agua');
    
    // Variable global para el total (usada con PayPal)
    let orderTotal = 0;
    
    // Muestra la sección de PLAN
    function mostrarPlan() {
      planSection.style.display = 'block';
      extrasSection.style.display = 'none';
      datosSection.style.display = 'none';
      pagoSection.style.display = 'none';
    }
    
    // Verifica que se haya seleccionado un plan
    function verificarSeleccion() {
      if (!comfort.checked && !premium.checked) {
        alert('Por favor, selecciona un plan antes de continuar.');
        return;
      }
      // Si se seleccionó un plan, mostramos sección Extras
      mostrarExtras();
    }
    
    // Muestra la sección de EXTRAS
    function mostrarExtras() {
      planSection.style.display = 'none';
      extrasSection.style.display = 'block';
      datosSection.style.display = 'none';
      pagoSection.style.display = 'none';
    }
    
    // Muestra la sección de DATOS
    function mostrarDatos() {
      planSection.style.display = 'none';
      extrasSection.style.display = 'none';
      datosSection.style.display = 'block';
      pagoSection.style.display = 'none';
    }
    
    // Valida el formulario antes de mostrar el pago
    function mostrarPago() {
      const form = document.getElementById('form-suscripcion');
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }
      planSection.style.display = 'none';
      extrasSection.style.display = 'none';
      datosSection.style.display = 'none';
      pagoSection.style.display = 'block';
    }
    
    // Actualiza el resumen
    function actualizarResumen() {
      let plan = '';
      let planPrice = 0;
      if (comfort.checked) {
        plan = 'Comfort';
        planPrice = 19.99;
      } else if (premium.checked) {
        plan = 'Premium';
        planPrice = 25.99;
      }
      
      // Extras
      const extrasArray = [];
      if (entrenador.checked) {
        extrasArray.push({ name: 'Entrenador Personal', price: 9.99 });
      }
      if (agua.checked) {
        extrasArray.push({ name: 'Agua', price: 3.00 });
      }
      
      const extrasPrice = extrasArray.reduce((acc, item) => acc + item.price, 0);
      orderTotal = planPrice + extrasPrice;
      
      // Plan
      if (plan !== '') {
        document.getElementById('plan-summary').style.display = 'table-row';
        document.getElementById('plan-detail').textContent = plan;
        document.getElementById('plan-price').textContent = '€' + planPrice.toFixed(2);
      } else {
        document.getElementById('plan-summary').style.display = 'none';
      }
      
      // Limpia filas de extras antiguos
      const summaryBody = document.getElementById('summary-body');
      const oldExtraRows = summaryBody.querySelectorAll('tr.extra-row');
      oldExtraRows.forEach(row => row.remove());
      
      // Inserta filas nuevas para los extras
      const totalRow = document.getElementById('total-summary');
      extrasArray.forEach(extra => {
        const tr = document.createElement('tr');
        tr.className = 'extra-row';
        
        const tdConcept = document.createElement('td');
        tdConcept.textContent = 'Extra';
        
        const tdDetail = document.createElement('td');
        tdDetail.textContent = extra.name;
        
        const tdPrice = document.createElement('td');
        tdPrice.className = 'text-end';
        tdPrice.textContent = '€' + extra.price.toFixed(2);
        
        tr.appendChild(tdConcept);
        tr.appendChild(tdDetail);
        tr.appendChild(tdPrice);
        
        summaryBody.insertBefore(tr, totalRow);
      });
      
      // Muestra u oculta la fila de Total
      if (orderTotal > 0) {
        document.getElementById('total-summary').style.display = 'table-row';
        document.getElementById('total-price').textContent = '€' + orderTotal.toFixed(2);
      } else {
        document.getElementById('total-summary').style.display = 'none';
      }
    }
    
    // PayPal
    paypal.Buttons({
      createOrder: function(data, actions) {
        return actions.order.create({
          purchase_units: [{
            amount: { value: orderTotal.toFixed(2) }
          }]
        });
      },
      onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
          console.log(details);
          let metodoPago = 'PayPal';
          if (details.payer && details.payer.payment_method) {
            metodoPago = details.payer.payment_method;
          }
          const metodoPagoInput = document.createElement('input');
          metodoPagoInput.type = 'hidden';
          metodoPagoInput.name = 'metodo_pago';
          metodoPagoInput.value = metodoPago;
          document.getElementById('form-suscripcion').appendChild(metodoPagoInput);
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
