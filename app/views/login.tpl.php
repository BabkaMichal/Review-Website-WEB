<?php
/////////////////////////////////////////////////////////////
/////////// Sablona pro zobrazeni login stranky   ///////////
/////////////////////////////////////////////////////////////

//// vypis sablony
// urceni globalnich promennych, se kterymi sablona pracuje
global $tplData;

// pripojim objekt pro vypis hlavicky a paticky HTML
require(DIRECTORY_VIEWS . "/TemplateBasics.class.php");
$tplHeaders = new TemplateBasics();

?>
<?php
// hlavicka
$tplHeaders->getHTMLHeader($tplData['title']);

// 1. VÝPIS PŘÍPADNÉ CHYBOVÉ HLÁŠKY
// Controller může do $tplData['error_message'] vložit text chyby (např. "Špatné jméno nebo heslo")
if (isset($tplData['error_message']) && $tplData['error_message']) {
    // Použijeme Bootstrap 'alert' komponentu
    echo '<div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="alert alert-danger text-center" role="alert">
                    ' . htmlspecialchars($tplData['error_message']) . '
                </div>
            </div>
          </div>';
}
?>

    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5"><h2 class="card-title text-center mb-4">Přihlášení</h2>

                    <form action="index.php?page=login" method="POST">

                        <input type="hidden" name="action" value="perform_login">

                        <div class="mb-3">
                            <label for="loginName" class="form-label">Přihlašovací jméno</label>
                            <input type="text" class="form-control" id="loginName" name="login_name" required>
                        </div>

                        <div class="mb-4">
                            <label for="loginPassword" class="form-label">Heslo</label>
                            <input type="password" class="form-control" id="loginPassword" name="login_pass" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Přihlásit se</button>
                        </div>

                    </form>

                </div>
            </div>

            <div class="text-center mt-3">
                <p>Ještě nemáte účet? <a href="index.php?page=register">Zaregistrujte se</a></p>
            </div>

        </div>
    </div>


<?php
// paticka
$tplHeaders->getHTMLFooter();
?>