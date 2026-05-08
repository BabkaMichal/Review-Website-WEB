<?php
/////////////////////////////////////////////////////////////
/////////// Šablona pro zobrazení registrační stránky ////////
/////////////////////////////////////////////////////////////

// Určení globálních proměnných, se kterými šablona pracuje
global $tplData;

// Připojení objektu pro výpis hlavičky a patičky HTML
require(DIRECTORY_VIEWS . "/TemplateBasics.class.php");
$tplHeaders = new TemplateBasics();

?>
<?php
// Hlavička
$tplHeaders->getHTMLHeader($tplData['title']);

// 1. Výpis případné chybové hlášky
if (isset($tplData['error_message']) && $tplData['error_message']) {
    echo '<div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="alert alert-danger text-center" role="alert">
                    ' . htmlspecialchars($tplData['error_message']) . '
                </div>
            </div>
          </div>';
}

// 2. Výpis případné informační (úspěšné) hlášky
if (isset($tplData['success_message']) && $tplData['success_message']) {
    echo '<div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="alert alert-success text-center" role="alert">
                    ' . htmlspecialchars($tplData['success_message']) . '
                </div>
            </div>
          </div>';
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        <div class="card shadow-sm">
            <div class="card-body p-4 p-md-5">
                <h2 class="card-title text-center mb-4">Registrace</h2>

                <form action="index.php?page=register" method="POST">

                    <input type="hidden" name="action" value="perform_registration">

                    <div class="mb-3">
                        <label for="regName" class="form-label">Uživatelské jméno</label>
                        <input type="text" class="form-control" id="regName" name="reg_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="regEmail" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="regEmail" name="reg_email" required>
                    </div>

                    <div class="mb-3">
                        <label for="regPassword" class="form-label">Heslo</label>
                        <input type="password" class="form-control" id="regPassword" name="reg_pass" required>
                    </div>

                    <div class="mb-4">
                        <label for="regPasswordCheck" class="form-label">Potvrzení hesla</label>
                        <input type="password" class="form-control" id="regPasswordCheck" name="reg_pass_check"
                               required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">Zaregistrovat se</button>
                    </div>

                </form>

            </div>
        </div>

        <div class="text-center mt-3">
            <p>Už máte účet? <a href="index.php?page=login">Přihlaste se</a></p>
        </div>

    </div>
</div>

<?php
// Patička
$tplHeaders->getHTMLFooter();
?>
