<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concert Ticket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/d8cfbe84b9.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-5lQ8zPmpCF3FZK8qblHlH7HV2AAhVHEoHPKHT0zFfSrrpNXL1dJEdNyo3s0khTbP" crossorigin="anonymous"></script>
    <?php require 'assets/partials/login-check.php'; ?>
    <?php
        require 'assets/styles/admin.php';
        require 'assets/styles/admin-options.php';
    ?>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
        }
        .page-header {
            margin-bottom: 30px;
            text-align: center;
        }
        .page-header h1 {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 10px;
        }
        .page-header p {
            font-size: 1.1rem;
            color: #6c757d;
        }
        .promo-section {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .promo-section h2 {
            font-size: 1.75rem;
            color: #007bff;
            margin-bottom: 15px;
        }
        .promo-section p {
            font-size: 1rem;
            color: #333;
            line-height: 1.6;
        }
        .promo-section ul {
            padding-left: 20px;
        }
        .promo-section ul li {
            margin-bottom: 10px;
        }
        .promo-section img {
            max-width: 100%;
            height: auto;
            border-radius: 0.375rem;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .note {
            margin-top: 20px;
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
        }
        .footer {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .footer a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header"><br><br><br>
            <h1>Ticket Benefits</h1>
            <p>Manage your concerts and events with ease</p>
        </div>
        <div class="promo-section">
            <h2>T1 Eat, Refill, Enjoy, Get 100 Baht Back</h2>
            <p><strong>Enjoy at ThaiTicketMajor</strong></p>
            <p>Receive a 100 Baht cashback* when you purchase concert or event tickets of 100 Baht or more per sales slip at <a href="https://www.thaiticketmajor.com" target="_blank">www.thaiticketmajor.com</a> and ThaiTicketMajor outlets.</p>
            <p><strong>(Limited to the first 1,500 registered individuals per month / Limited to 100 Baht cashback per sales slip per main card account per month)</strong></p>
            <p><strong>Promotion Period:</strong> January 1, 2024 – December 31, 2024</p>
            <p><strong>Registration:</strong> Starts on the 1st of each month via UCHOOSE to receive cashback.</p>
            <img src="assets/img/CT1.png" alt="Promotional Image">
            <h3>Terms and Conditions:</h3>
            <p>This offer is valid for expenses charged to Central The 1 credit cards only, for full amount transactions at <a href="https://www.thaiticketmajor.com" target="_blank">www.thaiticketmajor.com</a> and ThaiTicketMajor, a comprehensive ticket distributor, at participating Stand Alone branches. Spend 100 Baht or more per sales slip to receive 100 Baht cashback (amounts from multiple sales slips cannot be combined).</p>
            <p>Offer is limited to the first 1,500 individuals who successfully register via the UCHOOSE app each month. Registration must be completed correctly and you must receive a confirmation message from the system before making a purchase for it to be valid. The system will calculate spending based on the date of successful registration each month. Registration must be done anew each month for participation in that month.</p>
            <p>Cashback is limited to 100 Baht per sales slip per main card account per month.</p>
            <p>Cashback will be credited to the main card account within 3 days after the transaction (for supplementary cards, cashback will be credited only to the main card account).</p>
            <p>If the cardholder does not receive or receives incorrect benefits, please contact the card service center within 90 days from the end of the promotion. Otherwise, it will be considered that the cardholder accepts the company’s action as correct.</p>
            <p>Cashback cannot be transferred, exchanged, or redeemed for cash.</p>
            <p>The company reserves the right to reclaim the cashback from the main card account (or charge the cashback from the credit card account) if there is a cancellation of the qualifying spending after the promotion ends, or if ThaiTicketMajor refunds the cardholder due to changes or cancellations of the concert or event, or if the cardholder denies the transaction or payment afterwards.</p>
            <p>The company relies on the information in its system. Cardholders should keep their evidence and sales slips for verification.</p>
            <p>Cashback is only granted to cardholders who maintain their membership status and have a good payment history up to the date of cashback credit.</p>
            <p>Terms and conditions are as specified by the company.</p>
            <p>Credit card services provided by General Card Services Co., Ltd.</p>
        </div>
    </div>
    <?php require 'assets/partials/footer.php'; ?>
</body>
</html>
