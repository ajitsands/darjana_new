<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail {
    public static function sendOrderConfirmation($orderData, $orderItems, $customerEmail) {
        $mail = new PHPMailer(true);

        try {
            // SMTP Settings (Configure these for live production)
            // $mail->isSMTP();
            // $mail->Host       = 'smtp.example.com'; 
            // $mail->SMTPAuth   = true;
            // $mail->Username   = 'your_email@example.com';
            // $mail->Password   = 'your_password';
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            // $mail->Port       = 465;

            // Using native PHP mail() for now
            $mail->isMail();

            // Sender and Recipients
            $mail->setFrom('orders@darjanafashion.com', 'Dar Jana Fashion');
            $mail->addAddress($customerEmail, $orderData['customer_name']);
            
            // CC to admin
            $mail->addCC('orders@darjanafashion.com');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Order Confirmation - Dar Jana Fashion (#' . $orderData['order_number'] . ')';

            // Generate HTML Body
            ob_start();
            $order = $orderData;
            $items = $orderItems;
            include __DIR__ . '/../app/Views/emails/order_confirmation.php';
            $htmlBody = ob_get_clean();

            $mail->Body = $htmlBody;

            // Plain text alternative
            $mail->AltBody = "Thank you for your order! Your order number is " . $orderData['order_number'] . ".\n\nTotal: " . number_format($orderData['total_amount'], 2) . " BHD";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
