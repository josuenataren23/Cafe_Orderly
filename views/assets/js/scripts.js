// Render a widget
const widgetId = turnstile.render("#container", {
  sitekey: "<YOUR-SITE-KEY>",
  callback: handleSuccess,
});

// Get the current token
const token = turnstile.getResponse(widgetId);

// Check if widget is expired
const isExpired = turnstile.isExpired(widgetId);

// Reset the widget (clears current state)
turnstile.reset(widgetId);

// Remove the widget completely
turnstile.remove(widgetId);




function onSignIn(googleUser) {
    var id_token = googleUser.getAuthResponse().id_token;

    // 🛑 Reemplaza esta ruta si tu sistema de ruteo es diferente, 
    // pero debe apuntar a AuthController::googleAuth()
    fetch('?controller=Auth&action=googleAuth', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'idtoken=' + id_token // Envío del token
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.redirect) {
            // Redirecciona al dashboard o inicio
            window.location.href = data.redirect; 
        } else {
            alert("Error al iniciar sesión: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error de conexión:', error);
        alert('Hubo un error de conexión con el servidor.');
    });
}