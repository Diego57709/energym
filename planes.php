<?php require 'partials/header2.view.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan de Suscripción</title>
    <style>
        /* Estilo general */
.main {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    color: #333;
}
.container {
    max-width: 1200px;
    width: 100%;
    display: flex;
    flex-direction: row;
    padding: 20px;
    gap: 20px;
}
.main-content, .summary {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
}
.main-content {flex: 3;}
.summary {flex: 1;}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.header a {
    color: #007bff;
    text-decoration: none;
}
#plan-section, #extras-section  {
    display: block;
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
}
#extras-section, #datos-section {display: none;}
.plan-section h2, .extras-section h2 {
    color: #0f8b8d;
    margin: 0;
    margin-bottom: 10px;
}
.plan-container {
    display: flex;
    gap: 20px;
}
.plan {
    display: flex;
    flex-direction: column;
    justify-content: space-between; 
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.plan p {font-size: 0.9em; margin: 10px 0; }
.plan h3 {font-size: 1.2em;}
.plan .price {
    font-size: 1.3em;
    color: #0f8b8d;
}
.plan .old-price {
    text-decoration: line-through;
    color: #777;
    font-size: 0.9em;
}
.plan .features { text-align: left; margin-top: 10px;}
.plan button {
    background-color: #800080;
    color: white;
    border: none;
    padding: 10px;
    font-size: 1em;
    border-radius: 5px;
    cursor: pointer;
}
.summary h3 {font-weight: bold; margin-bottom: 10px;}

.matricula-y-boton {
    margin-top: auto;
    padding-top: 10px;
    text-align: center;
}
.matricula {
    font-size: 0.9em;
    color: #777;
    margin-bottom: 10px;
}

/* opciones seleccionadas */
.option-selected input[type="radio"], .option-selected input[type="checkbox"]{
    display: none;
}

.option-selected label, button{
    display: inline-block;
    padding: 10px 20px;
    background-color: #0f8b8d;
    color: white;
    border: 2px solid #0f8b8d;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s, transform 0.3s;
}
button {margin-top: 15px;}

.option-selected input[type="radio"]:checked + label, .plan input[type="checkbox"]:checked + label {
    background-color: #0b7375;
    border-color: #0b7375;
    transform: scale(1.1);
}

/* precios */
.extras-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}
.extras-container .plan {
    flex: 1;
    max-width: 300px;
}
.extras-container .plan h2 {
    font-size: 1.2em;
    margin-bottom: 10px;
}
.extras-container .plan ul {
    list-style: none;
    padding: 0;
    margin-bottom: 10px;
    text-align: left;
}
.extras-container .plan .price {
    font-size: 1.3em;
    color: #0f8b8d;
    margin-bottom: 10px;
}
.datos-section {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: auto;
}
.datos-section h2 {
    color: #0f8b8d;
    margin-bottom: 15px;
}
.datos-container label {
    font-weight: bold;
    color: #333;
}
.datos-container input, .datos-container select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 100%;
    box-sizing: border-box;
}


/* pantallas más pequeñas */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
    }
    .plan-container {
        flex-direction: column;
    }
    .summary {
        margin-top: 20px;
    }
    .extras-container {
        flex-direction: column; /* Cambia a columna en pantallas pequeñas */
    }
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

            // Verificar si algún plan está seleccionado
            if (!planComfort.checked && !planPremium.checked) {
                alert("Por favor, selecciona un plan antes de continuar.");
            } else {
                // Si se ha seleccionado un plan, mostrar la sección de extras
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
            // Limpiar listas y calcular total
            const extrasList = document.getElementById('extras-list');
            const planesList = document.getElementById('planes-list');
            const totalElement = document.getElementById('total');
            var summaryText = document.getElementById('summary-text');
            document.getElementById("total").style.display = "block";
            extrasList.innerHTML = '';
            planesList.innerHTML = '';
            summaryText.innerHTML = '';
            let total = 0;

            // Obtener el plan seleccionado y agregar su precio al total
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

            // Obtener los extras seleccionados y agregar sus precios al total
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
            // Mostrar el total
            totalElement.textContent = 'Total: €' + total.toFixed(2);
        }
    </script>
