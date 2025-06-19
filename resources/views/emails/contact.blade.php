<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Nouveau message de contact - Educ-Map</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .field {
            margin-bottom: 15px;
        }

        .field-label {
            font-weight: bold;
            color: #495057;
        }

        .field-value {
            margin-top: 5px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 3px;
        }

        .message-content {
            white-space: pre-wrap;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Nouveau message de contact - Educ-Map</h2>
        <p>Un nouveau message a été reçu via le formulaire de contact du site Educ-Map.</p>
    </div>

    <div class="content">
        <div class="field">
            <div class="field-label">Nom :</div>
            <div class="field-value">{{ $name }}</div>
        </div>

        <div class="field">
            <div class="field-label">Email :</div>
            <div class="field-value">{{ $email }}</div>
        </div>

        @if(isset($phone) && $phone)
            <div class="field">
                <div class="field-label">Téléphone :</div>
                <div class="field-value">{{ $phone }}</div>
            </div>
        @endif

        @if(isset($organization) && $organization)
            <div class="field">
                <div class="field-label">Organisation :</div>
                <div class="field-value">{{ $organization }}</div>
            </div>
        @endif

        <div class="field">
            <div class="field-label">Sujet :</div>
            <div class="field-value">{{ $subject }}</div>
        </div>

        <div class="field">
            <div class="field-label">Message :</div>
            <div class="field-value message-content">{{ $message }}</div>
        </div>
    </div>

    <hr style="margin: 30px 0; border: none; border-top: 1px solid #dee2e6;">

    <p style="font-size: 12px; color: #6c757d; text-align: center;">
        Ce message a été envoyé depuis le site Educ-Map.<br>
        Pour répondre, vous pouvez utiliser l'adresse email fournie ci-dessus.
    </p>
</body>

</html>
