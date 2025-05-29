<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página en Mantenimiento</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .container {
            text-align: center;
            max-width: 600px;
        }

        h1 {
            font-size: 2.5rem;
            color: #444;
        }

        p {
            font-size: 1.2rem;
            margin: 20px 0;
            color: #666;
        }

        .timer {
            font-size: 1.5rem;
            color: #e74c3c;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Estamos en Mantenimiento</h1>
        <img src="https://visibilidadonline.es/wp-content/uploads/marketin-visibilidad-online.png">
        <!--<img src="data:image/png;base64,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" alt="Página en Mantenimiento" class="maintenance-image">-->
        <p>Gracias por su paciencia. Estamos realizando algunas mejoras en nuestro sitio y estaremos de regreso pronto.</p>
        <div class="timer">
            <!-- Se mostrará la cuenta regresiva aquí -->
            Tiempo estimado: <span id="countdown"></span>
        </div>
    </div>

    <script>
        // Temporizador de cuenta regresiva
        const targetDate = new Date("2025-04-16 18:00:00"); // Ajusta la fecha y hora objetivo
        const countdownElement = document.getElementById("countdown");

        function updateCountdown() {
            const now = new Date();
            const difference = targetDate - now;

            if (difference <= 0) {
                countdownElement.textContent = "¡Ya estamos de vuelta!";
                return;
            }

            const days = Math.floor(difference / (1000 * 60 * 60 * 24));
            const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((difference % (1000 * 60)) / 1000);

            countdownElement.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }

        // Actualizar cada segundo
        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</body>
</html>
