<?php
class WhatsAppSender {
    private $phoneNumberId;
    private $accessToken;
    private $apiUrl;

    public function __construct() {
        // Obtener variables de entorno
        $this->phoneNumberId = $_SESSION["WHATSAPP"]["PHONE_NUMBER_ID"];
        $this->accessToken = $_SESSION["WHATSAPP"]["ACCESS_TOKEN"];
    
        // 🔍 DEBUG: Mostrar los valores en el log
        error_log("📞 PHONE_NUMBER_ID sin modificar: " . $this->phoneNumberId);
        error_log("🔑 ACCESS_TOKEN sin modificar: " . $this->accessToken);
    
        // Verificar que las variables de entorno estén configuradas
        if (!$this->phoneNumberId || !$this->accessToken) {
            error_log("❌ ERROR: Falta PHONE_NUMBER_ID o ACCESS_TOKEN en las variables de entorno.");
            throw new Exception("❌ ERROR: PHONE_NUMBER_ID o ACCESS_TOKEN no están configurados.");
        }
    
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


