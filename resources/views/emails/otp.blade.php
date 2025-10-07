<!-- resources/views/emails/otp.blade.php -->
<!doctype html>
<html><body>
  <p>Bonjour,</p>
  <p>Votre code de vérification est : <strong>{{ $code }}</strong></p>
  <p>Ce code expire dans 10 minutes.</p>
</body></html>
