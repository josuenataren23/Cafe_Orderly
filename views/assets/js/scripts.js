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