<?php


global $tplData;


require(DIRECTORY_VIEWS . "/TemplateBasics.class.php");
$tplHeaders = new TemplateBasics();

?>

<?php

$tplHeaders->getHTMLHeader($tplData['title']);


if (isset($tplData['delete'])) {
    echo "<div class='alert alert-info'>" . htmlspecialchars($tplData['delete']) . "</div>";
}

if (isset($tplData['message'])) {
    $message = $tplData['message'];
    $alertType = (strpos($message, 'CHYBA') === 0) ? 'alert-danger' : 'alert-success';
    echo "<div class='alert " . $alertType . "'>" . htmlspecialchars($message) . "</div>";
}

$currentAdminId = $_SESSION['user']['id'] ?? 0;

$res = "<div class='table-responsive'>";
$res .= "<table class='table table-striped table-hover'>";

$res .= "<thead class='thead-dark'>"
    . "<tr>"
    . "<th>ID</th>"
    . "<th>Login</th>"
    . "<th>E-mail</th>"
    . "<th>Role</th>"
    . "<th>Akce</th>"
    . "</tr>"
    . "</thead>";

$res .= "<tbody>";

foreach ($tplData['users'] as $u) {

    $id_safe = (int)$u['id_user'];
    $login_safe = htmlspecialchars($u['login']);
    $email_safe = htmlspecialchars($u['email']);
    $role_safe = htmlspecialchars($u['role']);

    $res .= "<tr>"
        . "<td>$id_safe</td>"
        . "<td>$login_safe</td>"
        . "<td>$email_safe</td>"
        . "<td><strong>$role_safe</strong></td>";

    $res .= "<td>";

    if ($id_safe == $currentAdminId) {
        $res .= "<i>(To jste vy)</i>";
    } else {

        $res .= "<form method='post' style='display: inline-block; margin-right: 5px;'>"
            . "<input type='hidden' name='id_user' value='$id_safe'>"
            // Přidáno potvrzení a hezčí třída
            . "<button type='submit' name='action' value='delete' class='btn btn-danger btn-sm' "
            . "onclick='return confirm(\"Opravdu chcete smazat uživatele $login_safe?\");'>"
            . "Smazat</button>"
            . "</form>";

        if ($u['role'] === 'user') {
            $res .= "<form method='post' style='display: inline-block;'>"
                . "<input type='hidden' name='id_user' value='$id_safe'>"
                . "<button type='submit' name='action' value='changeRole' class='btn btn-success btn-sm'>"
                . "Povýšit na Admina</button>"
                . "</form>";
        }
    }

    $res .= "</td></tr>";
}

$res .= "</tbody></table>";
$res .= "</div>";

echo $res;


$tplHeaders->getHTMLFooter()

?>
