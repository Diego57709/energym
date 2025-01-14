<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
        }
        .card {
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            background: #ffffff;
        }
        h1 {
            font-size: 34px;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 10px;
        }
        p.text-muted {
            margin-bottom: 20px;
        }
        #reader-container {
            position: relative;
            width: 100%;
            max-width: 600px;
            height: 400px;
            margin: 20px auto;
            border-radius: 12px;
            overflow: hidden;
            background-color: #000;
        }
        #reader {
            width: 100%;
            height: 100%;
        }
        .scan-box {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
            width: 220px;
            height: 220px;
            border: 4px dotted #28a745;
            border-radius: 12px;
            pointer-events: none;
            box-shadow: 0 0 15px rgba(40, 167, 69, 0.5);
            background: transparent;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <?php include 'partials/header1.view.php'; ?>
    </header>

    <div class="container">
        <div class="card">
            <h1 class="text-center">QR Code Scanner | Admin</h1>
            <p class="text-muted text-center">Utiliza la cámara para escanear códigos QR de acceso</p>

            <!-- Success/Error Message -->
            <?php
            if (isset($_GET['status']) && isset($_GET['message'])):
                $statusClass = ($_GET['status'] === 'success') ? 'alert-success' : 'alert-danger';
                $message = htmlspecialchars($_GET['message']);
            ?>
                <div class="alert <?php echo $statusClass; ?> text-center">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Camera Container -->
            <div id="reader-container">
                <div id="reader"></div>
                <div class="scan-box"></div>
            </div>

            <!-- Buttons to Start/Stop Camera -->
            <div class="btn-group mt-3">
                <button id="start-btn" class="btn btn-primary" onclick="startScanner()">Iniciar Cámara</button>
                <button id="stop-btn" class="btn btn-danger" onclick="stopScanner()" disabled>Detener Cámara</button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let html5QrCode;
        let isScanning = false;

        function startScanner() {
            const startBtn = document.getElementById('start-btn');
            const stopBtn = document.getElementById('stop-btn');

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            const qrCodeSuccessCallback = (decodedText) => {
                stopScanner();
                window.location.href = `qr_verificacion_e.php?token=${encodeURIComponent(decodedText)}`;
            };

            const config = { fps: 10, qrbox: { width: 220, height: 220 } };
            html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
                .then(() => {
                    isScanning = true;
                    startBtn.disabled = true;
                    stopBtn.disabled = false;
                })
                .catch(err => {
                    console.error("Unable to start scanner:", err);
                    alert("Error al acceder a la cámara. Verifica los permisos.");
                });
        }

        function stopScanner() {
            const startBtn = document.getElementById('start-btn');
            const stopBtn = document.getElementById('stop-btn');

            if (html5QrCode && isScanning) {
                html5QrCode.stop()
                    .then(() => {
                        isScanning = false;
                        startBtn.disabled = false;
                        stopBtn.disabled = true;
                    })
                    .catch(err => console.error("Failed to stop scanner:", err));
            }
        }
    </script>

    <!-- Footer -->
    <footer>
        <?php include 'partials/footer.view.php'; ?>
    </footer>

</body>
</html>
