<?php
function getEmailTemplate($title, $message, $buttonText = null, $buttonLink = null) {
    return '
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>' . htmlspecialchars($title) . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
            }
            .email-container {
                max-width: 600px;
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            }
            .header {
                background-color: #007bff;
                color: white;
                padding: 10px;
                text-align: center;
                font-size: 18px;
                font-weight: bold;
                border-radius: 8px 8px 0 0;
            }
            .content {
                padding: 20px;
                font-size: 16px;
                color: #333;
            }
            .footer {
                margin-top: 20px;
                text-align: center;
                font-size: 14px;
                color: #666;
            }
            .btn {
                display: inline-block;
                margin-top: 20px;
                padding: 10px 15px;
                background-color: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-size: 16px;
            }
            .btn:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">' . htmlspecialchars($title) . '</div>
            <div class="content">
                <p>' . nl2br(htmlspecialchars($message)) . '</p>'
                . ($buttonText && $buttonLink ? '<a href="' . htmlspecialchars($buttonLink) . '" class="btn">' . htmlspecialchars($buttonText) . '</a>' : '') . '
            </div>
            <div class="footer">
                Cet e-mail est généré automatiquement, merci de ne pas y répondre.<br>
                &copy; 2025 Gestion Notes de Frais
            </div>
        </div>
    </body>
    </html>';
}
?>
