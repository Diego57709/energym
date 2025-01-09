<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>ChatBot</h1>
    <input type="text" name="text" id="text">
    <br><br>
    <button onClick="generateResponse();">Generar respuesta</button>
    <br><br>
    <div id="response"></div>
</body>
<script>
    function generateResponse() {
    var text = document.getElementById("text");
    var response = document.getElementById("response");  // Fixed the typo 'documen'

    fetch("response.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            text: text.value,
        }),
    })
    .then((res) => res.text())  // Fixed 'res=res.text()' to a valid arrow function
    .then((res) => {
        response.innerHTML = res;
    })
    .catch((error) => {
        response.innerHTML = `<span style='color: red;'>Error: ${error.message}</span>`;
        console.error("Error:", error);
    });
}

</script>
</html>
