<?php
class WhatsAppSender {
    private $phoneNumberId;
    private $accessToken;
    private $apiUrl;

    public function __construct() {
        // Obtener variables de entorno
        // Registrar en error_log para verificar si las variables se están obteniendo correctamente
        error_log("🔍 DEBUG: Iniciando WhatsAppSender...");

        if (!isset($_SESSION["WHATSAPP"]["PHONE_NUMBER_ID"]) || !isset($_SESSION["WHATSAPP"]["ACCESS_TOKEN"])) {
            error_log("❌ ERROR: Las variables de sesión no están definidas.");
            error_log("📌 SESSION CONTENT: " . print_r($_SESSION, true));
            throw new Exception("❌ ERROR: PHONE_NUMBER_ID o ACCESS_TOKEN no están configurados.");
        }

        $this->phoneNumberId = $_SESSION["WHATSAPP"]["PHONE_NUMBER_ID"];
        $this->accessToken = $_SESSION["WHATSAPP"]["ACCESS_TOKEN"];

        


        // Ocultar parte del token por seguridad
        $maskedToken = substr($this->accessToken, 0, 4) . '...' . substr($this->accessToken, -4);
        $maskedPhoneId = substr($this->phoneNumberId, 0, 2) . '...' . substr($this->phoneNumberId, -2);

        error_log("📞 PHONE_NUMBER_ID: {$maskedPhoneId}");
        error_log("🔑 ACCESS_TOKEN: {$maskedToken}");

        // Construir la URL de la API de WhatsApp
        $this->apiUrl = "https://graph.facebook.com/v18.0/{$this->phoneNumberId}/messages";
    }

    public function sendMessage($to, $message) {
        $data = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $message]
        ];

        $headers = [
            "Authorization: Bearer {$this->accessToken}",
            "Content-Type: application/json"
        ];

        // Iniciar la solicitud cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        error_log("📩 Enviando mensaje a {$to} -> {$message}");
        error_log("📝 Respuesta HTTP {$httpCode}: {$response}");

        $decodedResponse = json_decode($response, true);

        if ($httpCode !== 200) {
            error_log("❌ ERROR WhatsApp API: " . json_encode($decodedResponse, JSON_PRETTY_PRINT));
            return [
                'success' => false,
                'http_code' => $httpCode,
                'error' => $decodedResponse['error'] ?? 'Error desconocido'
            ];
        }

        return [
            'success' => true,
            'response' => $decodedResponse
        ];
    }
}