</head>
<body>
    <div class="main"> 
        <div class="container">
            <div class="main-content">
                <div class="header">
                    <div>
                        <p><strong>EnerGym</strong></p>
                        <p>Paseo de las Delicias 32</p>
                    </div>
                    <a onclick="mostrarPlan()">Atras</a>
                </div>

                <div class="plan-section" id="plan-section">
                    <h2>PLAN</h2>
                    <p>Escoge la suscripción que mejor se adapte a tus necesidades.</p>
                    <div class="plan-container">
                        <!-- Plan Comfort -->
                        <div class="plan">
                            <h3>COMFORT</h3>
                            <p class="old-price">€24,99</p>
                            <p class="price">€19,99</p>
                            <div class="features">
                                <p>✔️ Reserva con 36h de antelación 1 clase</p>
                                <p>✔️ Planes de entrenamiento en tu app EnerGym</p>
                                <p>✔️ YONGO Sports Water ahora por solo ¡3,90€!</p>
                            </div>
                            <div class="matricula-y-boton">
                                <p class="matricula">€9,99 cuota de inscripción</p>
                                <span class="option-selected">
                                    <input type="radio" id="comfort" name="plan" value="Comfort" onchange="actualizarResumen()">
                                    <label for="comfort">Elegir Comfort</label>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Plan Premium -->
                        <div class="plan" style="border: 2px solid #4cb7ab;">
                            <div style="background-color: #4cb7ab; color: white; padding: 5px; border-radius: 5px;">
                                MEJOR VALORADA
                            </div>
                            <h3>PREMIUM</h3>
                            <p class="old-price">€29,99</p>
                            <p class="price">€25,99</p>
                            <div class="features">
                                <p>✔️ Todo desde Plan Comfort, y</p>
                                <p>✔️ Reserva con 48h de antelación ¡DOS CLASES!</p>
                                <p>✔️ YONGO Sports Water ahora por solo !1,90€! (precio oficial 4,90€)</p>
                                <p>✔️ Ven a entrenar con quien quieras de viernes a domingo</p>
                            </div>
                            <div class="matricula-y-boton">
                                <p class="matricula">€0,00 cuota de inscripción</p>
                                <span class="option-selected">
                                    <input type="radio" id="premium" name="plan" value="Premium" onchange="actualizarResumen()">
                                    <label for="premium">Elegir Premium</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Botón para mostrar la sección de extras -->
                <button onclick="verificarSeleccion()" id='cont1'>Continuar</button>
                </div>

                <!-- Sección de extras oculta inicialmente -->
                <div class="extras-section" id="extras-section">
                    <h2>EXTRAS</h2>
                    <p>Servicios adicionales.</p>
                    <!-- Contenido de extras aquí -->
                    <div class="extras-container">
                        <div class="plan">
                            <h2>Entrenador Personal</h2>
                            <ul>
                                <li>✔️ Asesoramiento personalizado</li>
                                <li>✔️ Sesiones privadas de entrenamiento</li>
                            </ul>
                            <p class="price">€9,99 / sesión</p>
                            <div class="matricula-y-boton">
                                <span class="option-selected">
                                <input type="checkbox" id="entrenador" name="extra" value="Entrenador Personal" onchange="actualizarResumen()">
                                    <label for="entrenador">Elegir Entrenador Personal</label>
                                </span>
                            </div>
                        </div>
                        <div class="plan">
                            <h2>Yongo</h2>
                            <ul>
                                <li>✔️ Botella de agua premium</li>
                                <li>✔️ Reposición ilimitada</li>
                            </ul>
                            <p class="price">€3,00</p>
                            <div class="matricula-y-boton">
                                <span class="option-selected">
                                <input type="checkbox" id="agua" name="extra" value="Agua" onchange="actualizarResumen()">
                                    <label for="agua">Elegir Agua de Pago</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Botón para mostrar la sección de extras -->
                <button onclick="mostrarDatos()" id='cont2'>Continuar</button>
                </div>

                <div class="datos-section" id="datos-section">
    <h2>DATOS DE PAGO</h2>
    <p>Por favor, completa la siguiente información para procesar tu pago.</p>
    
    <div class="datos-container">
        <form action="procesar_plan.php" method="post" style="display: flex; flex-direction: column; gap: 15px;">
            
            <!-- Nombre Completo -->
            <label for="nombre">Nombre Completo:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan Pérez" required>

            <!-- Email -->
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" placeholder="Ej: juan.perez@example.com" required>

            <!-- Teléfono -->
            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" placeholder="Ej: +34 600 123 456" required>

            <!-- Dirección Completa -->
            <label for="direccion">Dirección Completa:</label>
            <input type="text" id="direccion" name="direccion" placeholder="Ej: Calle Mayor, 123, Madrid" required>

            <!-- Código Postal -->
            <label for="codigo_postal">Código Postal:</label>
            <input type="text" id="codigo_postal" name="codigo_postal" placeholder="Ej: 28080" required>

            <!-- Fecha de Nacimiento -->
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

            <!-- Género -->
            <label for="genero">Género:</label>
            <select id="genero" name="genero" required>
                <option value="" disabled selected>Seleccione su género</option>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
                <option value="otro">Otro</option>
                <option value="prefiere_no_decir">Prefiero no decir</option>
            </select>

            <!-- Método de Pago -->
            <h3>Método de Pago:</h3>
            <label for="paypal">
                <input type="radio" id="paypal" name="metodo_pago" value="paypal" checked>
                PayPal
            </label>

            <!-- Botón de Enviar -->
            <button type="submit" style="background-color: #4cb7ab; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 1em; cursor: pointer;">
                Proceder con el Pago
            </button>
        </form>
    </div>
</div>
            </div>

            
            
            <div class="summary">
                <h3>RESUMEN DEL PEDIDO</h3>
                <p id="summary-text">Sus opciones aparecen aquí en un práctico resumen. Elige tu club para iniciar tu registro.</p>
                <div id="planes-resumen">
                    <h4 id="plan-sel" style="display:none">Planes seleccionados:</h4>
                    <ul id="planes-list"></ul> <!-- Lista para los planes -->
                </div>
                <div id="extras-resumen">
                    <h4 id="extra-sel" style="display:none">Extras seleccionados:</h4>
                    <ul id="extras-list"></ul> <!-- Lista para los extras -->
                </div>
                <h4 id="total" style="margin-top: 10px; font-weight: bold; display:none">Total: €0.00</h4>
                
            </div>
        </div>

        </div>
    </div>
</body>
</html>
