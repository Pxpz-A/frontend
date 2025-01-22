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
            font-family: 'Sarabun', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin-top: 20px;
        }
        .accordion-button {
            background-color: #007bff;
            border: 1px solid #007bff;
            color: #fff;
            font-weight: 600;
            border-radius: 0.375rem;
        }
        .accordion-button:not(.collapsed) {
            color: #fff;
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .accordion-body {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-top: 0;
            padding: 20px;
            color: #333;
        }
        .accordion-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .accordion-header {
            margin-bottom: 0;
        }
        .accordion-header button {
            border-radius: 0.375rem;
        }
        h2 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #343a40;
        }
       
        .note {
            margin-top: 20px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container"><br><br><br>
        <h2 class="text-center">Ticket Shield - Ticket Protection Service</h2>

        <div class="accordion" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Service Fees
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        A 7% fee is charged based on the price of the purchased tickets (including VAT).
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Ticket Shield Service Terms
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <strong>Coverage</strong>
                        <p>If the ticket holder is unable to attend the event or performance due to one of the following reasons that occur after the ticket has been purchased and within 30 days before the event or performance date:</p>
                        <ul>
                            <li>Death of the ticket holder or a family member within 30 days before the concert or event.</li>
                            <li>Severe injury from an accident or serious illness of the ticket holder or family member, requiring medical treatment and confirmation from a doctor that travel to attend the concert or event is not feasible.</li>
                            <li>Quarantine for disease control as advised by a doctor for the ticket holder or family member.</li>
                            <li>A car accident or emergency involving a personal vehicle used for traveling to the concert or event, requiring emergency assistance.</li>
                            <li>A delay or cancellation of public transportation schedules defined in the policy terms that could not be foreseen.</li>
                            <li>A subpoena to appear as a witness in court or any court summons received without prior knowledge before purchasing the tickets.</li>
                            <li>Necessary travel or relocation of the ticket holder for temporary or permanent work assignments due to employer orders.</li>
                            <li>Serious damage to the ticket holder's residence from fire, theft, or natural disasters within 7 days before the event, making it impossible to attend.</li>
                            <li>Severe natural disasters preventing travel to the event venue on time.</li>
                            <li>Changes in exam dates coinciding with the event date, or event dates falling one day before the exam date without prior knowledge of the change.</li>
                        </ul>
                        <p><strong>Note:</strong> "Family member" refers to parents, children, and/or legally married spouse of the ticket holder.</p>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Expense Reimbursement
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        Reimbursement for the ticket value as shown on the ticket for the concert or performance, excluding platform service fees, The Concert fees, and payment fees.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Ticket Refund Claims
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        In the case that the ticket holder cannot attend the event due to one of the reasons mentioned, a refund claim must be submitted within 30 days from the date of the incident. The ticket holder or their representative must submit the following documents to the company:
                        <ul>
                            <li>Refund claim form as specified by the company.</li>
                            <li>Unused concert or event tickets.</li>
                            <li>Copy of the ticket holder's ID card.</li>
                            <li>Death certificate, autopsy report, police daily log, and residence registration with a "deceased" stamp for the ticket holder or family member (in case of death).</li>
                            <li>Medical certificate detailing significant symptoms, diagnosis, and treatment (for injury, illness, or quarantine as advised by a doctor).</li>
                            <li>Local police daily log or official loss or damage report from relevant authorities (in case of road accidents or personal vehicle emergencies).</li>
                            <li>Copy of passport or travel documents and/or evidence of canceled or rescheduled public transport bookings (in case of schedule changes or cancellations).</li>
                            <li>Court summons (in case of receiving any court order).</li>
                            <li>Photographs and documents confirming residence damage (in case of residence damage).</li>
                            <li>Confirmation letter from the employer or the ticket holder’s company (in case of necessary travel or relocation due to employer orders).</li>
                            <li>Notification of exam date changes from the examining body, school, college, or university (in case of exam date changes).</li>
                            <li>Any additional documents required by the company.</li>
                        </ul>
                        <p>For claim inquiries, contact:</p>
                        <p>KPI Contact Center: 0 2624 1111<br>Monday - Friday, 08:00 – 18:00<br>Email: contact_kpi@kpi.co.th<br>Line ID: @kpicontactcenter (Monday - Friday, 08:30 – 16:30)</p>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        Exclusions
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        The company will not compensate for losses directly or indirectly caused by event cancellations by the event organizers.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingSix">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                        Cancellation of Ticket Shield Service
                    </button>
                </h2>
                <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <strong>Cancellation Procedure:</strong>
                        <ul>
                            <li>Prepare the following documents:
                                <ul>
                                    <li>Policy number</li>
                                    <li>Ticket number</li>
                                    <li>Concert name and performance time</li>
                                    <li>Phone number</li>
                                    <li>ID card of the ticket holder</li>
                                    <li>Bank account copy of the ticket holder</li>
                                </ul>
                            </li>
                            <li>Send an email to KPI Contact Center at contact_kpi@kpi.co.th to request cancellation of the Ticket Shield service.</li>
                        </ul>
                        <strong>Conditions:</strong>
                        <ul>
                            <li>The service can be canceled at least 14 days before the event starts.</li>
                            <li>Refunds will be made only via bank transfer.</li>
                        </ul>
                        
                    </div>
                    
                </div>
                
            </div>
            <div class="note">
                        <strong>Note:</strong> Claim rights are limited to 1 account per ticket only.
                    </div>
        </div>
    </div>

    
</body><?php require 'assets/partials/footer.php'; ?>
</html>
