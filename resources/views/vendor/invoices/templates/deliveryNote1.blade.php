<!DOCTYPE html>
<html>
<head>
  <title>Delivery Note Template</title>
  <style>
    /* CSS styles for the document */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 800px;
      height: 984px;
      /* margin: 0 auto; */
      padding: 20px;
      border: 1px solid black;
    }

    .page {
      padding: 20px;
      background-color: #fff;
    }

    .header {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .address {
      margin-bottom: 20px;
    }

    .section {
      margin-bottom: 20px;
    }

    .section-header {
      font-weight: bold;
      margin-bottom: 10px;
    }

    .section-content {
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div style="float: left">
            @if($invoice->logo)
                <img src="{{ $invoice->getLogo() }}" alt="logo" height="100">
            @endif
        </div>

        <div style="float: right; padding-top: 20px;">
            <span style="float: right; color: #0E4681; font-size: 27px; padding-right: 20px;">
                <strong>Jobson Asia Pte Ltd</strong>
            </span>
            <p style="float: right; padding-top: 13px; padding-right: 20px;">Gul Circle 156A</p>
            <p style="float: right; padding-top: 30px; padding-right: 20px;">SINGAPORE</p>
            <p style="float: right; padding-top: 50px; padding-right: 20px;">629614</p>
            <p style="float: right; padding-top: 120px; padding-right: 20px; text-align: center;"><strong style="padding-right: 10px; text-align: center;">Nr. Comprovante</strong><input type="text" style="height: 15px; width: 130px;"></p>
        </div>

        <div style="float: left; padding-top: 200px;">
            <p style="float: left; padding-top: 30px; text-align: center;"><strong style="text-align: center; padding-right: 10px">Delivery Note / Job nr.</strong><input type="text" style="height: 15px; width: 130px;"></p>
            <p style="float: left; padding-top: 70px; text-align: center;"><strong style="text-align: center; padding-right: 80px">M/V</strong><input type="text" style="height: 15px; width: 330px;"></p>
            <p style="float: left; padding-top: 100px; text-align: center;"><strong style="text-align: center; padding-right: 80px">C/O</strong><input type="text" style="height: 15px; width: 330px;"></p>
            <p style="float: left; padding-top: 130px; text-align: center;"><strong style="text-align: center; padding-right: 30px">Reference</strong><input type="text" style="height: 15px; width: 330px;"></p>
        </div>

        <div style="padding-top: 400px">
            <input type="text" style="height: 20px; width: 160px; font-weight: bold; font-size: 20px;" value="Job Description">
        </div>

        <div style="padding-top: 300px">
            <input type="text" style="height: 20px; width: 100px; font-weight: bold; font-size: 20px;" value="Remarks">
            <br>
            <span style="font-size: 12px;"><strong>Limitation of liability:</strong>–
                Jobson Asia Pte Ltd can not under any circumstances be retained liable for damages to the assets including damages to products or parts repaired or maintained by the client. Under no circumstances shall Jobson Asia Pte Ltd be held liable for indirect damages such as the loss of gain and/or profit of the client for the non-usability of the equipment object of repairs and/or
                maintenance, or every other indirect consequence, contingent and/or accidental foreign or connected to the work carried out by Jobson
                Asia Pte Ltd with exception m
                ade in the case of fraud. Under no circumstances shall Jobson Asia Pte Ltd be considered liable for
                damages unforeseen by Jobson Asia Pte Ltd at the moment of the contract conclusion. Jobson Asia Pte Ltd shall not be held lia
                ble for
                damages deriving from work carried out by the client or third parties even if executed with the attendance of Jobson Asia Pte Ltd.
                Jobson Asia Pte Ltd shall be responsible for damages deriving directly from work carried out by their own employees or work c
                arried
                out by third parties subject to the supervision of Jobson personnel. In any case Jobson Asia Pte Ltd shall not be held liable for
                damages claimed after six months from the completion of the job (delivery notes undersigning). Under all circumstances the li
                ability
                of Jobson
                shall not exceed 1/3 of the price agreed upon in the contract.</span>
        </div>

        <div style="float: right;">
            <p style="float: right; padding-top: 30px; text-align: center;"><strong style="text-align: center; padding-right: 10px">Date</strong><input type="text" style="height: 15px; width: 130px;"></p>
            <p style="float: right; padding-top: 70px; text-align: center;"><strong style="text-align: center; padding-right: 80px">Place of Completion</strong><input type="text" style="height: 15px; width: 330px;"></p>
            <p style="float: right; padding-top: 100px; text-align: center;"><strong style="text-align: center; padding-right: 80px">Signed and Accepted By</strong><input type="text" style="height: 15px; width: 330px;"></p>
            <p style="float: right; padding-top: 130px; text-align: center;"><strong style="text-align: center; padding-right: 30px">Ship's Stamp</strong><input type="text" style="height: 15px; width: 330px;"></p>
        </div>

    </div>

    {{-- <div>
      <div class="header">Delivery Note / Job nr.</div>

      <div class="address">
        Jobson Asia Pte Ltd<br>
        Gul Circle 156A<br>
        SINGAPORE 629614<br>
        Nr. Comprovante
      </div>

      <div class="section">
        <div class="section-header">Reference</div>
        <div class="section-content">Job Description</div>
      </div>

      <div class="section">
        <div class="section-header">Remarks</div>
        <div class="section-content">
          Limitation of liability: - Jobson Asia Pte Ltd can not under any circumstances be retained liable for damages...
        </div>
      </div>

      <div class="section">
        <div class="section-header">Date</div>
        <div class="section-content">Place of Completion</div>
      </div>

      <div class="section">
        <div class="section-header">Signed And Accepted By</div>
        <div class="section-content">Ship's Stamp</div>
      </div>

      <div class="section">
        <div class="section-header">ALONGSIDE/LOADING PIER</div>
        <div class="section-content">
          ARRIVAL TIME: ____________<br>
          LIFTING TIME STAR: ________ END:________<br>
          REMARKS:
        </div>
      </div>
    </div> --}}
  </div>
</body>
</html>
