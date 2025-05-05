<?php
require_once("../../controlleur/functionpaiments.php");
<<<<<<< HEAD

=======
require_once 'C:\xampp\htdocs\islem\projetweb\model\config.php';
>>>>>>> 628366a (cruuud)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de paiement invalide.");
}

$paiment_id = (int)$_GET['id'];
<<<<<<< HEAD
$paiment = getPaimentById($paiment_id);

if (!$paiment) {
    die("Paiement introuvable.");
}
=======

$paiment = getPaiementById($paiment_id);




// Format the date properly (assuming it's stored in a standard format)
$formatted_date = date('d/m/Y', strtotime($paiment['Date']));

// Generate invoice number (using payment ID and timestamp)
$invoice_number = 'TUN-' . str_pad($paiment['ID'], 5, '0', STR_PAD_LEFT) . '-' . date('Ymd');
>>>>>>> 628366a (cruuud)
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <title>Facture Paiement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 60px;
        }
        .facture-container {
            border: 1px solid #ccc;
            padding: 20px;
            width: 600px;
            margin: auto;
        }
        h1 {
            text-align: center;
            color:rgb(88, 4, 4);
        }
        .ligne {
            margin: 15px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            margin-top: 40px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="facture-container">
        <h1>Facture de Paiement</h1>
        <div class="ligne"><strong>ID Paiement:</strong> <?= htmlspecialchars($paiment['ID']) ?></div>
        <div class="ligne"><strong>Date:</strong> <?= htmlspecialchars($paiment['Date']) ?></div>
        <div class="ligne"><strong>Type d'abonnement:</strong> <?= htmlspecialchars($paiment['Abonnement']) ?></div>
        <div class="ligne"><strong>Méthode de paiement:</strong> <?= htmlspecialchars($paiment['payment_method']) ?></div>
        <div class="ligne"><strong>ID Client:</strong> <?= htmlspecialchars($paiment['user_id']) ?></div>
        <div class="ligne total"><strong>Montant:</strong> <?= number_format($paiment['montant'], 2) ?> TND</div>
    </div>
=======
    <title>Facture Tunify #<?= $invoice_number ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: #8b0000;
            --secondary-color: #ff4500;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --border-color: #e0e0e0;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .page-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .invoice-container {
            width: 800px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }
        
        /* Header Section */
        .invoice-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .company-info {
            animation: slideInLeft 0.8s ease-out;
        }
        
        .logo {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .document-title {
            text-align: right;
            animation: slideInRight 0.8s ease-out;
        }
        
        .document-title h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .invoice-number {
            font-size: 16px;
            opacity: 0.9;
        }
        
        /* Invoice Details */
        .invoice-body {
            padding: 40px;
        }
        
        .client-details {
            margin-bottom: 30px;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }
        
        .section-title {
            font-size: 18px;
            color: var(--primary-color);
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .client-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .client-column {
            flex-basis: 48%;
        }
        
        .detail-row {
            margin-bottom: 8px;
        }
        
        .detail-label {
            color: #666;
            font-weight: 500;
        }
        
        .detail-value {
            font-weight: 600;
            color: #333;
        }
        
        /* Invoice Table */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }
        
        .invoice-table th {
            background-color: var(--light-gray);
            color: var(--dark-gray);
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        .invoice-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .item-description {
            font-weight: 500;
        }
        
        .text-right {
            text-align: right;
        }
        
        /* Summary */
        .invoice-summary {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }
        
        .summary-table {
            width: 40%;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .summary-row.total {
            font-weight: 700;
            font-size: 20px;
            color: var(--primary-color);
            border-top: 2px solid var(--primary-color);
            border-bottom: none;
            padding-top: 12px;
        }
        
        /* Footer */
        .invoice-footer {
            background-color: var(--light-gray);
            padding: 20px 40px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-top: 1px solid var(--border-color);
            animation: fadeInUp 0.8s ease-out 0.8s both;
        }
        
        .thank-you {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        /* Actions */
        .invoice-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            background-color: white;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            animation: fadeIn 1s ease-out;
        }
        
        .action-button {
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 50px;
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .action-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(139, 0, 0, 0.3);
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from { 
                opacity: 0;
                transform: translateX(-30px);
            }
            to { 
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from { 
                opacity: 0;
                transform: translateX(30px);
            }
            to { 
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Print styles */
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                width: 100%;
            }
            
            .invoice-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="invoice-container" id="invoice-content">
            <div class="invoice-header">
                <div class="company-info">
                    <div class="logo">TUNIFY</div>
                    <div class="company-details">
                        Tunisie<br>
                        +216 XX XXX XXX<br>
                        contact@tunify.com
                    </div>
                </div>
                
                <div class="document-title">
                    <h1>FACTURE</h1>
                    <div class="invoice-number"><?= htmlspecialchars($invoice_number) ?></div>
                </div>
            </div>
            
            <div class="invoice-body">
                <div class="client-row">
                    <div class="client-column">
                        <div class="section-title">Informations Client</div>
                        <div class="detail-row">
                            <span class="detail-label">ID Client:</span>
                            <span class="detail-value"><?= htmlspecialchars($paiment['user_id']) ?></span>
                        </div>
                    </div>
                    
                    <div class="client-column">
                        <div class="section-title">Détails de paiement</div>
                        <div class="detail-row">
                            <span class="detail-label">Date:</span>
                            <span class="detail-value"><?= htmlspecialchars($formatted_date) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Méthode:</span>
                            <span class="detail-value"><?= htmlspecialchars($paiment['payment_method']) ?></span>
                        </div>
                        <!-- ▼ -->
<div class="detail-row">
 
   
</div>
                        <div class="detail-row">
                            <span class="detail-label">ID Transaction:</span>
                            <span class="detail-value"><?= htmlspecialchars($paiment['ID']) ?></span>
                        </div>
                    </div>
                </div>
                
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th width="60%">Description</th>
                            <th width="15%">Durée</th>
                            <th width="25%" class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="item-description">
                                Abonnement Tunify - <?= htmlspecialchars($paiment['Abonnement']) ?>
                            </td>
                            <td>1 mois</td>
                            <td class="text-right"><?= number_format($paiment['montant'], 2) ?> TND</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="invoice-summary">
                    <div class="summary-table">
                        <div class="summary-row total">
                            <div>Total</div>
                            <div><?= number_format($paiment['montant'], 2) ?> TND</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="invoice-footer">
                <div class="thank-you">Merci pour votre confiance !</div>
                <p>Pour toute question concernant cette facture, veuillez nous contacter à support@tunify.com</p>
                <p>&copy; 2025 Tunify, Tous droits réservés.</p>
            </div>
        </div>
        
        <div class="invoice-actions">
            <button class="action-button" id="print-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                </svg>
                Imprimer
            </button>
            <button class="action-button" id="download-pdf">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg>
                Télécharger PDF
            </button>
        </div>
    </div>

    <script>
        // Print functionality
        document.getElementById('print-button').addEventListener('click', function() {
            window.print();
        });
        
        // PDF download functionality
        document.getElementById('download-pdf').addEventListener('click', function() {
            const element = document.getElementById('invoice-content');
            const opt = {
                margin: 10,
                filename: 'facture-tunify-<?= $invoice_number ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            // Generate PDF
            html2pdf().from(element).set(opt).save();
        });
        
        // Add smooth entrance animations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = "1";
        });
    </script>
>>>>>>> 628366a (cruuud)
</body>
</html>